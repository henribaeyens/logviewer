<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Db\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\Module\Logviewer\Domain\Entity\ExceptionEntry;
use PrestaShop\Module\Logviewer\Domain\Repository\ExceptionEntryRepositoryInterface;

final class ExceptionEntryRepository implements ExceptionEntryRepositoryInterface
{
    /**
     * @var string Table name
     */
    private $table;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(
        private Connection $connection,
        string $databasePrefix,
    ) {
        $this->table = $databasePrefix . ExceptionEntry::TABLE;
    }

    public function countByDate(string $date): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getBaseQuery();
        $qb
            ->select('COUNT(e.id_entry)')
            ->where('DATE(`date`) = :date')
            ->setParameter('date', $date)
        ;
    
        $row = $qb->execute()->fetchFirstColumn();
        return (int) $row[0];
    }

    /**
     * Delete all entries.
     *
     * @return int the number of affected rows
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteAll(): int
    {
        $platform = $this->connection->getDatabasePlatform();

        return $this->connection->executeStatement($platform->getTruncateTableSQL($this->table, true));
    }

    private function getBaseQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from($this->table, 'e');
    }
}
