<?php

namespace App\Helper;

class CurrencyHelper
{
    public static function costFormat($value)
    {
        if ($value > 1000) {
            $x = round($value);
            $xNumberFormat = number_format($x);
            $xArray = explode(',', $xNumberFormat);
            $xParts = array('k', 'm', 'b', 't');
            $x_count_parts = \count($xArray) - 1;
            $xDisplay = $xArray[0] . ((int) $xArray[1][0] !== 0 ? '.' . $xArray[1][0] : '');
            $xDisplay .= $xParts[$x_count_parts - 1];

            return $xDisplay;

        }

        return $value;
    }
}