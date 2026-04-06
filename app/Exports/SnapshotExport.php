<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Models\HistoryPegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SnapshotExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $filterMonth;
    protected $search;

    public function __construct($filterMonth = null, $search = null)
    {
        $this->filterMonth = $filterMonth;
        $this->search = $search;
    }

    public function collection()
    {
        if ($this->filterMonth) {
            $query = HistoryPegawai::where('periode', $this->filterMonth)
                ->with(['unor', 'jabatan', 'golongan']);
                
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip_baru', 'like', '%' . $this->search . '%');
                });
            }
            return $query->get();
        } else {
            // Live data
            $query = Pegawai::with(['unor', 'jabatanBaru', 'golongan', 'pangkat']);
            
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip_baru', 'like', '%' . $this->search . '%');
                });
            }
            
            return $query->get()->map(function ($p) {
                return (object) [
                    'nip_baru' => $p->nip_baru,
                    'nama' => $p->nama,
                    'tempat_lahir' => $p->tempat_lahir,
                    'tanggal_lahir' => $p->tanggal_lahir,
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'agama' => $p->agama,
                    'status_kawin' => $p->status_kawin,
                    'golongan_nama' => $p->golongan ? $p->golongan->nama : '-',
                    'jabatan_nama' => $p->jabatanBaru ? $p->jabatanBaru->nama : '-',
                    'jenis_jabatan_nama' => $p->jenis_jabatan,
                    'unor_nama' => $p->unor ? $p->unor->nama : '-',
                    'pendidikan_terakhir' => $p->pendidikan_terakhir,
                    'tingkat_pendidikan' => $p->tingkat_pendidikan,
                    'status_asn' => 'PNS', // Wait, the UI hardcodes status_asn. Let's assume PNS for now, or use the field if available.
                    'tmt_cpns' => $p->tmt_cpns,
                    'tmt_pns' => $p->tmt_pns,
                ];
            });
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama',
            'Tempat, Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Status Kawin',
            'Golongan',
            'Jabatan',
            'Jenis Jabatan',
            'Unit Kerja',
            'Pendidikan',
            'Tingkat Pendidikan',
            'TMT CPNS',
            'TMT PNS',
        ];
    }

    private $row = 0;

    public function map($row): array
    {
        $this->row++;
        
        $ttl = ($row->tempat_lahir ?? '') . ', ' . ($row->tanggal_lahir ? date('d-m-Y', strtotime($row->tanggal_lahir)) : '');
        $jk = $row->jenis_kelamin === 'L' ? 'Laki-laki' : ($row->jenis_kelamin === 'P' ? 'Perempuan' : $row->jenis_kelamin);

        return [
            $this->row,
            "'" . $row->nip_baru, // Force text so it's not converted to scientific notation
            $row->nama,
            $ttl,
            $jk,
            $row->agama,
            $row->status_kawin,
            $row->golongan_nama,
            $row->jabatan_nama,
            $row->jenis_jabatan_nama,
            $row->unor_nama,
            $row->pendidikan_terakhir,
            $row->tingkat_pendidikan,
            $row->tmt_cpns ? date('d-m-Y', strtotime($row->tmt_cpns)) : '-',
            $row->tmt_pns ? date('d-m-Y', strtotime($row->tmt_pns)) : '-',
        ];
    }

    public function title(): string
    {
        return 'Data Pegawai';
    }
}
