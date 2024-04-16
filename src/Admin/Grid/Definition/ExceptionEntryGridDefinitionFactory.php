<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\HtmlColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

final class ExceptionEntryGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'logviewer_grid_exception_entries';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Exceptions', [], 'Modules.Logviewer.Admin');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DateTimeColumn('date'))
                ->setName($this->trans('Date', [], 'Modules.Logviewer.Admin'))
                ->setOptions([
                    'field' => 'date',
                ])
            )
            ->add(
                (new HtmlColumn('content'))
                    ->setName($this->trans('Exception', [], 'Modules.Logviewer.Admin'))
                    ->setOptions([
                        'field' => 'content',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
            )
        ;

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('content', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('content')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'logviewer_exceptions',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SubmitGridAction('refresh_exceptions'))
                    ->setName($this->trans('Refresh exceptions', [], 'Modules.Logviewer.Action'))
                    ->setIcon('refresh')
                    ->setOptions([
                        'submit_route' => 'logviewer_exceptions_refresh',
                    ]
                )
            )
        ;
    }

}
