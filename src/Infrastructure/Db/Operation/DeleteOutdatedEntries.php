<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

 namespace PrestaShop\Module\Logviewer\Infrastructure\Db\Operation;

use Db;

class DeleteOutdatedEntries extends AbstractDbOperation
{
    public function execute(array $dates): bool
    {
        $dbInstance = Db::getInstance();

        $sql = '
            DELETE FROM `' . _DB_PREFIX_ . $this->definition['table'] . '`
            WHERE DATE(`date`) NOT IN ("' . implode('", "', $dates) . '");
        ';

        return $dbInstance->execute($sql);
    }

}
