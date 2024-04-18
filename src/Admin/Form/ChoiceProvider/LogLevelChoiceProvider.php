<?php declare(strict_types=1);

/**
 * @author    Henri Baeyens <henri.baeyens@gmail.com>
 * @copyright 2024
 * @license   MIT License
 */

namespace PrestaShop\Module\Logviewer\Admin\Form\ChoiceProvider;

use PrestaShop\Module\Logviewer\Domain\Enum\LogLevel;
use PrestaShop\Module\Logviewer\Domain\Repository\LogEntryRepositoryInterface;

final class LogLevelChoiceProvider
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
        $levels = $this->logEntryRepository->findLogLevels();

        if (empty($levels)) {
            return LogLevel::array();
        }

        $choices = [];
        foreach ($levels as $level) {
            $choices[$level['level']] = $level['level'];
        }

        return $choices;
    }
}
