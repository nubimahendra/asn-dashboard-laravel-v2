<?php

namespace App\Helpers;

use App\Models\RefUnor;
use Illuminate\Support\Collection;

class UptFilterHelper
{
    /**
     * Konfigurasi mapping antara nama OPD dan pattern UPT-nya.
     * Kunci adalah nama OPD (dicocokkan secara case-insensitive)
     * Value adalah daftar kategori UPT beserta pattern SQL (LIKE)
     */
    const UPT_CONFIG = [
        'Dinas Pendidikan' => [
            ['label' => 'UPT SD', 'pattern' => 'UPT SD%'],
            ['label' => 'UPT SMP', 'pattern' => 'UPT SMP%'],
        ],
        'Dinas Kesehatan' => [
            ['label' => 'UPT Puskesmas', 'pattern' => 'UPT Puskesmas%'],
        ],
    ];

    /**
     * Cari apakah nama OPD ada di dalam config (case-insensitive)
     *
     * @param string|null $opdName
     * @return string|null Mengembalikan key asli jika cocok, atau null
     */
    public static function matchOpdKey(?string $opdName): ?string
    {
        if (empty($opdName)) {
            return null;
        }

        $opdNameLower = strtolower(trim($opdName));
        foreach (array_keys(self::UPT_CONFIG) as $key) {
            if (strtolower($key) === $opdNameLower) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Cek apakah suatu OPD memiliki filter khusus UPT
     */
    public static function hasUptFilter(?string $opdName): bool
    {
        return self::matchOpdKey($opdName) !== null;
    }

    /**
     * Mengambil daftar UPT yang dikelompokkan berdasarkan kategori
     *
     * @param string $opdName
     * @return array Array of categories containing specific UPT names
     */
    public static function getUptListGrouped(string $opdName): array
    {
        $matchedKey = self::matchOpdKey($opdName);
        if (!$matchedKey) {
            return [];
        }

        $config = self::UPT_CONFIG[$matchedKey];
        $grouped = [];

        foreach ($config as $cat) {
            $items = RefUnor::where('nama_opd', 'LIKE', $cat['pattern'])
                ->select('nama_opd')
                ->distinct()
                ->orderBy('nama_opd')
                ->pluck('nama_opd');

            // Hanya masukkan ke grup jika ada data
            if ($items->count() > 0) {
                $grouped[$cat['label']] = [
                    'pattern' => $cat['pattern'],
                    'items' => $items
                ];
            }
        }

        return $grouped;
    }

    /**
     * Memproses nilai filter UPT menjadi kriteria query
     * 
     * @param string $uptValue (Bisa "__cat:UPT SD" atau "UPT SD Negeri 1")
     * @return array [column, operator, value]
     */
    public static function resolveUptFilter(string $uptValue): array
    {
        if (str_starts_with($uptValue, '__cat:')) {
            // Filter kategori UPT (contoh: Semua UPT SD)
            $catLabel = substr($uptValue, 6);
            
            // Cari pattern dari config
            $pattern = "{$catLabel}%"; // Default fallback
            foreach (self::UPT_CONFIG as $opd => $categories) {
                foreach ($categories as $cat) {
                    if ($cat['label'] === $catLabel) {
                        $pattern = $cat['pattern'];
                        break 2;
                    }
                }
            }
            
            return [
                'column' => 'nama_opd',
                'operator' => 'LIKE',
                'value' => $pattern
            ];
        }

        // Filter individual UPT
        return [
            'column' => 'nama_opd',
            'operator' => '=',
            'value' => $uptValue
        ];
    }
}
