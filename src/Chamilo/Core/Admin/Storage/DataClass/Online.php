<?php
namespace Chamilo\Core\Admin\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class Online extends DataClass
{
    public const PARAM_TIME = 'time';
    public const PROPERTY_LAST_ACCESS_DATE = 'last_access_date';
    public const PROPERTY_USER_ID = 'user_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_USER_ID, self::PROPERTY_LAST_ACCESS_DATE]);
    }

    public function getLastAccessDate(): ?int
    {
        return $this->getDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE);
    }

    public static function getStorageUnitName(): string
    {
        return 'admin_online';
    }

    public function getUserId(): ?int
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setLastAccessDate(int $lastAccessDate)
    {
        $this->setDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE, $lastAccessDate);
    }

    public function setUserId(int $userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }
}
