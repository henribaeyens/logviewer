<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Controller;

use PrestaShop\Module\Logviewer\Admin\Grid\Definition\ExceptionEntryGridDefinitionFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\Module\Logviewer\Admin\Search\Filters\ExceptionEntryFilters;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\ExceptionReaderInterface;

class LogviewerExceptionController extends FrameworkBundleAdminController
{
    public function __construct(
        private GridFactory $exceptionEntryGridFactory,
        private ExceptionReaderInterface $exceptionReader,
        private readonly string $currentEnvironment,
    ) {
    }
    
    /**
     * @AdminSecurity(
     *      "is_granted('read', request.get('_legacy_controller'))", message="Access denied."
     * )
     *
     * @param ExceptionEntryFilters $filters the list of filters from the request
     *
     * @return Response
     */
    public function indexAction(ExceptionEntryFilters $filters): Response
    {
        $grid = $this->exceptionEntryGridFactory->getGrid($filters);

        return $this->render('@Modules/logviewer/src/Admin/Resource/Views/Exceptions/list.html.twig', [
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
            $this->get('prestashop.module.logviewer.grid.definition.exception_entry'),
            $request,
            ExceptionEntryGridDefinitionFactory::GRID_ID,
            'logviewer_exceptions'
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
        $this->exceptionReader->process();

        return $this->redirectToRoute('logviewer_exceptions');
    }

}
