<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivity extends DataClass
{
    public const ACTIVITY_CREATED = 1;
    public const ACTIVITY_DELETED = 2;
    public const ACTIVITY_EXPORTED = 3;
    public const ACTIVITY_IMPORTED = 4;
    public const ACTIVITY_PASSWORD_RESET = 5;
    public const ACTIVITY_QUOTA = 6;
    public const ACTIVITY_REGISTERED = 7;
    public const ACTIVITY_UPDATED = 8;

    public const PROPERTY_ACTION = 'action';
    public const PROPERTY_DATE = 'date';
    public const PROPERTY_SOURCE_USER_ID = 'source_user_id';
    public const PROPERTY_TARGET_USER_ID = 'target_user_id';

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
                self::PROPERTY_TARGET_USER_ID,
                self::PROPERTY_SOURCE_USER_ID
            ]
        );
    }

    public function getSourceUserIdentifier(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_SOURCE_USER_ID);
    }

    public static function getStorageUnitName(): string
    {
        return 'user_activity';
    }

    public function getTargetUserIdentifier(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_TARGET_USER_ID);
    }

    public function setAction(int $action): UserActivity
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action);

        return $this;
    }

    public function setDate(int $date): UserActivity
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);

        return $this;
    }

    public function setSourceUserIdentifier(?string $sourceUserIdentifier): UserActivity
    {
        $this->setDefaultProperty(self::PROPERTY_SOURCE_USER_ID, $sourceUserIdentifier);

        return $this;
    }

    public function setTargetUserIdentifier(?string $targetUserIdentifier): UserActivity
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_USER_ID, $targetUserIdentifier);

        return $this;
    }
}