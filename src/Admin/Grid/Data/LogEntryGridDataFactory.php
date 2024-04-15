<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;

class LogEntryGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    protected $logEntryDataFactory;

    /**
     * @param GridDataFactoryInterface $logEntryDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $logEntryDataFactory
    ) {
        $this->logEntryDataFactory = $logEntryDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->logEntryDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($data->getRecords()->all()),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
