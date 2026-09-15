<?php

namespace App\Helpers;

class GolonganHelper
{
    public static function parseRoman(?string $golonganKey): float
    {
        if (empty($golonganKey)) {
            return 99;
        }

        $romanOrder = [
            'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6,
            'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12
        ];

        $parts = explode('/', $golonganKey);
        $base = trim($parts[0]);
        $baseValue = $romanOrder[$base] ?? 99;
        $subValue = 0;
        
        if (isset($parts[1])) {
            $subValue = ord(strtolower($parts[1])) / 1000;
        }

        return $baseValue + $subValue;
    }
}
