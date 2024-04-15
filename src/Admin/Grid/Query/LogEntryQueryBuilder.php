<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;

final class LogEntryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicator
     */
    private $searchCriteriaApplicator;

    /**
     * @var string Table name
     */
    private $table;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        $databasePrefix,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $databasePrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->table = $databasePrefix . 'logviewer_log';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $queryBuilder = $this
            ->getQueryBuilder()
            ->select('l.*')
        ;

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $queryBuilder)
            ->applySorting($searchCriteria, $queryBuilder);

        return $queryBuilder;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $queryBuilder = $this
            ->getQueryBuilder()
            ->select('COUNT(l.id_entry)')
        ;

        return $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);
    }
    
    /**
     * Apply filters to log query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function applyFilters(array $filters, QueryBuilder $qb): QueryBuilder
    {
        $allowedFilters = [
            'env',
            'context',
            'level',
            'content',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['env', 'context', 'level'])) {
                $qb
                    ->andWhere("$filterName = :$filterName")
                    ->setParameter($filterName, $filterValue);
            } else {
                $qb
                    ->andWhere("$filterName LIKE :$filterName")
                    ->setParameter($filterName, '%'.$filterValue.'%');
            }
        }

        return $qb;
    }
    
    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from($this->table, 'l');
    }
}
