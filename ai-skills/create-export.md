# SOP: Membuat Export Baru (Excel/PDF)

Panduan untuk mengimplementasikan fitur Export menggunakan *package* `Maatwebsite/Laravel Excel`. Proyek ini mengharuskan format dan penggunaan *interfaces* yang seragam.

## 📝 Langkah Eksekusi (Checklist)

- [ ] **1. Buat File Export Class**
  Buat file baru di direktori `app/Exports/` (contoh: `app/Exports/LaporanPegawaiExport.php`).
- [ ] **2. Implementasikan Interface Wajib**
  Pastikan class mengimplementasikan interface dasar berikut:
  - `FromCollection` atau `FromQuery`: Sumber data.
  - `WithHeadings`: Judul/header kolom Excel.
  - `WithMapping`: Memformat relasi atau modifikasi data per baris.
  - `ShouldAutoSize`: Melebarkan kolom secara dinamis.
- [ ] **3. Panggil dari Controller**
  Gunakan Facade `Excel::download()` di dalam method controller.

---

## 💻 Template Kode Standard Export

Gunakan kerangka kode di bawah ini sebagai pondasi pembuatan export baru:

```php
<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExportTemplate implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;

    /**
     * Dependency injection untuk filter atau pengiriman data dari controller
     */
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * Deklarasikan header baris pertama
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Lengkap',
            'Jabatan',
            'OPD'
        ];
    }

    /**
     * Mapping data dari collection ke format baris Excel
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Variabel static untuk increment nomor (opsional)
        static $no = 0;
        $no++;

        return [
            $no,
            $row->nip_baru,
            $row->nama_lengkap, // Contoh pemakaian accessor
            $row->jabatan->nama ?? '-', // Penanganan null-safe pada relasi
            $row->unor->nama ?? '-'
        ];
    }
}
```

## 🚀 Penggunaan di Controller

```php
use App\Exports\LaporanExportTemplate;
use Maatwebsite\Excel\Facades\Excel;

public function exportExcel()
{
    $data = Model::with(['jabatan', 'unor'])->get();
    
    // Download format Excel
    return Excel::download(new LaporanExportTemplate($data), 'Laporan_Pegawai.xlsx');
}

public function exportPdf()
{
    $data = Model::with(['jabatan', 'unor'])->get();
    
    // Download format PDF (Harus dikonfigurasi MPDF wrapper di config jika mengikuti standar)
    return Excel::download(new LaporanExportTemplate($data), 'Laporan_Pegawai.pdf', \Maatwebsite\Excel\Excel::MPDF);
}
```
