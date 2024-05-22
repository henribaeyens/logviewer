<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Fs\Reader;

use SplFileObject;
use PrestaShop\Module\Logviewer\Domain\Exception\FileErrorException;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderInterface;
use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;

final class LogReader implements LogReaderInterface
{
    /**
     * @param string $logsDir
     * @param string $currentEnvironment
     */
    public function __construct(
        protected readonly string $logsDir,
        protected readonly string $currentEnvironment
    ) {
    }

    public function process(LogReaderStrategyInterface $readerStrategy): bool|string
    {
        try {
            $readerStrategy->setFile($this->getFile());
        } catch (FileErrorException $e) {
            return $e->getMessage();
        }

        $readerStrategy->process();

        return true;
    }

    /**
     * @return SplFileObject
     * 
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
 
}
