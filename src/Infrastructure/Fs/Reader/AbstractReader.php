<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader;

use DateTime;
use DateInterval;
use Configuration;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteOutdatedEntries;

abstract class AbstractReader
{
    /**
     * @var string
     */
    protected $logsDir;

    /**
     * @var BulkInsert
     */
    protected $bulkInsert;

    /**
     * @var DeleteOutdatedEntries
     */
    protected $deleteOutdatedEntries;

    /**
     * @param string $logsDir
     * @param BulkInsert $bulkInsert
     * @param DeleteOutdatedEntries $deleteOutdatedEntries
     */
    protected function __construct(
        string $logsDir,
        BulkInsert $bulkInsert,
        DeleteOutdatedEntries $deleteOutdatedEntries,
    ) {
        $this->logsDir = $logsDir;
        $this->bulkInsert = $bulkInsert;
        $this->deleteOutdatedEntries = $deleteOutdatedEntries;
    }

    protected function getDates(): array
    {
        $history = (int) Configuration::get('Logviewer_History_Days');
        $toDay = new DateTime();

        $dates = [];

        $dates[] = $toDay->format('Y-m-d');
        foreach (range(1, $history - 1) as $d) {
            $dates[] = $toDay->sub(new DateInterval("P1D"))->format('Y-m-d');
        }
        return array_reverse($dates);
    }

    protected function deleteOutdatedEntries($entityClass): void
    {
        $this->deleteOutdatedEntries->setDefinition($entityClass);
        $this->deleteOutdatedEntries->execute($this->getDates());
    }

    abstract protected function initialize(): void;
    abstract protected function updateEntries(): void;

}
