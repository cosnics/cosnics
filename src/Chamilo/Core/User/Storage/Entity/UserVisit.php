<?php
namespace Chamilo\Core\User\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\User\Storage\Repository\UserTrackingEntityRepository')]
#[ORM\Table(name: 'user_visit')]
class UserVisit
{
    public const string PROPERTY_ENTER_DATE = 'enter_date';
    public const string PROPERTY_LEAVE_DATE = 'leave_date';
    public const string PROPERTY_LOCATION = 'location';
    public const string PROPERTY_USER_ID = 'user_id';

    /**
     * @ORM\Column(name="enter_date", type="integer")
     */
    protected int $enterDate;

    /**
     * @ORM\Column(name="id", type="string", nullable=true)
     */
    protected ?string $identifier = null;

    /**
     * @ORM\Column(name="leave_date", type="integer", nullable=true)
     */
    protected ?int $leaveDate;

    /**
     * @ORM\Column(name="location", type="string")
     */
    protected string $location;

    /**
     * @ORM\Column(name="user_id", type="string")
     */
    protected string $userIdentifier;

    public function getEnterDate(): int
    {
        return $this->enterDate;
    }

    public function setEnterDate(int $enterDate): static
    {
        $this->enterDate = $enterDate;

        return $this;
    }

    public function getLeaveDate(): ?int
    {
        return $this->leaveDate;
    }

    public function setLeaveDate(?int $leaveDate): static
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

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(?string $userIdentifier): static
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }
}
