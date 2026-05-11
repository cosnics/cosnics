<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserActivity;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivityRepository extends EntityRepository
{
    public function saveUserActivity(UserActivity $userActivity): void
    {
        $this->getEntityManager()->persist($userActivity);
        $this->getEntityManager()->flush();
    }
}