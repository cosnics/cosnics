<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserVisit;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTrackingEntityRepository extends EntityRepository
{
    public function saveUserVisit(UserVisit $userVisit): void
    {
        $this->getEntityManager()->persist($userVisit);
        $this->getEntityManager()->flush();
    }
}