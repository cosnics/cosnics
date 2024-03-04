<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Event\Recurrence
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait RecurringContentObjectTrait
{

    public static array $days = [1 => 'MO', 2 => 'TU', 3 => 'WE', 4 => 'TH', 5 => 'FR', 6 => 'SA', 7 => 'SU'];

    /**
     * @throws \Exception
     */
    public static function frequency_as_string(int $frequency): string
    {
        $translator = Translation::getInstance();

        switch ($frequency)
        {
            case RecurringContentObjectInterface::FREQUENCY_DAILY :
                $string = $translator->getTranslation('Daily', [], 'Chamilo\Libraries\Calendar');
                break;
            case RecurringContentObjectInterface::FREQUENCY_WEEKLY :
                $string = $translator->getTranslation('Weekly', [], 'Chamilo\Libraries\Calendar');
                break;
            case RecurringContentObjectInterface::FREQUENCY_MONTHLY :
                $string = $translator->getTranslation('Monthly', [], 'Chamilo\Libraries\Calendar');
                break;
            case RecurringContentObjectInterface::FREQUENCY_YEARLY :
                $string = $translator->getTranslation('Yearly', [], 'Chamilo\Libraries\Calendar');
                break;
            case RecurringContentObjectInterface::FREQUENCY_WEEKDAYS :
                $string = $translator->getTranslation('Weekdays', [], 'Chamilo\Libraries\Calendar');
                break;
            case RecurringContentObjectInterface::FREQUENCY_BIWEEKLY :
                $string = $translator->getTranslation('Biweekly', [], 'Chamilo\Libraries\Calendar');
                break;
            default:
                throw new Exception();
        }

        return $string;
    }

    public function frequency_is_indefinately(): bool
    {
        $repeat_to = $this->get_until();

        return ($repeat_to == 0);
    }

    abstract public function getAdditionalProperty(string $name): mixed;

    public function get_byday(): ?string
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYDAY);
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

    public function get_bymonth(): ?string
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYMONTH);
    }

    /**
     * @return string[]
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

    public static function get_bymonth_string(int $month): string
    {
        $translation = self::get_bymonth_options();

        return $translation[$month];
    }

    public function get_bymonthday(): ?string
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYMONTHDAY);
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

    public static function get_day_string(int $day_number): string
    {
        if (!is_numeric($day_number))
        {
            $day_number = self::get_day_format($day_number);
        }
        $translation = self::get_byday_options();

        return $translation[$day_number];
    }

    public function get_frequency(): ?int
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY);
    }

    /**
     * @throws \Exception
     */
    public function get_frequency_as_string(): string
    {
        return self::frequency_as_string($this->get_frequency());
    }

    public function get_frequency_count(): ?int
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY_COUNT);
    }

    public function get_frequency_interval(): ?int
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY_INTERVAL);
    }

    /**
     * @return string[]
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

    public static function get_rank_string(int $rank): string
    {
        $translation = self::get_rank_options();

        return $translation[$rank];
    }

    public function get_until(): ?int
    {
        return $this->getAdditionalProperty(RecurringContentObjectInterface::PROPERTY_UNTIL);
    }

    public function has_frequency(): bool
    {
        $repeat = $this->get_frequency();

        return ($repeat != '0');
    }

    abstract public function setAdditionalProperty(string $name, mixed $value): static;

    public function set_byday(?string $byday)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYDAY, $byday);
    }

    public function set_bymonth(?string $bymonth)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYMONTH, $bymonth);
    }

    public function set_bymonthday(?string $bymonthday)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_BYMONTHDAY, $bymonthday);
    }

    public function set_frequency(?int $frequency)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY, $frequency);
    }

    public function set_frequency_count(?int $frequency_count)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY_COUNT, $frequency_count);
    }

    public function set_frequency_interval(?int $frequency_interval)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_FREQUENCY_INTERVAL, $frequency_interval);
    }

    public function set_until(?int $until)
    {
        $this->setAdditionalProperty(RecurringContentObjectInterface::PROPERTY_UNTIL, $until);
    }
}