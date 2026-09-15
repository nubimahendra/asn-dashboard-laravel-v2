# Implementation Fix Plan

## Project: ASN Dashboard Laravel v2

---

## 🔴 Critical Bugs (Must Fix)

### 1. Missing `gol_akhir` in `$fillable` — Mass Assignment Exception

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Models/Pegawai.php:36-71` |
| **Severity** | Critical |
| **Impact** | Pegawai import/update silently fails with MassAssignmentException |
| **Root Cause** | `gol_akhir` column exists in DB (migration `2026_05_19_024607_add_gol_akhir_to_pegawai_table.php`) but is missing from `$fillable` array |
| **Evidence** | `PegawaiImportService.php:116` assigns `'gol_akhir' => $staging->gol_akhir` |

**Steps to fix:**
1. Add `'gol_akhir'` to `protected $fillable` array in `app/Models/Pegawai.php` after `'golongan_id'`

---

### 2. Duplicate Route Registration

| Attribute | Detail |
|-----------|--------|
| **File** | `routes/api.php:12-13` |
| **Severity** | Medium |
| **Impact** | Dead code, no functional harm but confusing |

**Steps to fix:**
1. Remove line 13 duplicate: `Route::get('/chat/history', [App\Http\Controllers\ChatController::class, 'history']);`

---

### 3. `singleOverride` Creates Raw Request Object

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/RekonIuranController.php:195-197` |
| **Severity** | High |
| **Impact** | Bypasses validation middleware; fragile architecture |

**Steps to fix:**
1. Extract shared logic from `bulkOverride` into a private method
2. Have both `bulkOverride` and `singleOverride` call the shared method directly instead of wrapping via `new Request`

---

### 4. Both PNS+PPPK Filter Missing Active ID Set

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/RekonIuranController.php:65-70` and `IuranKorpriController.php:155-157` |
| **Severity** | High |
| **Impact** | May include ineligible employees in iuran calculations |

**Steps to fix:**
1. When both PNS and PPPK are selected, use the known active ID set instead of just excluding `101`
2. Use: `whereIn('kedudukan_hukum_id', ['01','02','03','04','15','71','73'])` (or reference `Pegawai::ACTIVE_KEDUDUKAN_HUKUM`)
3. Also ensure `status_cpns_pns` filter is applied

---

### 5. Unused Method `generateIuran` with No Route

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/IuranKorpriController.php:14-22` |
| **Severity** | Low |
| **Impact** | Dead code |

**Steps to fix:**
1. Either add a route pointing to it, or remove the method

---

## 🟡 Significant Issues (Should Fix)

### 6. Raw Superglobals in Controller

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/PegawaiImportController.php:186-217` |
| **Severity** | High |
| **Impact** | Bypasses Laravel's Request abstraction; key `$_FILES['file']` may not exist (input name is `files` plural) |

**Steps to fix:**
1. Remove all raw `$_FILES`, `$_POST`, `$_SERVER` references
2. Use `$request->file()` and `$request->all()` instead
3. Leverage Laravel's built-in validation messages for upload errors

---

### 7. Ambiguous `orWhereHas` in Search Queries

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/KelasJabatanController.php:18-23` and `JabatanMappingController.php:18-23` |
| **Severity** | High |
| **Impact** | Search returns incorrect results — matches all records when unor name is searched |

**Steps to fix:**
1. Wrap both `whereHas` + `orWhereHas` inside a single `where(function($q) { ... })` closure
2. Example:
```php
$query->where(function ($q) use ($search) {
    $q->whereHas('jabatan', fn($q) => $q->where('nama', 'like', "%{$search}%"))
      ->orWhereHas('unor', fn($q) => $q->where('nama_opd', 'like', "%{$search}%")->orWhere('nama', 'like', "%{$search}%"));
});
```

---

### 8. Zero-Division Risk in Breakdown Count

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/IuranKorpriController.php:309` |
| **Severity** | Medium |
| **Impact** | Stored `count` for archived iuran rekap is inaccurate when structural employees exist in same golongan |

**Steps to fix:**
1. Track actual per-golongan counts during `calculateRealtime()` rather than computing retroactively
2. Store the count directly in `rekap_iuran_bulanan` during `simpanRekap`

---

### 9. Hardcoded `'IV/b'` Eselon Fallback

| Attribute | Detail |
|-----------|--------|
| **Files** | `MariDashboardController.php:65`, `IuranKorpriController.php:199`, `IuranKorpriController.php:423` |
| **Severity** | Medium |
| **Impact** | Unmapped jabatan silently defaults to a specific eselon, potentially charging wrong iuran amount |

**Steps to fix:**
1. Extract fallback value to a configuration constant
2. Log a warning when fallback is used
3. Consider using `$pegawai->gol_akhir` as fallback instead of hardcoded eselon

---

### 10. Redundant Duplicate DB Query

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/MariDashboardController.php:56,62` |
| **Severity** | Low |
| **Impact** | Performance: fires same Eloquent relationship twice |

