<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\Module\Logviewer\Admin\Module\TableHandler;
use PrestaShop\Module\Logviewer\Admin\Module\TabHandler;

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Logviewer extends Module
{
    /**
     * @var bool
     */
    private $ps_version;

    /**
     * @var string
     */
    private $logo_path;

    /**
     * @var string
     */
    private $module_path;

    const MODULE_ADMIN_CONTROLLERS = [
        [
            'class_name' => 'LogviewerAdminParentController',
            'visible' => false,
            'parent_class_name' => 'AdminParentModulesSf',
            'name' => 'Logviewer',
        ],
        [
            'class_name' => 'LogviewerConfigurationController',
            'visible' => true,
            'parent_class_name' => 'LogviewerAdminParentController',
            'name' => 'Configuration',
        ],
        [
            'class_name' => 'LogviewerLogController',
            'visible' => true,
            'parent_class_name' => 'LogviewerAdminParentController',
            'name' => 'Logs',
        ],
        [
            'class_name' => 'LogviewerExceptionController',
            'visible' => true,
            'parent_class_name' => 'LogviewerAdminParentController',
            'name' => 'Exceptions',
        ],
    ];

    public function __construct()
    {
        $this->name = 'logviewer';
        $this->tab = 'other';
        $this->version = '1.1.0';
        $this->author = 'Henri Baeyens';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Logviewer');
        $this->description = $this->l('View logs (errors and exceptions) from your back-office.');
        $this->ps_version = (bool) version_compare(_PS_VERSION_, '8', '>=');

       // $this->logo_path = $this->_path . 'logo.png';
        $this->module_path = $this->_path;

        $this->ps_versions_compliancy = ['min' => '8', 'max' => _PS_VERSION_];
    }

    /**
     * install()
     *
     * @return bool
     */
    public function install(): bool
    {
        Configuration::updateValue('Logviewer_History_Days', 5);
        Configuration::updateValue('Logviewer_Last_Line_Read_Dev', 0);
        Configuration::updateValue('Logviewer_Last_Line_Read_Prod', 0);
        Configuration::updateValue('Logviewer_Last_Date_Read', 0);

        $finder = new ContainerFinder(Context::getContext());
        $sfContainer = $finder->getContainer();
        $tabRepository = $sfContainer->get('prestashop.core.admin.tab.repository');

        return (new TableHandler())->create() && (new TabHandler($tabRepository, $this->getTranslator()))->install() && parent::install();
    }

    /**
     * uninstall()
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        Configuration::deleteByName('Logviewer_History_Days');
        Configuration::deleteByName('Logviewer_Last_Line_Read_Dev');
        Configuration::deleteByName('Logviewer_Last_Line_Read_Prod');
        Configuration::deleteByName('Logviewer_Last_Date_Read');

        $finder = new ContainerFinder(Context::getContext());
        $sfContainer = $finder->getContainer();
        $tabRepository = $sfContainer->get('prestashop.core.admin.tab.repository');

        return (new TableHandler())->drop() && (new TabHandler($tabRepository, $this->getTranslator()))->uninstall() && parent::uninstall();
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            SymfonyContainer::getInstance()->get('router')->generate('logviewer_configuration')
        );
    }
}
