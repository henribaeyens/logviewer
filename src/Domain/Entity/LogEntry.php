<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Entity;

use ObjectModel;

class LogEntry extends ObjectModel
{
    public CONST TABLE = 'logviewer_log';

    public CONST LEVELS = [
        'INFO' => 'INFO',
        'DEBUG' => 'DEBUG',
        'WARNING' => 'WARNING',
        'CRITICAL' => 'CRITICAL',
    ];

    public CONST CONTEXTS = [
        'app' => 'app',
        'console' => 'console',
        'doctrine' => 'doctrine',
        'php' => 'php',
        'request' => 'request',
        'security' => 'security',
    ];

    public $id;

    /** @var int entry ID */
    public $id_entry;

     /** @var string Entry environment (ie. dev or prod) */
    public $env;

    /** @var string Entry date */
    public $date;

    /** @var string Entry context (ie. doctrine, php, &c) */
    public $context;

    /** @var string Entry severity level (ie. warning, info, &c) */
    public $level;

    /** @var string Error content */
    public $content;

    public static $definition = [
        'table' => self::TABLE,
        'primary' => 'id_entry',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'env' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 6],
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'context' => ['type' => self::TYPE_STRING, 'size' => 16],
            'level' => ['type' => self::TYPE_STRING, 'size' => 16],
            'content' => ['type' => self::TYPE_STRING],
        ],
    ];

}
