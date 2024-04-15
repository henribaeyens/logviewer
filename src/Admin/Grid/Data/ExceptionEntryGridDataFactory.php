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

class ExceptionEntryGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    protected $exceptionEntryDataFactory;

    /**
     * @param GridDataFactoryInterface $exceptionEntryDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $exceptionEntryDataFactory
    ) {
        $this->exceptionEntryDataFactory = $exceptionEntryDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->exceptionEntryDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($data->getRecords()->all()),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
