<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Search\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\Module\Logviewer\Admin\Grid\Definition\ExceptionEntryGridDefinitionFactory;

final class ExceptionEntryFilters extends Filters
{
    /** @var string */
    protected $filterId = ExceptionEntryGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => static::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_entry',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
