<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Factory;

use PrestaShop\Module\Logviewer\Domain\Service\Reader\LogReaderStrategyInterface;

interface ReadingStrategyFactoryInterface
{
    public function create(
        string $strategy,
        ?string $currentEnvironment = null,
    ): LogReaderStrategyInterface;
}