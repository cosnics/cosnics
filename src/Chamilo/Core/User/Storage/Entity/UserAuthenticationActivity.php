<?php
namespace Chamilo\Core\User\Storage\Entity;

use Chamilo\Core\User\Architecture\Enum\UserAuthenticationActivityTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\User\Storage\Repository\UserAuthenticationActivityRepository')]
#[ORM\Table(name: 'user_authentication_activity')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class UserAuthenticationActivity implements DoctrineEntityInterface
{
    public const int ACTIVITY_LOGIN = 1;
    public const int ACTIVITY_LOGOUT = 2;
    public const string PROPERTY_ACTION = 'action';
    public const string PROPERTY_DATE = 'date';
    public const string PROPERTY_IP = 'ip';
    public const string PROPERTY_USER_ID = 'user_id';

    #[ORM\Column(name: 'action', enumType: UserAuthenticationActivityTypeEnum::class)]
    protected UserAuthenticationActivityTypeEnum $action;

    #[ORM\Column(name: 'date', type: 'integer', nullable: true)]
    protected int $date;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'ip', type: 'string', nullable: true)]
    protected ?string $ip;

    #[ORM\Column(name: 'user_id', type: 'uuid')]
    protected Uuid $userIdentifier;

    public function __construct()
    {
        $this->setIdentifier(new UuidV7());
    }

    public function getAction(): UserAuthenticationActivityTypeEnum
    {
        return $this->action;
    }

    public function setAction(UserAuthenticationActivityTypeEnum $action): UserAuthenticationActivity
    {
        $this->action = $action;

        return $this;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): UserAuthenticationActivity
    {
        $this->date = $date;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): UserAuthenticationActivity
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): UserAuthenticationActivity
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUserIdentifier(): Uuid
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(Uuid $userIdentifier): UserAuthenticationActivity
    {
        $this->userIdentifier = $userIdentifier;

        return $this;
    }
}