<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;

class SubscribedUser extends User
{
    public const PROPERTY_RELATION_ID = 'relation_id';
    public const PROPERTY_GROUP_ID = 'group_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_RELATION_ID;
        $extendedPropertyNames[] = self::PROPERTY_GROUP_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getRelationId()
    {
        return $this->getDefaultProperty(self::PROPERTY_RELATION_ID);
    }

    public function getGroupId()
    {
        return $this->getDefaultProperty(self::PROPERTY_GROUP_ID);
    }
}