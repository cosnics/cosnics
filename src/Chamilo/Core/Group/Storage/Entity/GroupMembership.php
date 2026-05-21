<?php
namespace Chamilo\Core\Group\Storage\Entity;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Entity
 * @author  Dieter De Neef
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\Group\Storage\Repository\GroupMembershipEntityRepository')]
#[ORM\Table(name: 'group_group_rel_user')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class GroupMembership implements DoctrineEntityInterface
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_GROUP_ID = 'groupIdentifier';
    public const string PROPERTY_USER_ID = 'userIdentifier';
    public const string PROPERTY_GROUP = 'group';
    public const string PROPERTY_USER = 'user';

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: 'group_id', nullable: false)]
    protected Group $group;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    protected User $user;

    public static function getAlias(): string
    {
        return 't_grp_mbs';
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): GroupMembership
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
