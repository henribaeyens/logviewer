<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Tool;

use DateTime;
use DateInterval;

final class Tools
{
    public static function getDates(int $days): array
    {
        $toDay = new DateTime();
        $dates = [];
    
        $dates[] = $toDay->format('Y-m-d');
        foreach (range(1, $days - 1) as $d) {
            $dates[] = $toDay->sub(new DateInterval("P1D"))->format('Y-m-d');
        }
        return array_reverse($dates);
    }
}
