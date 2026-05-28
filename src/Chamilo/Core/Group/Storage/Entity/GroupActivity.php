<?php
namespace Chamilo\Core\Group\Storage\Entity;

use Chamilo\Core\Group\Architecture\Enum\GroupActivityTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\Group\Storage\Repository\GroupActivityRepository')]
#[ORM\Table(name: 'group_activity')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class GroupActivity implements DoctrineEntityInterface
{
    public const string PROPERTY_ACTION = 'action';
    public const string PROPERTY_DATE = 'date';
    public const string PROPERTY_GROUP_ID = 'groupIdentifier';
    public const string PROPERTY_TARGET_USER_ID = 'targetUserIdentifier';
    public const string PROPERTY_USER_ID = 'userIdentifier';

    #[ORM\Column(name: 'action', enumType: GroupActivityTypeEnum::class)]
    protected GroupActivityTypeEnum $action;

    #[ORM\Column(name: 'date', type: 'integer', nullable: true)]
    protected int $date;

    #[ORM\Column(name: 'reference_id', type: 'uuid')]
    protected Uuid $groupIdentifier;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'target_user_id', type: 'uuid', nullable: true)]
    protected ?Uuid $targetUserIdentifier;

    #[ORM\Column(name: 'user_id', type: 'uuid', nullable: true)]
    protected ?Uuid $userIdentifier;

    public function getAction(): GroupActivityTypeEnum
    {
        return $this->action;
    }

    public function setAction(GroupActivityTypeEnum $action): static
    {
        $this->action = $action;

        return $this;
    }

    public static function getAlias(): string
    {
        return 't_grp_atv';
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getGroupIdentifier(): Uuid
    {
        return $this->groupIdentifier;
    }

    public function setGroupIdentifier(Uuid $groupIdentifier): static
    {
        $this->groupIdentifier = $groupIdentifier;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getTargetUserIdentifier(): ?string
    {
        return $this->targetUserIdentifier;
    }

    public function setTargetUserIdentifier(?Uuid $targetUserIdentifier): static
    {
        $this->targetUserIdentifier = $targetUserIdentifier;

        return $this;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(?Uuid $userIdentifier): static
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }
}