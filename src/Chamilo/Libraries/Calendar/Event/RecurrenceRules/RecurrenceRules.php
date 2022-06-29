<?php
namespace Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 * @package Chamilo\Libraries\Calendar\Event\RecurrenceRules
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecurrenceRules
{
    public const FREQUENCY_BIWEEKLY = 4;
    public const FREQUENCY_DAILY = 1;
    public const FREQUENCY_MONTHLY = 5;
    public const FREQUENCY_NONE = 0;
    public const FREQUENCY_WEEKDAYS = 3;
    public const FREQUENCY_WEEKLY = 2;
    public const FREQUENCY_YEARLY = 6;

    /**
     * @var string[]
     */
    private array $byDay;

    /**
     * @var int[]
     */
    private array $byMonth;

    /**
     * @var int[]
     */
    private array $byMonthDay;

    /**
     * @var int[]
     */
    private array $byWeekNumber;

    private ?int $count;

    private ?int $frequency;

    private ?int $interval;

    private ?int $until;

    /**
     * @param string[] $byDay
     * @param int[] $byMonthDay
     * @param int[] $byMonth
     * @param int[] $byWeekNumber
     */
    public function __construct(
        ?int $frequency = self::FREQUENCY_NONE, ?int $until = null, ?int $count = null, ?int $interval = null,
        array $byDay = [], array $byMonthDay = [], array $byMonth = [], array $byWeekNumber = []
    )
    {
        $this->frequency = $frequency;
        $this->until = $until;
        $this->count = $count;
        $this->interval = $interval;
        $this->byDay = $byDay;
        $this->byMonthDay = $byMonthDay;
        $this->byMonth = $byMonth;
        $this->byWeekNumber = $byWeekNumber;
    }

    /**
     * @return string[]
     */
    public function getByDay(): array
    {
        return $this->byDay;
    }

    /**
     * @param string[] $byDay
     */
    public function setByDay(array $byDay)
    {
        $this->byDay = $byDay;
    }

    /**
     * @return int[]
     */
    public function getByMonth(): array
    {
        return $this->byMonth;
    }

    /**
     * @param int[] $byMonth
     */
    public function setByMonth(array $byMonth)
    {
        $this->byMonth = $byMonth;
    }

    /**
     * @return int[]
     */
    public function getByMonthDay(): array
    {
        return $this->byMonthDay;
    }

    /**
     * @param int[] $byMonthDay
     */
    public function setByMonthDay(array $byMonthDay)
    {
        $this->byMonthDay = $byMonthDay;
    }

    /**
     * @return int[]
     */
    public function getByWeekNumber(): array
    {
        return $this->byWeekNumber;
    }

    /**
     * @param int[] $byWeekNumber
     */
    public function setByWeekNumber(array $byWeekNumber)
    {
        $this->byWeekNumber = $byWeekNumber;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count)
    {
        $this->count = $count;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(?int $frequency)
    {
        $this->frequency = $frequency;
    }

    public function getInterval(): ?int
    {
        return $this->interval;
    }

    public function setInterval(?int $interval)
    {
        $this->interval = $interval;
    }

    public function getUntil(): ?int
    {
        return $this->until;
    }

    public function setUntil(?int $until)
    {
        $this->until = $until;
    }

    public function hasRecurrence(): bool
    {
        return $this->getFrequency() != self::FREQUENCY_NONE;
    }

    public function isIndefinite(): bool
    {
        $repeatTo = $this->getUntil();

        return ($repeatTo == 0);
    }
}