<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

 namespace PrestaShop\Module\Logviewer\Infrastructure\Db\Operation;

use Db;

class BulkInsert extends AbstractDbOperation
{
    public function execute(array $rows): bool
    {
        if (empty($rows)) {
            return false;
        }

        $dbInstance = Db::getInstance();

        $sql = '
            INSERT INTO `' . _DB_PREFIX_ . $this->definition['table'] . '`
            (`' . implode('`, `', array_keys($this->definition['fields'])). '`) VALUES 
        ';
        $sql .= implode(',', $rows);
        $sql .= ';';
        
        return $dbInstance->execute($sql);
    }

}
