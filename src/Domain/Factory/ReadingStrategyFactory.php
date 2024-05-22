<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Factory;

use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Domain\Exception\StrategyNotFoundException;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteAllEntries;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteOutdatedEntries;

class ReadingStrategyFactory implements ReadingStrategyFactoryInterface
{
    /**
     * Returns a reading strategy interface
     *
     * @throws StrategyNotFoundException
     */
    public function create(
        string $strategy,
        ?string $currentEnvironment = null,
    ): LogReaderStrategyInterface {
        $strategyClass = sprintf('PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader\Strategy\%s', ucfirst($strategy));

        if (class_exists($strategyClass)) {
            if ('history' === $strategy) {
                return new $strategyClass(new BulkInsert(), new DeleteOutdatedEntries(), $currentEnvironment);
            } else if ('tail' === $strategy) {
                return new $strategyClass(new BulkInsert(), new DeleteAllEntries(), $currentEnvironment);
            }
        }
        
        throw new StrategyNotFoundException(sprintf('Strategy "%s" not found.', $strategyClass));
    }

}