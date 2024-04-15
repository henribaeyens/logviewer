<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Domain\Entity;

use ObjectModel;

class ExceptionEntry extends ObjectModel
{
    public CONST TABLE = 'logviewer_exception';

    public $id;

    /** @var int entry ID */
    public $id_entry;

     /** @var string Entry environment (ie. dev or prod) */
    public $env;

    /** @var string Entry date */
    public $date;

    /** @var string Entry context (ie. doctrine, php, &c) */
    public $context;

    public static $definition = [
        'table' => self::TABLE,
        'primary' => 'id_entry',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'content' => ['type' => self::TYPE_HTML],
        ],
    ];

}
