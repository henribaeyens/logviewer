<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Service\Reader;

use SplFileObject;

interface LogReaderStrategyInterface
{
    public function setFile(SplFileObject $fileObject): void;
    public function process(): void;
}
