<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Enum;

enum LogLevel: string
{
    use EnumToArray;
    
    case INFO = 'INFO';
    case DEBUG = 'DEBUG';
    case WARNING = 'WARNING';
    case CRITICAL = 'CRITICAL';

}
