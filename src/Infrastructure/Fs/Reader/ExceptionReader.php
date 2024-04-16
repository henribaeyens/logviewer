<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader;

use DateTime;
use Configuration;
use SplFileObject;
use Validate;
use PrestaShop\Module\Logviewer\Domain\Entity\ExceptionEntry;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\BulkInsert;
use PrestaShop\Module\Logviewer\Admin\Service\Reader\ExceptionReaderInterface;
use PrestaShop\Module\Logviewer\Infrastructure\Db\Operation\DeleteOutdatedEntries;
use PrestaShop\Module\Logviewer\Domain\Repository\ExceptionEntryRepositoryInterface;

final class ExceptionReader extends AbstractReader implements ExceptionReaderInterface
{
    private CONST FILE_NAME_PATTERN = '/(\d{8})_exception.log$/';
    private CONST EXCEPTION_PATTERN = '/^[[:blank:]]+v[\d.]+[[:blank:]]+(\d{4}\/\d{2}\/\d{2})[[:blank:]]-[[:blank:]](\d{2}:\d{2}:\d{2}):(.*)/sm';

    /**
     * @var ExceptionEntryRepositoryInterface
     */
    private $exceptionEntryRepository;

    /**
     * @param string $logsDir
     * @param BulkInsert $bulkInsert
     * @param DeleteOutdatedEntries $deleteOutdatedEntries
     * @param ExceptionEntryRepositoryInterface $exceptionEntryRepository
     */
    public function __construct(
        string $logsDir,
        BulkInsert $bulkInsert,
        DeleteOutdatedEntries $deleteOutdatedEntries,
        ExceptionEntryRepositoryInterface $exceptionEntryRepository
    ) {
        parent::__construct($logsDir, $bulkInsert, $deleteOutdatedEntries);
        $this->exceptionEntryRepository = $exceptionEntryRepository;
        $this->bulkInsert->setDefinition(ExceptionEntry::class);
    }

    public function read(): void
    {
        if (!Validate::isDateFormat($this->getLastDateRead())) {
            $this->initialize();
        } else {
            $this->updateEntries();
        }
    }

    protected function initialize(): void
    {
        $this->readExceptions($this->getDates()[0]);
    }

    protected function updateEntries(): void
    {
        $this->deleteOutdatedEntries(ExceptionEntry::class);
        $this->readExceptions($this->getLastDateRead());
    }

    private function getFilesFromDate(DateTime $startDate): array
    {
        $exceptionLogFiles = [];
        $files = glob("$this->logsDir/*_exception.log");
        foreach ($files as $file) {
            preg_match(self::FILE_NAME_PATTERN, $file, $m);
            $d = DateTime::createFromFormat('Ymd', $m[1])->setTime(0, 0); 
            if ($d < $startDate) {
                continue;
            }
            $exceptionLogFiles[$d->format('Y-m-d')] = $file;
        }

        return $exceptionLogFiles;
    }

    private function readExceptions(string $startDate): void
    {
        $lastDateRead = $startDate;
        $exceptionsRead = 0;
        $rowsToInsert = [];

        $exceptionFiles = $this->getFilesFromDate(new DateTime($startDate));
        foreach ($exceptionFiles as $date => $file) {
            $fo = new SplFileObject($file);
            if ($fo) {
                $chunk = $fo->fread($fo->getSize());
                $errors = explode('*ERROR*', $chunk);
                array_shift($errors);
                $exceptionCount = $this->exceptionEntryRepository->countByDate($date);
                if ($exceptionCount == count($errors)) {
                    continue;
                }
                for ($i = $exceptionCount; $i < count($errors); $i++) {
                    preg_match(self::EXCEPTION_PATTERN, $errors[$i], $m);
                    $rowsToInsert[] = sprintf("('%s', '%s')", $date . ' ' . $m[2], pSQL(trim($m[3]), true));
                    if (++$exceptionsRead % 20 == 0) {
                        $this->bulkInsert->execute($rowsToInsert);
                        $rowsToInsert = [];
                    }
                }
                $fo = null;
            }
            $lastDateRead = $date;
        }
        $this->setLastDateRead($lastDateRead);
        $this->bulkInsert->execute($rowsToInsert);
        $rowsToInsert = [];
    }

    private function getLastDateRead(): string|bool
    {
        return Configuration::get('Logviewer_Last_Date_Read');
    }

    private function setLastDateRead(string $date): void
    {
        Configuration::updateValue('Logviewer_Last_Date_Read', $date);
    }

}
