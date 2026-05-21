<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupEntityRepository extends AbstractEntityRepository
{
    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function getGroupReference(Uuid $groupIdentifier): Group
    {
        return $this->getEntityManager()->getReference(Group::class, $groupIdentifier);
    }
}