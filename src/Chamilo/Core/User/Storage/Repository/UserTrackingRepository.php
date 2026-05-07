<?php
namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException;
use Chamilo\Core\User\Storage\DataClass\UserActivity;
use Chamilo\Core\User\Storage\DataClass\UserAuthenticationActivity;
use Chamilo\Core\User\Storage\DataClass\UserVisit;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTrackingRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createUserActivity(UserActivity $userActivity): void
    {
        $this->dataClassRepository->create($userActivity);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createUserAuthenticationActivity(UserAuthenticationActivity $userAuthenticationActivity): void
    {
        $this->dataClassRepository->create($userAuthenticationActivity);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createUserVisit(UserVisit $userVisit): void
    {
        $this->dataClassRepository->create($userVisit);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserVisitException
     */
    public function findUserVisitByIdentifier(string $userVisitIdentifier): ?UserVisit
    {
        try {
            return $this->dataClassRepository->retrieveById(UserVisit::class, $userVisitIdentifier);
        }
        catch (StorageNoResultException) {
            throw new NoSuchUserVisitException([DataClass::PROPERTY_ID => $userVisitIdentifier]);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateUserVisit(UserVisit $userVisit): void
    {
        $this->dataClassRepository->update($userVisit);
    }
}