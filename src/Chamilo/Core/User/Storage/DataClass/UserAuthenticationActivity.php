<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserAuthenticationActivity extends DataClass
{
    public const ACTIVITY_LOGIN = 1;
    public const ACTIVITY_LOGOUT = 2;

    public const PROPERTY_ACTION = 'action';
    public const PROPERTY_DATE = 'date';
    public const PROPERTY_IP = 'ip';
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
                self::PROPERTY_USER_ID,
                self::PROPERTY_IP
            ]
        );
    }

    public function getIp(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_IP);
    }

    public static function getStorageUnitName(): string
    {
        return 'user_authentication_activity';
    }

    public function getUserIdentifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setAction(int $action): UserAuthenticationActivity
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action);

        return $this;
    }

    public function setDate(int $date): UserAuthenticationActivity
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);

        return $this;
    }

    public function setIp(?string $ip): UserAuthenticationActivity
    {
        $this->setDefaultProperty(self::PROPERTY_IP, $ip);

        return $this;
    }

    public function setUserIdentifier(?string $userIdentifier): UserAuthenticationActivity
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);

        return $this;
    }
}