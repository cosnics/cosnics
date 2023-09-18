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

    public function createWhoIsOnlineForUserIdentifierAndLastAccessDate(string $userIdentifier, int $lastAccessDate
    ): bool
    {
        $online = new Online();

        $online->setUserId($userIdentifier);
        $online->setLastAccessDate($lastAccessDate);

        return $this->getWhoIsOnlineRepository()->createWhoIsOnline($online);
    }

    /**
     * @return string[]
     */
    public function findDistinctOnlineUserIdentifiers(): array
    {
        return $this->getWhoIsOnlineRepository()->findDistinctOnlineUserIdentifiers();
    }

    public function findWhoIsOnlineForUserIdentifier(string $userIdentifier): ?Online
    {
        return $this->getWhoIsOnlineRepository()->findWhoIsOnlineForUserIdentifier($userIdentifier);
    }

    public function getWhoIsOnlineRepository(): WhoIsOnlineRepository
    {
        return $this->whoIsOnlineRepository;
    }

    public function updateWhoIsOnline(Online $online): bool
    {
        return $this->getWhoIsOnlineRepository()->updateWhoIsOnline($online);
    }

    public function updateWhoIsOnlineForUserIdentifierWithCurrentTime(?string $userIdentifier): bool
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