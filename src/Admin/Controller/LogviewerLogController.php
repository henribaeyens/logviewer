<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Controller;

use Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShop\Module\Logviewer\Admin\Search\Filters\LogEntryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderInterface;
use PrestaShop\Module\Logviewer\Domain\Factory\ReadingStrategyFactoryInterface;
use PrestaShop\Module\Logviewer\Admin\Grid\Definition\LogEntryGridDefinitionFactory;

class LogviewerLogController extends FrameworkBundleAdminController
{
    /**
     * @param GridFactory $logEntryGridFactory
     * @param LogReaderInterface $logReader
     * @param ReadingStrategyFactoryInterface $readingStrategyFactory
     * @param string $currentEnvironment
     */
    public function __construct(
        private GridFactory $logEntryGridFactory,
        private LogReaderInterface $logReader,
        private ReadingStrategyFactoryInterface $readingStrategyFactory,
        private readonly string $currentEnvironment,
    ) {
    }
    
    /**
     * @AdminSecurity(
     *      "is_granted('read', request.get('_legacy_controller'))", message="Access denied."
     * )
     *
     * @param LogEntryFilters $filters the list of filters from the request
     *
     * @return Response
     */
    public function indexAction(LogEntryFilters $filters): Response
    {
        $filters->addFilter([
            'env' => $this->currentEnvironment,
        ]);

        $grid = $this->logEntryGridFactory->getGrid($filters);

        return $this->render('@Modules/logviewer/src/Admin/Resource/Views/Logs/list.html.twig', [
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => [],
            'layoutTitle' => $this->trans('Logs', 'Admin.Navigation.Menu'),
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'grid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))", message="Access denied.", redirectRoute="logviewer_logs"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request): RedirectResponse
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.logviewer.grid.definition.log_entry'),
            $request,
            LogEntryGridDefinitionFactory::GRID_ID,
            'logviewer_logs'
        );
    }

    /**
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))", message="Access denied.", redirectRoute="logviewer_logs"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function refreshAction(Request $request): RedirectResponse
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $readingStrategy = $this->readingStrategyFactory->create(Configuration::get('Logviewer_Strategy'), $this->currentEnvironment);
        $outcome = $this->logReader->process($readingStrategy);
        if (true !== $outcome) {
            $this->addFlash('error', $outcome);
        }

        return $this->redirectToRoute('logviewer_logs');
    }

}
