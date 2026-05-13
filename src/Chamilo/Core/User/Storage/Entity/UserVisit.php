<?php
namespace Chamilo\Core\User\Storage\Entity;

use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\User\Storage\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\User\Storage\Repository\UserVisitRepository')]
#[ORM\Table(name: 'user_visit')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class UserVisit implements DoctrineEntityInterface
{
    public const string PROPERTY_ENTER_DATE = 'enter_date';
    public const string PROPERTY_LEAVE_DATE = 'leave_date';
    public const string PROPERTY_LOCATION = 'location';
    public const string PROPERTY_USER_ID = 'user_id';

    #[ORM\Column(name: 'enter_date', type: 'integer')]
    protected int $enterDate;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'leave_date', type: 'integer', nullable: true)]
    protected int|null $leaveDate;

    #[ORM\Column(name: 'location', type: 'string')]
    protected string $location;

    #[ORM\Column(name: 'user_id', type: 'uuid')]
    protected Uuid $userIdentifier;

    public function __construct()
    {
        $this->setIdentifier(new UuidV7());
    }

    public function getEnterDate(): int
    {
        return $this->enterDate;
    }

    public function setEnterDate(int $enterDate): static
    {
        $this->enterDate = $enterDate;

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

    public function getLeaveDate(): int|null
    {
        return $this->leaveDate;
    }

    public function setLeaveDate(int|null $leaveDate): static
    {
        $this->leaveDate = $leaveDate;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getUserIdentifier(): Uuid
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(Uuid|null $userIdentifier): static
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }
}
