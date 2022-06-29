<?php
namespace Chamilo\Libraries\Calendar\Event\Recurrence;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Event\Recurrence
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RecurringContentObject extends ContentObject
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

    public static array $days = [1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA', 7 => 'SU'];

    /**
     * @throws \Exception
     */
    public static function frequency_as_string(int $frequency): string
    {
        $translator = Translation::getInstance();

        switch ($frequency)
        {
            case self::FREQUENCY_DAILY :
                $string = $translator->getTranslation('Daily', [], 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_WEEKLY :
                $string = $translator->getTranslation('Weekly', [], 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_MONTHLY :
                $string = $translator->getTranslation('Monthly', [], 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_YEARLY :
                $string = $translator->getTranslation('Yearly', [], 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_WEEKDAYS :
                $string = $translator->getTranslation('Weekdays', [], 'Chamilo\Libraries\Calendar');
                break;
            case self::FREQUENCY_BIWEEKLY :
                $string = $translator->getTranslation('Biweekly', [], 'Chamilo\Libraries\Calendar');
                break;
            default:
                throw new Exception();
        }

        return $string;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function frequency_is_indefinately(): bool
    {
        $repeat_to = $this->get_until();

        return ($repeat_to == 0);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassnameFromNamespace(static::class, true);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_byday(): ?string
    {
        return $this->getAdditionalProperty(self::PROPERTY_BYDAY);
    }

    public static function get_byday_ical_format(int $rank, int $day): string
    {
        $format = [];
        if ($rank != 0)
        {
            $format[] = $rank;
        }

        $format[] = self::get_day_ical_format($day);

        return implode('', $format);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function get_byday_options(): array
    {
        $translator = Translation::getInstance();

        return [
            1 => $translator->getTranslation('Monday', [], 'Chamilo\Libraries\Calendar'),
            2 => $translator->getTranslation('Tuesday', [], 'Chamilo\Libraries\Calendar'),
            3 => $translator->getTranslation('Wednesday', [], 'Chamilo\Libraries\Calendar'),
            4 => $translator->getTranslation('Thursday', [], 'Chamilo\Libraries\Calendar'),
            5 => $translator->getTranslation('Friday', [], 'Chamilo\Libraries\Calendar'),
            6 => $translator->getTranslation('Saturday', [], 'Chamilo\Libraries\Calendar'),
            7 => $translator->getTranslation('Sunday', [], 'Chamilo\Libraries\Calendar')
        ];
    }

    /**
     * @return string[]
     */
    public static function get_byday_parts(string $bydays): array
    {
        $bydays = explode(',', $bydays);
        $parts = [];

        foreach ($bydays as $byday)
        {
            preg_match_all('/(-?[1-5]?)([A-Z]+)/', $byday, $byday_parts);

            $parts[] = [$byday_parts[1] == 0 ? 0 : $byday_parts[1][0], $byday_parts[2][0]];
        }

        return $parts;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_bymonth(): ?string
    {
        return $this->getAdditionalProperty(self::PROPERTY_BYMONTH);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function get_bymonth_options(): array
    {
        $translator = Translation::getInstance();

        return [
            1 => $translator->getTranslation('January', [], 'Chamilo\Libraries\Calendar'),
            2 => $translator->getTranslation('February', [], 'Chamilo\Libraries\Calendar'),
            3 => $translator->getTranslation('March', [], 'Chamilo\Libraries\Calendar'),
            4 => $translator->getTranslation('April', [], 'Chamilo\Libraries\Calendar'),
            5 => $translator->getTranslation('May', [], 'Chamilo\Libraries\Calendar'),
            6 => $translator->getTranslation('June', [], 'Chamilo\Libraries\Calendar'),
            7 => $translator->getTranslation('Juli', [], 'Chamilo\Libraries\Calendar'),
            8 => $translator->getTranslation('August', [], 'Chamilo\Libraries\Calendar'),
            9 => $translator->getTranslation('September', [], 'Chamilo\Libraries\Calendar'),
            10 => $translator->getTranslation('October', [], 'Chamilo\Libraries\Calendar'),
            11 => $translator->getTranslation('November', [], 'Chamilo\Libraries\Calendar'),
            12 => $translator->getTranslation('December', [], 'Chamilo\Libraries\Calendar')
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public static function get_bymonth_string(int $month): string
    {
        $translation = self::get_bymonth_options();

        return $translation[$month];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_bymonthday(): ?string
    {
        return $this->getAdditionalProperty(self::PROPERTY_BYMONTHDAY);
    }

    /**
     * @return string[]
     */
    public static function get_bymonthday_options(): array
    {
        return range(1, 31);
    }

    public static function get_day_format(string $day): int
    {
        $days = array_flip(self::$days);

        return $days[$day];
    }

    public static function get_day_ical_format(int $day): string
    {
        return self::$days[$day];
    }

    /**
     * @throws \ReflectionException
     */
    public static function get_day_string(int $day_number): string
    {
        if (!is_numeric($day_number))
        {
            $day_number = self::get_day_format($day_number);
        }
        $translation = self::get_byday_options();

        return $translation[$day_number];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_frequency(): ?int
    {
        return $this->getAdditionalProperty(self::PROPERTY_FREQUENCY);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function get_frequency_as_string(): string
    {
        return self::frequency_as_string($this->get_frequency());
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_frequency_count(): ?int
    {
        return $this->getAdditionalProperty(self::PROPERTY_FREQUENCY_COUNT);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_frequency_interval(): ?int
    {
        return $this->getAdditionalProperty(self::PROPERTY_FREQUENCY_INTERVAL);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function get_frequency_options(): array
    {
        $translator = Translation::getInstance();
        $options = [];

        $options[self::FREQUENCY_DAILY] = $translator->getTranslation('Daily', [], 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_WEEKDAYS] = $translator->getTranslation('Weekdays', [], 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_WEEKLY] = $translator->getTranslation('Weekly', [], 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_BIWEEKLY] = $translator->getTranslation('BiWeekly', [], 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_MONTHLY] = $translator->getTranslation('Monthly', [], 'Chamilo\Libraries\Calendar');
        $options[self::FREQUENCY_YEARLY] = $translator->getTranslation('Yearly', [], 'Chamilo\Libraries\Calendar');

        return $options;
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function get_rank_options(): array
    {
        $translator = Translation::getInstance();
        $ranks = [];

        $ranks[0] = $translator->getTranslation('Every', [], 'Chamilo\Libraries\Calendar');
        $ranks[1] = $translator->getTranslation('First', [], 'Chamilo\Libraries\Calendar');
        $ranks[2] = $translator->getTranslation('Second', [], 'Chamilo\Libraries\Calendar');
        $ranks[3] = $translator->getTranslation('Third', [], 'Chamilo\Libraries\Calendar');
        $ranks[4] = $translator->getTranslation('Fourth', [], 'Chamilo\Libraries\Calendar');
        $ranks[5] = $translator->getTranslation('Fifth', [], 'Chamilo\Libraries\Calendar');
        $ranks[- 1] = $translator->getTranslation('Last', [], 'Chamilo\Libraries\Calendar');

        return $ranks;
    }

    /**
     * @throws \ReflectionException
     */
    public static function get_rank_string(int $rank): string
    {
        $translation = self::get_rank_options();

        return $translation[$rank];
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function get_type_string(): string
    {
        if ($this->has_frequency())
        {
            return Translation::getInstance()->getTranslation(
                'RepeatingCalendarEvent', [], 'Chamilo\Libraries\Calendar'
            );
        }
        else
        {
            return parent::get_type_string();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_until(): ?int
    {
        return $this->getAdditionalProperty(self::PROPERTY_UNTIL);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function has_frequency(): bool
    {
        $repeat = $this->get_frequency();

        return ($repeat != '0');
    }

    public function set_byday(?string $byday)
    {
        $this->setAdditionalProperty(self::PROPERTY_BYDAY, $byday);
    }

    public function set_bymonth(?string $bymonth)
    {
        $this->setAdditionalProperty(self::PROPERTY_BYMONTH, $bymonth);
    }

    public function set_bymonthday(?string $bymonthday)
    {
        $this->setAdditionalProperty(self::PROPERTY_BYMONTHDAY, $bymonthday);
    }

    public function set_frequency(?int $frequency)
    {
        $this->setAdditionalProperty(self::PROPERTY_FREQUENCY, $frequency);
    }

    public function set_frequency_count(?int $frequency_count)
    {
        $this->setAdditionalProperty(self::PROPERTY_FREQUENCY_COUNT, $frequency_count);
    }

    public function set_frequency_interval(?int $frequency_interval)
    {
        $this->setAdditionalProperty(self::PROPERTY_FREQUENCY_INTERVAL, $frequency_interval);
    }

    public function set_until(?int $until)
    {
        $this->setAdditionalProperty(self::PROPERTY_UNTIL, $until);
    }
}