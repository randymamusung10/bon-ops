<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Parse a formatted number (like Rp 1.000.000,50 or 1000000.50) safely to float.
     *
     * @param mixed $value
     * @return float
     */
    public static function parse($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (!is_string($value)) {
            return (float) $value;
        }

        // Clean currency prefix and whitespace
        $clean = str_replace(['Rp', ' '], '', $value);

        // If it contains a comma, it's definitely Indonesian format (e.g. 1.234,56 or 12,5)
        if (strpos($clean, ',') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } else {
            // No comma, check dots.
            // If it contains multiple dots, it is definitely using dots as thousands separators.
            if (substr_count($clean, '.') > 1) {
                $clean = str_replace('.', '', $clean);
            } 
            // If it contains a single dot followed by exactly 3 digits at the end of the string,
            // it is a thousands separator in Indonesian format (e.g., "1.000" or "12.500")
            elseif (preg_match('/\.\d{3}$/', $clean)) {
                $clean = str_replace('.', '', $clean);
            }
        }

        return (float) $clean;
    }
}
