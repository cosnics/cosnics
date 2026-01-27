<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\DataClass\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;

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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUserActivity(UserActivity $userActivity): bool
    {
        return $this->getDataClassRepository()->create($userActivity);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUserAuthenticationActivity(UserAuthenticationActivity $userAuthenticationActivity): bool
    {
        return $this->getDataClassRepository()->create($userAuthenticationActivity);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createUserVisit(UserVisit $userVisit): bool
    {
        return $this->getDataClassRepository()->create($userVisit);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function findUserVisitByIdentifier(string $userVisitIdentifier): ?UserVisit
    {
        return $this->getDataClassRepository()->retrieveById(UserVisit::class, $userVisitIdentifier);
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserVisit(UserVisit $userVisit): bool
    {
        return $this->getDataClassRepository()->update($userVisit);
    }

}