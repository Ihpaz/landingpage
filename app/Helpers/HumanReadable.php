<?php

namespace App\Helpers;

class HumanReadable
{
    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function numberToHuman($number)
    {
        $units = ['', 'K', 'M', 'B'];

        for ($i = 0; $number > 1000; $i++) {
            $number /= 1000;
        }

        return round($number, 0) . $units[$i];
    }

    public static function priceToHuman($number)
    {
        $units = ['', 'K', 'Jt', 'M', 'T', 'B'];

        for ($i = 0; $number > 1000; $i++) {
            $number /= 1000;
        }

        return round($number, 1) . $units[$i];
    }
}
