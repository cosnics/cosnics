<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTrackingRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function createUserActivity(UserActivity $userActivity): bool
    {
        return $this->getDataClassRepository()->create($userActivity);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

}