<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Storage\DataClass\Online;
use Chamilo\Core\Admin\Storage\Repository\OnlineRepository;
use Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OnlineService
{
    protected OnlineRepository $onlineRepository;

    public function __construct(OnlineRepository $onlineRepository)
    {
        $this->onlineRepository = $onlineRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function createOnlineForUserIdentifierAndLastAccessDate(string $userIdentifier, int $lastAccessDate): bool
    {
        $online = new Online();

        $online->setUserId($userIdentifier);
        $online->setLastAccessDate($lastAccessDate);

        return $this->getOnlineRepository()->createOnline($online);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findDistinctOnlineUserIdentifiers(): array
    {
        return $this->getOnlineRepository()->findDistinctOnlineUserIdentifiers();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findOnlineForUserIdentifier(string $userIdentifier): ?Online
    {
        return $this->getOnlineRepository()->findOnlineForUserIdentifier($userIdentifier);
    }

    public function getOnlineRepository(): OnlineRepository
    {
        return $this->onlineRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateOnline(Online $online): bool
    {
        return $this->getOnlineRepository()->updateOnline($online);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function updateOnlineForUserIdentifierWithCurrentTime(?string $userIdentifier): bool
    {
        $time = time();

        try
        {
            $online = $this->findOnlineForUserIdentifier($userIdentifier);

            $online->setUserId($userIdentifier);
            $online->setLastAccessDate($time);

            return $this->updateOnline($online);
        }
        catch (StorageNoResultException)
        {
            return $this->createOnlineForUserIdentifierAndLastAccessDate($userIdentifier, $time);
        }
    }
}