<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Enum;

enum LogContext: string
{
    use EnumToArray;
    
    case app = 'app';
    case console = 'console';
    case doctrine = 'doctrine';
    case php = 'php';
    case request = 'request';
    case security = 'security';
}
