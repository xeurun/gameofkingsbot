<?php

namespace App\Helper;

class DateTimeHelper
{
    /**
     * Get hour
     */
    public static function hourBetween(
        ?\DateTimeInterface $left = null,
        ?\DateTimeInterface $right = null
    ): string {
        $left = $left ?? new \DateTime('now');
        $right = $right ?? new \DateTime('now');

        $diff = $left->diff($right);

        return $diff->h + ($diff->days * 24);
    }
}