**Steps to fix:**
1. Remove line 56 (`$override = $pegawai->iuranOverride;`) — keep only the one at line 62

---

### 11. `tap()` No-Op in `anomalyDetails`

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/PegawaiImportController.php:471-476` |
| **Severity** | Low |
| **Impact** | No functional harm, `map()` on paginator may throw unexpected errors |

**Steps to fix:**
1. Replace with direct `paginate()`:
```php
$anomalies = StgPegawaiImport::where(/* ... */)->paginate(15);
```

---

### 12. Failed Records Marked Processed — Retry Won't Work

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Jobs/ProcessPegawaiImport.php:80-83`, `PegawaiImportController.php:732-782` |
| **Severity** | Medium |
| **Impact** | Retry button in UI validates records but the import job will skip them |

**Steps to fix:**
1. In `retry()` method (line 732), also reset `is_processed` to `false` and `processed_at` to `null` for records being retried
2. Or modify the job to include records with `processing_error` not null even if `is_processed` is true

---

### 13. Empty Staging File Check Returns Wrong HTTP Status

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/PegawaiImportController.php:290-296` |
| **Severity** | Low |
| **Impact** | Returns 422 (validation error) instead of 400 (bad request) |

**Steps to fix:**
1. Change response status from 422 to 400
2. Or handle this case with a more descriptive message before the diff analysis runs

---

## 🔵 Recommendations (Should Consider)

### 14. Add `sidawai` DB Credentials to `.env`

| Attribute | Detail |
|-----------|--------|
| **File** | `.env` |
| **Impact** | Remote sync connection falls back to localhost defaults |

**Action:**
- Add `DB_REMOTE_HOST`, `DB_REMOTE_PORT`, `DB_REMOTE_DATABASE`, `DB_REMOTE_USERNAME`, `DB_REMOTE_PASSWORD` to `.env`
- Verify the connection string in `config/database.php`

---

### 15. Extract Roman Numeral Sorting to Shared Helper

| Attribute | Detail |
|-----------|--------|
| **Files** | `DashboardController.php:188-216`, `IuranKorpriController.php:43-49` |
| **Impact** | Logic duplicated in two places |

**Action:**
- Extract to `app/Helpers/GolonganHelper.php` or a dedicated service:
```php
class GolonganHelper {
    public static function sortByRoman(array $data): array { ... }
    public static function parseRoman(string $golonganKey): float { ... }
}
```

---

### 16. Document Queue Worker Requirement

| Attribute | Detail |
|-----------|--------|
| **Reason** | `QUEUE_CONNECTION=database` and job dispatched in `confirmSync` |

**Action:**
- Add to README: `php artisan queue:work --timeout=600 --sleep=3 --tries=3`
- Or add npm script: `"queue": "php artisan queue:work --timeout=600 --sleep=3 --tries=3"`

---

### 17. Clean Up Orphan `softDeletes()` Column

| Attribute | Detail |
|-----------|--------|
| **File** | `database/migrations/2026_02_05_130339_create_pegawai_table.php:88` |
| **Impact** | DB has unused `deleted_at` column |

**Action:**
- Add a migration to drop the `deleted_at` column
- Or add a comment explaining that it's unused (superceded by `kedudukan_hukum_id = 17`)

---

### 18. Add Date Casting to `SnapshotPegawai`

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Models/SnapshotPegawai.php:30-31` |

**Action:**
- Add `'tgl_lahir' => 'date'` to the `$casts` array

---

### 19. Log Skipped Employees in Iuran Generator

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Services/IuranKorpriGeneratorService.php:31-33` |

**Action:**
- Add a warning log when employee is skipped due to missing `riwayatJabatanAktif`:
```php
if (!$riwayat) {
    Log::warning("Employee ID {$pegawai->id} skipped: no active jabatan riwayat");
    continue;
}
```

---

### 20. Add `source_file` Tracking to `confirmSync`

| Attribute | Detail |
|-----------|--------|
| **File** | `app/Http/Controllers/PegawaiImportController.php:617-639` |

**Action:**
- The `confirmSync` method dispatches the job but doesn't update the batch status from 'ready' to 'processing'. Add:
```php
$batch = ImportBatch::where('source_file', $filename)->first();
if ($batch) $batch->update(['status' => 'processing']);
```

---

## Implementation Priority

| Priority | Items |
|----------|-------|
| **P0 — Immediate** | #1, #7 (breaks core functionality) |
| **P1 — High** | #3, #4, #6 (data integrity/security) |
| **P2 — Medium** | #2, #8, #9, #12, #14 |
| **P3 — Low** | #5, #10, #11, #13, #15, #16, #17, #18, #19, #20 |
