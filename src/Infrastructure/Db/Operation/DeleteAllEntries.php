<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Db\Operation;

use Db;

class DeleteAllEntries extends AbstractDbOperation
{
    public function execute(): bool
    {
        if ($this->checkEntity($this->definition)) {
            $dbInstance = Db::getInstance();

            $sql = '
                TRUNCATE `' . _DB_PREFIX_ . $this->definition['table'] . '`
            ';
    
            return $dbInstance->execute($sql);
        }
        return false;
    }

}
