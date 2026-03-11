<?php
namespace Chamilo\Application\Calendar\Storage\DataClass;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;

/**
 * @package Chamilo\Application\Calendar\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Availability extends DataClass implements UuidDataClassInterface
{
    public const string CONTEXT = Manager::CONTEXT;

    public const string PROPERTY_AVAILABILITY = 'availability';
    public const string PROPERTY_CALENDAR_ID = 'calendar_id';
    public const string PROPERTY_CALENDAR_TYPE = 'calendar_type';
    public const string PROPERTY_COLOUR = 'colour';
    public const string PROPERTY_USER_ID = 'user_id';

    public function getAvailability(): bool
    {
        return $this->getDefaultProperty(self::PROPERTY_AVAILABILITY);
    }

    public function getCalendarId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_ID);
    }

    public function getCalendarType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CALENDAR_TYPE);
    }

    public function getColour(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_COLOUR);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_CALENDAR_TYPE,
                self::PROPERTY_CALENDAR_ID,
                self::PROPERTY_AVAILABILITY,
                self::PROPERTY_COLOUR
            ]
        );
    }

    public static function getStorageUnitName(): string
    {
        return 'calendar_availability';
    }

    public function getUserId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function isActive(): bool
    {
        return $this->getAvailability() == true;
    }

    public function isInactive(): bool
    {
        return $this->getAvailability() == false;
    }

    public function setAvailability(bool $availability): static
    {
        $this->setDefaultProperty(self::PROPERTY_AVAILABILITY, $availability);

        return $this;
    }

    public function setCalendarId(string $calendarId): static
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_ID, $calendarId);

        return $this;
    }

    public function setCalendarType(string $calendarType): static
    {
        $this->setDefaultProperty(self::PROPERTY_CALENDAR_TYPE, $calendarType);

        return $this;
    }

    public function setColour(string $colour): static
    {
        $this->setDefaultProperty(self::PROPERTY_COLOUR, $colour);

        return $this;
    }

    public function setUserId(string $userId): static
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);

        return $this;
    }
}
