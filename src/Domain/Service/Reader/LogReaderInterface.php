<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Service\Reader;

use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;

interface LogReaderInterface
{
    public function process(LogReaderStrategyInterface $readerStrategy): bool|string;
}
