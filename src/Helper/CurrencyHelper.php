<?php

namespace App\Helper;

class CurrencyHelper
{
    /**
     * @param $value
     */
    public static function costFormat($value): string
    {
        if ($value >= 1000 || $value <= -1000) {
            $x = round($value);
            $xNumberFormat = number_format($x);
            $xArray = explode(',', $xNumberFormat);
            $xParts = ['k', 'm', 'b', 't'];
            $xCountParts = \count($xArray) - 1;
            $xDisplay = $xArray[0] . (0 !== (int)$xArray[1][0] ? '.' . $xArray[1][0] : '');
            $xDisplay .= $xParts[$xCountParts - 1];

            return $xDisplay;
        }

        return $value;
    }
}
