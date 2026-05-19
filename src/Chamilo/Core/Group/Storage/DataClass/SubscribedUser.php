<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribedUser extends User
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_GROUP_ID = 'group_id';
    public const string PROPERTY_RELATION_ID = 'relation_id';

    #[ORM\Column(name: 'group_id', type: 'string')]
    protected ?string $groupIdentifier;

    #[ORM\Column(name: 'relation_id', type: 'string')]
    protected ?string $relationIdentifier;

    public function getGroupId(): string
    {
        return $this->groupIdentifier;
    }

    public function getRelationId(): string
    {
        return $this->relationIdentifier;
    }
}