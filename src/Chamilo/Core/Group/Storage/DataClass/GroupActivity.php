<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Architecture\Enum\GroupActivityTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupActivity extends DataClass implements UuidDataClassInterface
{
    public const string PROPERTY_ACTION = 'action';
    public const string PROPERTY_DATE = 'date';
    public const string PROPERTY_GROUP_ID = 'reference_id';
    public const string PROPERTY_TARGET_USER_ID = 'target_user_id';
    public const string PROPERTY_USER_ID = 'user_id';

    public function getAction(): GroupActivityTypeEnum
    {
        return GroupActivityTypeEnum::from($this->getDefaultProperty(self::PROPERTY_ACTION));
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

    public function setAction(GroupActivityTypeEnum $action): static
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action->value);

        return $this;
    }

    public function setDate(int $date): static
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);

        return $this;
    }

    public function setGroupIdentifier(string $groupIdentifier): static
    {
        $this->setDefaultProperty(self::PROPERTY_GROUP_ID, $groupIdentifier);

        return $this;
    }

    public function setTargetUserIdentifier(?string $targetUserIdentifier): static
    {
        $this->setDefaultProperty(self::PROPERTY_TARGET_USER_ID, $targetUserIdentifier);

        return $this;
    }

    public function setUserIdentifier(?string $userIdentifier): static
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userIdentifier);

        return $this;
    }
}