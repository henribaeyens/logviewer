<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Enum;

trait EnumToArray
{
    public static function names(): array
    {
      return array_column(self::cases(), 'name');
    }
  
    public static function values(): array
    {
      return array_column(self::cases(), 'value');
    }
  
    public static function array(): array
    {
      return array_combine(self::values(), self::names());
    }
  
}
