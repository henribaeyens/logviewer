<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Form\ChoiceProvider;

use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;

final class LogContextChoiceProvider
{
    /**
     * @var LogEntryRepositoryInterface
     */
    protected $logEntryRepository;
    
    /**
     * @param LogEntryRepositoryInterface $logEntryRepository
     */
    public function __construct(
        LogEntryRepositoryInterface $logEntryRepository
    ) {
        $this->logEntryRepository = $logEntryRepository;
    }

    public function getChoices(): array
    {
        $contexts = $this->logEntryRepository->findLogContexts();

        if (empty($contexts)) {
            return LogEntry::CONTEXTS;
        }

        $choices = [];
        foreach ($contexts as $context) {
            $choices[$context['context']] = $context['context'];
        }

        return $choices;
    }
}
