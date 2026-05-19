<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Dieter De Neef
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembership extends DataClass implements UuidDataClassInterface
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_GROUP_ID = 'group_id';
    public const string PROPERTY_USER_ID = 'user_id';

    public static function getAlias(): string
    {
        return 't_grp_mbs';
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_GROUP_ID, self::PROPERTY_USER_ID]);
    }

    public function getGroupId(): string
    {
        $identifier = $this->getDefaultProperty(self::PROPERTY_GROUP_ID);

        if ($identifier) {
            $identifier = Uuid::fromString($identifier)->toString();
        }

        return $identifier;
    }

    public function getId(): ?string
    {
        $identifier = $this->getDefaultProperty(self::PROPERTY_ID);

        if ($identifier) {
            $identifier = Uuid::fromString($identifier)->toString();
        }

        return $identifier;
    }

    public static function getStorageUnitName(): string
    {
        return 'group_group_rel_user';
    }

    public function getUserId(): string
    {
        $identifier = $this->getDefaultProperty(self::PROPERTY_USER_ID);

        if ($identifier) {
            $identifier = Uuid::fromString($identifier)->toString();
        }

        return $identifier;
    }

    public function setGroupId($groupIdentifier): static
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $groupIdentifier);

        return $this;
    }

    public function setUserId($userIdentifier): static
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);

        return $this;
    }
}
