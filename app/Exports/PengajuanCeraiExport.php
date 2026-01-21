<?php

namespace App\Exports;

use App\Models\PengajuanCerai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanCeraiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $query = PengajuanCerai::query();
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('tanggal_surat', [$this->start_date, $this->end_date]);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Jabatan',
            'Tanggal Surat',
            'Jenis Pengajuan',
            'Unit Kerja',
            'OPD',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        return [
            $row->nip,
            $row->nama,
            $row->jabatan,
            $row->tanggal_surat->format('Y-m-d'),
            $row->jenis_pengajuan,
            $row->unit_kerja,
            $row->opd,
            $row->keterangan,
        ];
    }
}
