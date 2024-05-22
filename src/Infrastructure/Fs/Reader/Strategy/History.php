<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader\Strategy;

use DateTime;
use DateInterval;
use Configuration;
use SplFileObject;
use PrestaShop\Module\Logviewer\Domain\Tool\Tools;
use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteOutdatedEntries;

final class History implements LogReaderStrategyInterface
{
    /**
     * @var SplFileObject
     */
    protected $fileObject = null;

    /**
     * @param BulkInsert $bulkInsert
     * @param DeleteOutdatedEntries $deleteOutdatedEntries,
     * @param string $currentEnvironment
     */
    public function __construct(
        private BulkInsert $bulkInsert,
        private DeleteOutdatedEntries $deleteOutdatedEntries,
        private readonly string $currentEnvironment,
    ) {
        $this->deleteOutdatedEntries->setDefinition(LogEntry::class);
        $this->bulkInsert->setDefinition(LogEntry::class);
    }

    public function setFile(SplFileObject $fileObject): void
    {
        $this->fileObject = $fileObject;
    }

    public function process(): void
    {
        if (0 === $this->getLastLineRead()) {
            $this->initialize();
        } else {
            $this->deleteOutdatedEntries();
            $this->updateEntries();
        }
    }

    private function initialize(): void
    {
        $this->fileObject->seek(0);
        $line = $this->findFirstDate(Tools::getDates((int) Configuration::get('Logviewer_History_Days')));
        $this->fileObject->seek($line - 1);
        $this->read();
        $this->fileObject = null;
    }

    private function updateEntries(): void
    {
        $line = $this->getLastLineRead();
        $this->fileObject->seek($line - 1);
        $this->read();
        $this->fileObject = null;
    }

    private function read(): void
    {
        $contexts = Configuration::get('Logviewer_Log_Contexts');
        $levels= Configuration::get('Logviewer_Log_Levels');

        $pattern = '/^\[([0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2})\]\s(' . $contexts . ')\.(' . $levels . '):\s(.+)$/';
    
        $linesRead = 0;
        $rowsToInsert = [];
        while (!$this->fileObject->eof()) {
            $line = $this->fileObject->fgets();
            preg_match($pattern, $line, $m);
            if (empty($m[1])) {
                continue;
            }
            $rowsToInsert[] = sprintf("('%s', '%s', '%s', '%s', '%s')", $this->currentEnvironment, $m[1], $m[2], $m[3], pSQL(rtrim($m[4], '[] ')));
            if (++$linesRead % 150 == 0) {
                $this->bulkInsert->execute($rowsToInsert);
                $rowsToInsert = [];
            }
        }
        if (!empty($rowsToInsert)) {
            $this->bulkInsert->execute($rowsToInsert);
            $rowsToInsert = [];
        }

        $this->setLastLineRead($this->fileObject->key());
    }

    private function findFirstDate(array $dates): int
    {
        while (!$this->fileObject->eof()) {
            $line = $this->fileObject->fgets();
            foreach ($dates as $date) {
                if (str_starts_with($line, "[" . $date)) {
                    return $this->fileObject->key();
                }
            }
        }
        return 0;
    }

    private function setLastLineRead(int $line): void
    {
        $key = 'Logviewer_Last_Line_Read_' . ucfirst($this->currentEnvironment);
        Configuration::updateValue($key, $line);
    }

    private function getLastLineRead(): int|bool
    {
        $key = 'Logviewer_Last_Line_Read_' . ucfirst($this->currentEnvironment);
        return (int) Configuration::get($key);
    }

    protected function deleteOutdatedEntries(): void
    {
        $this->deleteOutdatedEntries->execute(Tools::getDates((int) Configuration::get('Logviewer_History_Days')));
    }

}
