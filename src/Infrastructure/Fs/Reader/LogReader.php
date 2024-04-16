<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader;

use Configuration;
use SplFileObject;
use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Domain\Exception\FileErrorException;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Admin\Service\Reader\LogReaderInterface;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteOutdatedEntries;

final class LogReader extends AbstractReader implements LogReaderInterface
{
    /**
     * @var string
     */
    private $currentEnvironment;

    /**
     * @var SplFileObject
     */
    private $fileObject = null;

    /**
     * @param string $logsDir
     * @param BulkInsert $bulkInsert
     * @param DeleteOutdatedEntries $deleteOutdatedEntries
     * @param string $currentEnvironment
     */
    public function __construct(
        string $logsDir,
        BulkInsert $bulkInsert,
        DeleteOutdatedEntries $deleteOutdatedEntries,
        string $currentEnvironment
    ) {
        parent::__construct($logsDir, $bulkInsert, $deleteOutdatedEntries);
        $this->bulkInsert->setDefinition(LogEntry::class);
        $this->currentEnvironment = $currentEnvironment;
    }

    public function read(): bool|string
    {
        try {
            $this->fileObject = $this->getFile();
        } catch (FileErrorException $e) {
            return $e->getMessage();
        }

        if (0 === $this->getLastLineRead()) {
            $this->initialize();
        } else {
            $this->deleteOutdatedEntries(LogEntry::class);
            $this->updateEntries();
        }
        return true;
    }

    protected function initialize(): void
    {
        $this->fileObject->seek(0);
        $line = $this->findFirstDate($this->getDates());
        $this->fileObject->seek($line - 1);
        $this->readLogs();
        $this->fileObject = null;
    }

    protected function updateEntries(): void
    {
        $line = $this->getLastLineRead();
        $this->fileObject->seek($line - 1);
        $this->readLogs();
        $this->fileObject = null;
    }

    public function readLogs(): void
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
        $this->bulkInsert->execute($rowsToInsert);
        $rowsToInsert = [];

        $this->setLastLineRead($this->fileObject->key());
    }

    /**
     * @throws FileErrorException
     */
    private function getFile(): SplFileObject
    {
        $file = "$this->logsDir/$this->currentEnvironment.log";

        if (!is_file($file)) {
            throw new FileErrorException(
                sprintf('An error occured when attempting to read "%s".', $file)
            );
        }
        return new SplFileObject($file);
    }

    private function countLines(): int
    {
        $this->fileObject->seek(PHP_INT_MAX);
    
        return $this->fileObject->key(); 
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
}
