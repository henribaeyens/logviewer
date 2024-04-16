/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and ContFributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

const $ = window.$;

$(() => {
    const logsGrid = new window.prestashop.component.Grid('logviewer_grid_log_entries');
    logsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    logsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    logsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());

    const exceptionsGrid = new window.prestashop.component.Grid('logviewer_grid_exception_entries');
    exceptionsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    exceptionsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    exceptionsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
});
