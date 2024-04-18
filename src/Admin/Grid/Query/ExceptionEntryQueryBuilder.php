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

final class ExceptionEntryQueryBuilder extends AbstractDoctrineQueryBuilder
{
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
        string $databasePrefix,
        private DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $databasePrefix);
        $this->table = $databasePrefix . 'logviewer_exception';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $queryBuilder = $this
            ->getQueryBuilder()
            ->select('e.*')
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
            ->select('COUNT(e.id_entry)')
        ;

        return $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);
    }
    
    /**
     * Apply filters to exception query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function applyFilters(array $filters, QueryBuilder $qb): QueryBuilder
    {
        $allowedFilters = [
            'content',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            $qb
                ->andWhere("$filterName LIKE :$filterName")
                ->setParameter($filterName, '%'.$filterValue.'%');
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
        return $this->connection->createQueryBuilder()->from($this->table, 'e');
    }
}
