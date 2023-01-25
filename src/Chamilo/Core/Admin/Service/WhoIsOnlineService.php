<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Repository\WhoIsOnlineRepository;
use Chamilo\Core\Admin\Storage\DataClass\Online;

/**
 * @package Chamilo\Core\Admin\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WhoIsOnlineService
{
    protected WhoIsOnlineRepository $whoIsOnlineRepository;

    public function __construct(WhoIsOnlineRepository $whoIsOnlineRepository)
    {
        $this->whoIsOnlineRepository = $whoIsOnlineRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createWhoIsOnlineForUserIdentifierAndLastAccessDate(int $userIdentifier, int $lastAccessDate): bool
    {
        $online = new Online();

        $online->setUserId($userIdentifier);
        $online->setLastAccessDate($lastAccessDate);

        return $this->getWhoIsOnlineRepository()->createWhoIsOnline($online);
    }

    public function findDistinctOnlineUserIdentifiers()
    {
        return $this->getWhoIsOnlineRepository()->findDistinctOnlineUserIdentifiers();
    }

    public function findWhoIsOnlineForUserIdentifier(int $userIdentifier): ?Online
    {
        return $this->getWhoIsOnlineRepository()->findWhoIsOnlineForUserIdentifier($userIdentifier);
    }

    public function getWhoIsOnlineRepository(): WhoIsOnlineRepository
    {
        return $this->whoIsOnlineRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWhoIsOnline(Online $online): bool
    {
        return $this->getWhoIsOnlineRepository()->updateWhoIsOnline($online);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWhoIsOnlineForUserIdentifierWithCurrentTime(?int $userIdentifier): bool
    {
        $time = time();

        $online = $this->findWhoIsOnlineForUserIdentifier($userIdentifier);

        if ($online instanceof Online)
        {
            $online->setUserId($userIdentifier);
            $online->setLastAccessDate($time);

            return $this->updateWhoIsOnline($online);
        }
        else
        {
            return $this->createWhoIsOnlineForUserIdentifierAndLastAccessDate($userIdentifier, $time);
        }
    }
}