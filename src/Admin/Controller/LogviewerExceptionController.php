<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\Module\Logviewer\Admin\Search\Filters\ExceptionEntryFilters;
use PrestaShop\Module\Logviewer\Admin\Service\Reader\ExceptionReaderInterface;

class LogviewerExceptionController extends FrameworkBundleAdminController
{
    /**
     * @var GridFactory
     */
    private $exceptionEntryGridFactory;

    /**
     * @var ExceptionReaderInterface
     */
    private $exceptionReader;

    /**
     * @var string
     */
    private $currentEnvironment;

    public function __construct(
        GridFactory $exceptionEntryGridFactory,
        ExceptionReaderInterface $exceptionReader,
        string $currentEnvironment
    ) {
        $this->exceptionEntryGridFactory = $exceptionEntryGridFactory;
        $this->exceptionReader = $exceptionReader;
        $this->currentEnvironment = $currentEnvironment;
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
    public function refreshAction(Request $request): RedirectResponse
    {
        $this->exceptionReader->read();

        return $this->redirectToRoute('logviewer_exceptions');
    }

}
