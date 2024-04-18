<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Controller;

use Configuration;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\Module\Logviewer\Admin\Form\Type\ConfigurationFormType;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;
use PrestaShop\Module\Logviewer\Domain\Repository\ExceptionEntryRepositoryInterface;

class LogviewerConfigurationController extends FrameworkBundleAdminController
{
    /**
     * @param LogEntryRepositoryInterface $logEntryRepository
     * @param ExceptionEntryRepositoryInterface $exceptionEntryRepository
     * @param string $currentEnvironment
     */
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
        private ExceptionEntryRepositoryInterface $exceptionEntryRepository,
        private readonly string $currentEnvironment,
    ) {
    }

    public function indexAction(Request $request)
    {
        $configurationForm = $this->createForm(
            ConfigurationFormType::class,
            $this->getConfigurationData(),
            [
                'max_days' => 5
            ]
        );

        $configurationForm->handleRequest($request);

        if ($configurationForm->isSubmitted() && $configurationForm->isValid()) {
            $resultHandleForm = $this->handleForm($configurationForm->getData());
            if ($resultHandleForm) {
                return $this->redirectToRoute('logviewer_configuration');
            }
        }

        return $this->render(
            '@Modules/Logviewer/src/Admin/Resource/Views/Configuration/form.html.twig',
            [
                'configurationForm' => $configurationForm->createView(),
            ]
        );
    }

    /**
     * handleForm
     *
     * @param array $data
     *
     * @return bool
     */
    private function handleForm($data): bool
    {
        $result = true;

        Configuration::updateValue('Logviewer_History_Days', $data['Logviewer_History_Days']);

        if (!empty($data['Logviewer_Log_Contexts'])) {
            Configuration::updateValue('Logviewer_Log_Contexts', implode('|', $data['Logviewer_Log_Contexts']));
        }
        if (!empty($data['Logviewer_Log_Levels'])) {
            Configuration::updateValue('Logviewer_Log_Levels', implode('|', $data['Logviewer_Log_Levels']));
        }

        $this->logEntryRepository->deleteAll();
        $this->exceptionEntryRepository->deleteAll();

        Configuration::updateValue('Logviewer_Last_Line_Read_' . ucfirst($this->currentEnvironment), 0);
        Configuration::updateValue('Logviewer_Last_Date_Read', 0);

        if ($result === true) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        }

        return $result;
    }

    /**
     * getConfigurationData
     *
     * @return array
     */
    private function getConfigurationData(): array
    {
        return [
            'Logviewer_History_Days'    => Configuration::get('Logviewer_History_Days'),
            'Logviewer_Log_Contexts'    => false != Configuration::get('Logviewer_Log_Contexts')
                                        ? explode('|', Configuration::get('Logviewer_Log_Contexts'))
                                        : [],
            'Logviewer_Log_Levels'      => false != Configuration::get('Logviewer_Log_Levels')
                                        ? explode('|', Configuration::get('Logviewer_Log_Levels'))
                                        : [],
        ];
    }
}
