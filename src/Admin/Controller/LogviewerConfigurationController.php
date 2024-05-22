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
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderInterface;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;
use PrestaShop\Module\Logviewer\Domain\Factory\ReadingStrategyFactoryInterface;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\ExceptionReaderInterface;
use PrestaShop\Module\Logviewer\Domain\Repository\ExceptionEntryRepositoryInterface;

class LogviewerConfigurationController extends FrameworkBundleAdminController
{
    /**
     * @param LogEntryRepositoryInterface $logEntryRepository
     * @param ExceptionEntryRepositoryInterface $exceptionEntryRepository
     * @param LogReaderInterface $logReader
     * @param ReadingStrategyFactoryInterface $readingStrategyFactory
     * @param ExceptionReaderInterface $exceptionReader
     * @param string $currentEnvironment
     */
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
        private ExceptionEntryRepositoryInterface $exceptionEntryRepository,
        private LogReaderInterface $logReader,
        private ReadingStrategyFactoryInterface $readingStrategyFactory,
        private ExceptionReaderInterface $exceptionReader,
        private readonly string $currentEnvironment,
    ) {
    }

    public function indexAction(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $configurationForm = $this->createForm(
            ConfigurationFormType::class,
            $this->getConfigurationData(),
            [
                'max_days' => 5,
                'min_lines' => 100,
                'max_lines' => 5000,
            ]
        );

        $configurationForm->handleRequest($request);

        if ($configurationForm->isSubmitted() && $configurationForm->isValid()) {
            if ($this->handleForm($configurationForm->getData())) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
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
        $strategy = $data['Logviewer_Strategy'];

        if ('history' === $strategy) {
            Configuration::updateValue('Logviewer_History_Days', $data['Logviewer_History_Days']);
            Configuration::updateValue('Logviewer_Tail_Lines', 100);
        } else if ('tail' === $strategy) {
            Configuration::updateValue('Logviewer_Tail_Lines', $data['Logviewer_Tail_Lines']);
            Configuration::updateValue('Logviewer_History_Days', 5);
        }

        Configuration::updateValue('Logviewer_Strategy', $strategy);
        Configuration::updateValue('Logviewer_Log_Contexts', implode('|', $data['Logviewer_Log_Contexts']));
        Configuration::updateValue('Logviewer_Log_Levels', implode('|', $data['Logviewer_Log_Levels']));

        Configuration::updateValue('Logviewer_Last_Line_Read_Dev', 0);
        Configuration::updateValue('Logviewer_Last_Line_Read_Prod', 0);
        Configuration::updateValue('Logviewer_Last_Date_Read', 0);

        Configuration::updateValue('Logviewer_Exception_History_Days', $data['Logviewer_Exception_History_Days']);

        $this->logEntryRepository->deleteAll();
        $this->exceptionEntryRepository->deleteAll();

        $readingStrategy = $this->readingStrategyFactory->create($strategy, $this->currentEnvironment);
        $this->logReader->process($readingStrategy);
        $this->exceptionReader->process();

        return true;
    }

    /**
     * getConfigurationData
     *
     * @return array
     */
    private function getConfigurationData(): array
    {
        return [
            'Logviewer_Strategy'                => Configuration::get('Logviewer_Strategy'),
            'Logviewer_History_Days'            => Configuration::get('Logviewer_History_Days'),
            'Logviewer_Log_Contexts'            => false != Configuration::get('Logviewer_Log_Contexts')
                                                ? explode('|', Configuration::get('Logviewer_Log_Contexts'))
                                                : [],
            'Logviewer_Log_Levels'              => false != Configuration::get('Logviewer_Log_Levels')
                                                ? explode('|', Configuration::get('Logviewer_Log_Levels'))
                                                : [],
            'Logviewer_Tail_Lines'              => Configuration::get('Logviewer_Tail_Lines'),
            'Logviewer_Exception_History_Days'  => Configuration::get('Logviewer_Exception_History_Days'),
        ];
    }
}
