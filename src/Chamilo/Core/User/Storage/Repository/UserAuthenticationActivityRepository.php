<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserAuthenticationActivity;
use Doctrine\ORM\EntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserAuthenticationActivityRepository extends EntityRepository
{
    public function saveUserAuthenticationActivity(UserAuthenticationActivity $userAuthenticationActivity): void
    {
        $this->getEntityManager()->persist($userAuthenticationActivity);
        $this->getEntityManager()->flush();
    }
}