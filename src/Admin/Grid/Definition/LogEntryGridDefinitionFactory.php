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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\Module\Logviewer\Admin\Grid\Column\LogLevelColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\Module\Logviewer\Admin\Form\Type\LogLevelChoiceType;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\Module\Logviewer\Admin\Form\Type\LogContextChoiceType;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

final class LogEntryGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'logviewer_grid_log_entries';

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
        return $this->trans('Logs', [], 'Modules.Logviewer.Admin');
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
                (new DataColumn('context'))
                    ->setName($this->trans('Context', [], 'Modules.Logviewer.Admin'))
                    ->setOptions([
                        'field' => 'context',
                    ])
            )
            ->add(
                (new LogLevelColumn('level'))
                    ->setName($this->trans('Level', [], 'Modules.Logviewer.Admin'))
                    ->setOptions([
                        'field' => 'level',
                    ])
            )
            ->add(
                (new DataColumn('content'))
                    ->setName($this->trans('Message', [], 'Modules.Logviewer.Admin'))
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
                (new Filter('context', LogContextChoiceType::class))
                    ->setAssociatedColumn('context')
            )
            ->add(
                (new Filter('level', LogLevelChoiceType::class))
                    ->setAssociatedColumn('level')
            )
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
                        'redirect_route' => 'logviewer_logs',
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
                (new SubmitGridAction('refresh_logs'))
                    ->setName($this->trans('Refresh logs', [], 'Modules.Logviewer.Action'))
                    ->setIcon('refresh')
                    ->setOptions([
                        'submit_route' => 'logviewer_logs_refresh',
                    ]
                )
            )
        ;
    }

}
