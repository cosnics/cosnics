<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupActivity extends DataClass
{
    public const ACTIVITY_CREATED = 1;
    public const ACTIVITY_DELETED = 2;
    public const ACTIVITY_MOVED = 4;
    public const ACTIVITY_SUBSCRIBED = 5;
    public const ACTIVITY_TRUNCATED = 3;
    public const ACTIVITY_UNSUBSCRIBED = 6;
    public const ACTIVITY_UPDATED = 7;

    public const PROPERTY_ACTION = 'action';
    public const PROPERTY_DATE = 'date';
    public const PROPERTY_GROUP_ID = 'reference_id';
    public const PROPERTY_TARGET_USER_ID = 'target_user_id';
    public const PROPERTY_USER_ID = 'user_id';

    public function getAction(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTION);
    }

    public function getDate(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ACTION,
                self::PROPERTY_DATE,
                self::PROPERTY_GROUP_ID,
                self::PROPERTY_TARGET_USER_ID,
                self::PROPERTY_USER_ID
            ]
        );
    }

    public function getGroupIdentifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }

    public static function getStorageUnitName(): string
    {
        return 'group_activity';
    }

    public function getTargetUserIdentifier(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_USER_ID);
    }

    public function getUserIdentifier(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setAction(int $action): GroupActivity
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action);

        return $this;
    }

    public function setDate(int $date): GroupActivity
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);

        return $this;
    }

    public function setGroupIdentifier(string $groupIdentifier): GroupActivity
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $groupIdentifier);

        return $this;
    }

    public function setTargetUserIdentifier(?string $targetUserIdentifier): GroupActivity
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_USER_ID, $targetUserIdentifier);

        return $this;
    }

    public function setUserIdentifier(?string $userIdentifier): GroupActivity
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);

        return $this;
    }
}