<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Form\ChoiceProvider;

use PrestaShop\Module\Logviewer\Domain\Enum\LogContext;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;

final class LogContextChoiceProvider
{
    /**
     * @param LogEntryRepositoryInterface $logEntryRepository
     */
    public function __construct(
        private LogEntryRepositoryInterface $logEntryRepository,
    ) {
    }

    public function getChoices(): array
    {
        $contexts = $this->logEntryRepository->findLogContexts();

        if (empty($contexts)) {
            return LogContext::array();
        }

        $choices = [];
        foreach ($contexts as $context) {
            $choices[$context['context']] = $context['context'];
        }

        return $choices;
    }
}
