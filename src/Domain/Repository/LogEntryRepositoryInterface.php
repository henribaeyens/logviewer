<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Repository;

interface LogEntryRepositoryInterface
{
    public function findLogContexts(): array;
    public function findLogLevels(): array;
    public function deleteAll(): int;
    
}
