# SOP: Membuat Background Job

Panduan ini mengatur standar pembuatan Background Jobs (Queue) di Laravel untuk sistem ASN Dashboard v2. Aturan ini sangat penting untuk mencegah gangguan pada performa memori (cursor drift) maupun kegagalan proses secara masif pada data besar.

## ⚠️ PERINGATAN KRITIKAL SEBELUM MEMBUAT JOB
1. **Dilarang keras** menggunakan fungsi `chunk()`. Wajib gunakan **`chunkById(500, function() {...})`** saat melakukan iterasi row pada tabel berskala besar.
2. Kegagalan (error) pada pemrosesan satu baris data (*per-record*) **TIDAK BOLEH** menyebabkan seluruh job terhenti. Selalu bungkus eksekusi individual dengan blok `try-catch`.
3. Jika Job memanipulasi Batch Data, entitas Batch (seperti `ImportBatch`) **harus** dibuat sebelum job di-dispatch di controller, bukan dicreate di dalam konstruktor Job.

---

## 📝 Langkah Eksekusi (Checklist)

- [ ] **1. Buat Job Class**
  Jalankan perintah: `php artisan make:job NamaProsesJob`.
- [ ] **2. Implementasikan ShouldQueue**
  Pastikan class mengimplementasikan interface `Illuminate\Contracts\Queue\ShouldQueue`.
- [ ] **3. Injeksi Parameter**
  Kirimkan ID atau identifier objek melalui `__construct()`. Dilarang mengirim objek Model Eloquent besar secara penuh; cukup kirimkan ID-nya saja untuk mencegah payload raksasa di driver queue.
- [ ] **4. Terapkan Standar Iterasi & Error Handling**
  Gunakan `chunkById()` dan lindungi loop item dengan `try-catch`.

---

## 💻 Template Kode Standard Background Job

Gunakan template di bawah ini saat mendesain Job baru yang memproses banyak data:

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\TargetModel; // Sesuaikan

class ProsesMasalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $batchId; // Hanya terima integer ID, jangan Eloquent Object!

    /**
     * Create a new job instance.
     */
    public function __construct(int $batchId)
    {
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. WAJIB GUNAKAN chunkById, bukan chunk biasa
        TargetModel::where('batch_id', $this->batchId)
            ->where('is_processed', false)
            ->chunkById(500, function ($records) {
                
                // 2. Iterasi setiap rekaman secara individual
                foreach ($records as $record) {
                    
                    // 3. ISOLASI ERROR: Wajib menggunakan try-catch di dalam perulangan
                    try {
                        
                        // --- MASUKKAN LOGIKA BISNIS DI SINI ---
                        // Contoh: sinkronisasi atau generate file
                        // $service->prosesData($record);
                        
                        // Tandai berhasil
                        $record->update(['is_processed' => true, 'status' => 'success']);
                        
                    } catch (\Exception $e) {
                        
                        // 4. LOG ERROR: Catat error agar tidak hilang, tapi biarkan job berlanjut
                        Log::error("ProsesMasalJob Error pada Record ID {$record->id}: " . $e->getMessage());
                        
                        // Tandai gagal agar progress bar tidak menyangkut (stuck)
                        $record->update([
                            'is_processed' => true, 
                            'status' => 'failed',
                            'error_message' => $e->getMessage() // (Opsional) jika kolom tersedia
                        ]);
                    }
                }
            });
            
        // (Opsional) Update status master Batch ID menjadi 'completed' di sini
    }
}
```
