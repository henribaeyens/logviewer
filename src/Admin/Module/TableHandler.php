<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Module;

use Db;
use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Domain\Entity\ExceptionEntry;

final class TableHandler
{
    public function create(): bool
    {
        $result = true;

        $statements = [
          'logs' =>
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . LogEntry::TABLE . '` (
              `id_entry` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              `env` VARCHAR(6) NULL,
              `date` DATETIME NOT NULL,
              `context` VARCHAR(16) NULL,
              `level` VARCHAR(16) NULL,
              `content` TEXT NULL,
              PRIMARY KEY (`id_entry`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ';',
          'exceptions' =>
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . ExceptionEntry::TABLE . '` (
                `id_entry` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `content` TEXT NULL,
                PRIMARY KEY (`id_entry`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ';',
        ];

        foreach ($statements as $statement) {
            $result = $result && Db::getInstance()->execute($statement);
        }

        return $result;
    }

    public function drop(): bool
    {
        $result = true;

        $statements = [
          'logs' =>
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . LogEntry::TABLE . '`;',
          'exceptions' =>
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . ExceptionEntry::TABLE . '`;',
        ];

        foreach ($statements as $statement) {
            $result = $result && Db::getInstance()->execute($statement);
        }

        return $result;
    }

}
