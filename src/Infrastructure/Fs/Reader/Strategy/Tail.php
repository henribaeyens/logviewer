<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader\Strategy;

use Configuration;
use SplFileObject;
use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteAllEntries;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;

final class Tail implements LogReaderStrategyInterface
{
    /**
     * @var SplFileObject
     */
    protected $fileObject = null;

    /**
     * @param BulkInsert $bulkInsert
     * @param DeleteAllEntries $deleteAllEntries
     * @param string $currentEnvironment
     */
    public function __construct(
        private BulkInsert $bulkInsert,
        private DeleteAllEntries $deleteAllEntries,
        private readonly string $currentEnvironment,
    ) {
        $this->deleteAllEntries->setDefinition(LogEntry::class);
        $this->bulkInsert->setDefinition(LogEntry::class);
    }

    public function setFile(SplFileObject $fileObject): void
    {
        $this->fileObject = $fileObject;
    }

    public function process(): void
    {
        $this->deleteAllEntries->execute();
        $this->read($this->countLines());
        $this->fileObject = null;
    }

    private function read(int $lineCount): void
    {
        $contexts = Configuration::get('Logviewer_Log_Contexts');
        $levels= Configuration::get('Logviewer_Log_Levels');
        $lineToRead = (int) Configuration::get('Logviewer_Tail_Lines');

        $pattern = '/^\[([0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2})\]\s(' . $contexts . ')\.(' . $levels . '):\s(.+)$/';
    
        $linesRead = 0;
        $rowsToInsert = [];
        while ($linesRead < $lineToRead) {
            if ($lineCount == 0) {
                break;
            }
            $this->fileObject->seek(--$lineCount);
            $line = $this->fileObject->current();
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
    }

    private function countLines(): int
    {
        $this->fileObject->seek(PHP_INT_MAX);
    
        return $this->fileObject->key(); 
    }

}
