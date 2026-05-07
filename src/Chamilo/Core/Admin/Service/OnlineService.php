<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Storage\DataClass\Online;
use Chamilo\Core\Admin\Storage\Repository\OnlineRepository;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class OnlineService
{
    public function __construct(protected OnlineRepository $onlineRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function createOnlineForUserIdentifierAndLastAccessDate(string $userIdentifier, int $lastAccessDate): void
    {
        $online = new Online();

        $online->setUserId($userIdentifier);
        $online->setLastAccessDate($lastAccessDate);

        $this->onlineRepository->createOnline($online);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findDistinctOnlineUserIdentifiers(): array
    {
        return $this->onlineRepository->findDistinctOnlineUserIdentifiers();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findOnlineForUserIdentifier(string $userIdentifier): ?Online
    {
        return $this->onlineRepository->findOnlineForUserIdentifier($userIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateOnline(Online $online): void
    {
        $this->onlineRepository->updateOnline($online);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     */
    public function updateOnlineForUserIdentifierWithCurrentTime(?string $userIdentifier): void
    {
        $time = time();

        try {
            $online = $this->findOnlineForUserIdentifier($userIdentifier);

            $online->setUserId($userIdentifier);
            $online->setLastAccessDate($time);

            $this->updateOnline($online);
        }
        catch (StorageNoResultException) {
            $this->createOnlineForUserIdentifierAndLastAccessDate($userIdentifier, $time);
        }
    }
}