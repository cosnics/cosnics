<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserAuthenticationActivity;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserAuthenticationActivityRepository extends AbstractEntityRepository
{
    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserAuthenticationActivity(UserAuthenticationActivity $userAuthenticationActivity): void
    {
        $this->saveEntity($userAuthenticationActivity);
    }
}