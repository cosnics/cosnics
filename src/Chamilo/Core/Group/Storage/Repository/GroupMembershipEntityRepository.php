<?php
namespace Chamilo\Core\Group\Storage\Repository;

use Chamilo\Core\Group\Storage\Entity\Group;
use Chamilo\Core\Group\Storage\Entity\GroupMembership;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupMembershipEntityRepository extends AbstractEntityRepository
{
    public function findGroupMembershipByIdentifier()
    {
        try {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();

            $queryBuilder->select(GroupMembership::getAlias(), Group::getAlias(), User::getAlias());
            $queryBuilder->from(GroupMembership::class, GroupMembership::getAlias());
            $queryBuilder->innerJoin(GroupMembership::getAlias() . '.group', Group::getAlias());
            $queryBuilder->innerJoin(GroupMembership::getAlias() . '.user', User::getAlias());

            $query = $queryBuilder->getQuery();
            $result = $query->setMaxResults(1)->getOneOrNullResult();

            dump($query->getDQL(), $query->getSQL(), $result);

        }
        catch (NoSuchObjectException $exception) {
            exit;
        }
    }
}