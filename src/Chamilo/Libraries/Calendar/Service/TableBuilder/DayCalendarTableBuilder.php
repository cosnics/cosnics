<?php
namespace Chamilo\Libraries\Calendar\Service\TableBuilder;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Architecture\Trait\HourBasedCalendarTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\TableBuilder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayCalendarTableBuilder extends CalendarTableBuilder
{
    use HourBasedCalendarTrait;

    public function __construct(
        Translator $translator, ?User $user, UserService $userService, bool $defaultHideNonWorkingHours,
        int $defaultHourStep, int $defaultWorkingHoursEnd, int $defaultWorkingHoursStart
    )
    {
        parent::__construct($translator, $user, $userService);

        $this->setDefaultHideNonWorkingHours($defaultHideNonWorkingHours);
        $this->setDefaultHourStep($defaultHourStep);
        $this->setDefaultWorkingHoursEnd($defaultWorkingHoursEnd);
        $this->setDefaultWorkingHoursStart($defaultWorkingHoursStart);
    }

    protected function addEvents(int $displayTime, HTML_Table $table, array $cellMapping, array $events): void
    {
        $start = 0;

        if ($this->getHideNonWorkingHours()) {
            $start = $this->getStartHour();
        }

        foreach ($events as $time => $items) {
            if ($time >= $this->getTableEndTime($displayTime)) {
                continue;
            }

            $row = (date('H', $time) - $start) / $this->hourStep;

            foreach ($items as $item) {
                try {
                    $cellContent = $table->getCellContents($row, 1);
                    $cellContent .= $item;
                    $table->setCellContents($row, 1, $cellContent);
                }
                catch (Exception) {
                }
            }
        }
    }

    /**
     * @throws \TableException
     */
    protected function buildTable(HTML_Table $table, int $displayTime, ?string $dayUrlTemplate = null): array
    {
        $header = $table->getHeader();
        $header->setRowType(0, 'th');
        $header->setHeaderContents(0, 0, '');
        $header->setCellAttributes(0, 0, ['class' => 'table-calendar-day-hours']);

        $header->setHeaderContents(
            0, 1,
            $this->getTranslator()->trans(date('l', $displayTime) . 'Short', [], StringUtilities::LIBRARIES) . ' ' .
            date('d/m', $displayTime)
        );

        $startHour = 0;
        $endHour = 24;

        if ($this->getHideNonWorkingHours()) {
            $startHour = $this->getStartHour();
            $endHour = $this->getEndHour();
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep()) {
            $rowId = ($hour / $this->getHourStep()) - $startHour;
            $cellContent = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
            $table->setCellContents($rowId, 0, $cellContent);

            $classes = [];

            $classes[] = 'table-calendar-day-hours';

            if ($hour % 2 == 0) {
                $classes[] = 'table-calendar-alternate';
            }

            $table->setCellAttributes($rowId, 0, ['class' => $classes]);
        }

        for ($hour = $startHour; $hour < $endHour; $hour += $this->getHourStep()) {
            $rowId = ($hour / $this->getHourStep()) - $startHour;

            $table->setCellContents($rowId, 1, '');

            $classes = $this->determineCellClasses($hour, $displayTime);

            if (count($classes) > 0) {
                $table->setCellAttributes($rowId, 1, ['class' => $classes]);
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    protected function determineCellClasses(int $hour, int $displayTime): array
    {
        $classes = [];

        // Highlight current hour
        if (date('Y-m-d') == date('Y-m-d', $displayTime)) {
            if (date('H') >= $hour && date('H') < $hour + $this->getHourStep()) {
                $classes[] = 'table-calendar-highlight';
            }
        }

        // Is current table hour during working hours?
        if ($hour < $this->getStartHour() || $hour >= $this->getEndHour()) {
            $classes[] = 'table-calendar-disabled';
        }

        if ($hour % 2 == 0) {
            $classes[] = 'table-calendar-alternate';
        }

        return $classes;
    }

    public function getTableEndTime(int $displayTime): int
    {
        if ($this->getHideNonWorkingHours()) {
            return strtotime(date('Y-m-d ' . ($this->getEndHour() - 1) . ':59:59', $displayTime));
        }

        return strtotime('+24 Hours', $this->getTableStartTime($displayTime));
    }

    public function getTableStartTime(int $displayTime): int
    {
        if ($this->getHideNonWorkingHours()) {
            return strtotime(date('Y-m-d ' . $this->getStartHour() . ':00:00', $displayTime));
        }

        return strtotime(date('Y-m-d 00:00:00', $displayTime));
    }
}
