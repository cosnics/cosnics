<?php
namespace Chamilo\Core\User\Storage\Entity;

use Chamilo\Core\User\Architecture\Enum\UserActivityTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\User\Storage\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\User\Storage\Repository\UserActivityRepository')]
#[ORM\Table(name: 'user_activity')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class UserActivity implements DoctrineEntityInterface
{
    public const string PROPERTY_ACTION = 'action';
    public const string PROPERTY_DATE = 'date';
    public const string PROPERTY_SOURCE_USER_ID = 'source_user_id';
    public const string PROPERTY_TARGET_USER_ID = 'target_user_id';

    #[ORM\Column(name: 'action', enumType: UserActivityTypeEnum::class)]
    protected UserActivityTypeEnum $action;

    #[ORM\Column(name: 'date', type: 'integer', nullable: true)]
    protected int $date;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'source_user_id', type: 'uuid', nullable: true)]
    protected ?Uuid $sourceUserIdentifier;

    #[ORM\Column(name: 'target_user_id', type: 'uuid', nullable: true)]
    protected ?Uuid $targetUserIdentifier;

    public function __construct()
    {
        $this->setIdentifier(new UuidV7());
    }

    public function getAction(): UserActivityTypeEnum
    {
        return $this->action;
    }

    public function setAction(UserActivityTypeEnum $action): UserActivity
    {
        $this->action = $action;

        return $this;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): UserActivity
    {
        $this->date = $date;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): UserActivity
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getSourceUserIdentifier(): ?Uuid
    {
        return $this->sourceUserIdentifier;
    }

    public function setSourceUserIdentifier(?Uuid $sourceUserIdentifier): UserActivity
    {
        $this->sourceUserIdentifier = $sourceUserIdentifier;

        return $this;
    }

    public function getTargetUserIdentifier(): ?Uuid
    {
        return $this->targetUserIdentifier;
    }

    public function setTargetUserIdentifier(?Uuid $targetUserIdentifier): UserActivity
    {
        $this->targetUserIdentifier = $targetUserIdentifier;

        return $this;
    }
}