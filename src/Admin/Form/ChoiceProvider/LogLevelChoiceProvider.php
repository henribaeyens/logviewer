<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Form\ChoiceProvider;

use PrestaShop\Module\Logviewer\Domain\Entity\LogEntry;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;

final class LogLevelChoiceProvider
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
        $levels = $this->logEntryRepository->findLogLevels();

        if (empty($levels)) {
            return LogEntry::LEVELS;
        }

        $choices = [];
        foreach ($levels as $level) {
            $choices[$level['level']] = $level['level'];
        }

        return $choices;
    }
}
