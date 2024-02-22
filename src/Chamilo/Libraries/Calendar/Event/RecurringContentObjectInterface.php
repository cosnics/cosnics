<?php
namespace Chamilo\Libraries\Calendar\Event;

/**
 * @package Chamilo\Libraries\Calendar\Event\Recurrence
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface RecurringContentObjectInterface
{
    public const FREQUENCY_BIWEEKLY = 4;
    public const FREQUENCY_DAILY = 1;
    public const FREQUENCY_MONTHLY = 5;
    public const FREQUENCY_NONE = 0;
    public const FREQUENCY_WEEKDAYS = 3;
    public const FREQUENCY_WEEKLY = 2;
    public const FREQUENCY_YEARLY = 6;

    public const PROPERTY_BYDAY = 'byday';
    public const PROPERTY_BYMONTH = 'bymonth';
    public const PROPERTY_BYMONTHDAY = 'bymonthday';
    public const PROPERTY_FREQUENCY = 'frequency';
    public const PROPERTY_FREQUENCY_COUNT = 'frequency_count';
    public const PROPERTY_FREQUENCY_INTERVAL = 'frequency_interval';
    public const PROPERTY_UNTIL = 'until';

    /**
     * @throws \Exception
     */
    public static function frequency_as_string(int $frequency): string;

    public function frequency_is_indefinately(): bool;

    public function get_byday(): ?string;

    public static function get_byday_ical_format(int $rank, int $day): string;

    /**
     * @return string[]
     */
    public static function get_byday_options(): array;

    /**
     * @return string[]
     */
    public static function get_byday_parts(string $bydays): array;

    public function get_bymonth(): ?string;

    /**
     * @return string[]
     */
    public static function get_bymonth_options(): array;

    public static function get_bymonth_string(int $month): string;

    public function get_bymonthday(): ?string;

    /**
     * @return string[]
     */
    public static function get_bymonthday_options(): array;

    public static function get_day_format(string $day): int;

    public static function get_day_ical_format(int $day): string;

    public static function get_day_string(int $day_number): string;

    public function get_frequency(): ?int;

    public function get_frequency_as_string(): string;

    public function get_frequency_count(): ?int;

    public function get_frequency_interval(): ?int;

    /**
     * @return string[]
     */
    public static function get_frequency_options(): array;

    /**
     * @return string[]
     */
    public static function get_rank_options(): array;

    public static function get_rank_string(int $rank): string;

    public function get_until(): ?int;

    public function has_frequency(): bool;

    public function set_bymonth(?string $bymonth);

    public function set_bymonthday(?string $bymonthday);

    public function set_frequency(?int $frequency);

    public function set_frequency_count(?int $frequency_count);

    public function set_frequency_interval(?int $frequency_interval);

    public function set_until(?int $until);
}