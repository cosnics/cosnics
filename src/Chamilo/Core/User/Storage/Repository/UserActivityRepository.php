<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\Entity\UserActivity;
use Chamilo\Libraries\Storage\Repository\AbstractEntityRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserActivityRepository extends AbstractEntityRepository
{
    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function saveUserActivity(UserActivity $userActivity): void
    {
        $this->saveEntity($userActivity);
    }
}