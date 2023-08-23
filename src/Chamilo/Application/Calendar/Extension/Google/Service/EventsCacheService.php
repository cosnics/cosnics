<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\SingleCacheAdapterHandlerTrait;
use Google_Service_Calendar_Events;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventsCacheService
{
    use SingleCacheAdapterHandlerTrait;

    protected User $user;

    protected UserSettingService $userSettingService;

    private CalendarRepository $calendarRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, CalendarRepository $calendarRepository, User $user,
        UserSettingService $userSettingService
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->calendarRepository = $calendarRepository;
        $this->user = $user;
        $this->userSettingService = $userSettingService;
    }

    public function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(string $calendarIdentifier, $fromDate, $toDate
    ): Google_Service_Calendar_Events
    {
        $calendarRepository = $this->getCalendarRepository();

        $cacheIdentifier = $this->getCacheKeyForParts(
            [$calendarRepository->getAccessToken(), __METHOD__, $calendarIdentifier, $fromDate, $toDate]
        );

        if (!$this->hasCacheDataForKey($cacheIdentifier))
        {
            $lifetimeInMinutes = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'refresh_external'
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $calendarIdentifier, $fromDate, $toDate
            ), $lifetimeInMinutes * 60
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }
}