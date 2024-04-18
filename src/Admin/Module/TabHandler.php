<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Module;

use Logviewer;
use Tab;
use Validate;
use Language;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\Translation\TranslatorInterface;

final class TabHandler
{
    public function __construct(
        private TabRepository $tabRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function install(): bool
    {
        $installTabCompleted = true;

        foreach (Logviewer::MODULE_ADMIN_CONTROLLERS as $controller) {
            if ($this->tabRepository->findOneIdByClassName($controller['class_name'])) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $controller['class_name'];
            $tab->active = $controller['visible'];
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $this->translator->trans($controller['name'], [], 'Modules.Logviewer.Admin', $lang['locale']);
            }
            $tab->id_parent = $this->tabRepository->findOneIdByClassName($controller['parent_class_name']);
            $tab->module = 'logviewer';
            $installTabCompleted = $installTabCompleted && $tab->add();
        }

        return $installTabCompleted;
    }

    public function uninstall(): bool
    {
        $uninstallTabCompleted = true;

        foreach (Logviewer::MODULE_ADMIN_CONTROLLERS as $controller) {
            $id_tab = (int) $this->tabRepository->findOneIdByClassName($controller['class_name']);
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
            }
        }

        return $uninstallTabCompleted;
    }

}
