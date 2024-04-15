<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Infrastructure\Db\Operation;

use ObjectModel;

abstract class AbstractDbOperation
{
    /**
     * @var array
     */
    protected $definition;

    public function setDefinition($entity): void
    {
        $this->definition = ObjectModel::getDefinition(new $entity());
    }

    abstract public function execute(array $data): bool;

}
