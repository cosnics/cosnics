<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserVisit extends DataClass
{

    public const PROPERTY_ENTER_DATE = 'enter_date';
    public const PROPERTY_LEAVE_DATE = 'leave_date';
    public const PROPERTY_LOCATION = 'location';
    public const PROPERTY_USER_ID = 'user_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ENTER_DATE,
                self::PROPERTY_LEAVE_DATE,
                self::PROPERTY_LOCATION,
                self::PROPERTY_USER_ID
            ]
        );
    }

    public function getEnterDate(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTER_DATE);
    }

    public function getLeaveDate(): ?int
    {
        return $this->getDefaultProperty(self::PROPERTY_LEAVE_DATE);
    }

    public function getLocation(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION);
    }

    public static function getStorageUnitName(): string
    {
        return 'user_visit';
    }

    public function getUserIdentifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setEnterDate(int $enterDate): UserVisit
    {
        $this->setDefaultProperty(self::PROPERTY_ENTER_DATE, $enterDate);

        return $this;
    }

    public function setLeaveDate(?int $leaveDate): UserVisit
    {
        $this->setDefaultProperty(self::PROPERTY_LEAVE_DATE, $leaveDate);

        return $this;
    }

    public function setLocation(string $location): UserVisit
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION, $location);

        return $this;
    }

    public function setUserIdentifier(?string $userIdentifier): UserVisit
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);

        return $this;
    }

}
