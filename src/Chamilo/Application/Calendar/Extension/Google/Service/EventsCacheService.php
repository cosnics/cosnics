<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Trait\SingleCacheAdapterHandlerTrait;
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

    protected UserService $userService;

    private CalendarRepository $calendarRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, CalendarRepository $calendarRepository, UserService $userService
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->calendarRepository = $calendarRepository;
        $this->userService = $userService;
    }

    public function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, $fromDate, $toDate
    ): Google_Service_Calendar_Events
    {
        $calendarRepository = $this->getCalendarRepository();

        $cacheIdentifier = $this->getCacheKeyForParts(
            [__METHOD__, $user->getId(), $calendarIdentifier, $fromDate, $toDate]
        );

        if (!$this->hasCacheDataForKey($cacheIdentifier)) {
            $lifetimeInMinutes = $this->getUserService()->findUserSetting(
                $user, 'Chamilo\Core\Admin', 'DefaultLifetime'
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $user, $calendarIdentifier, $fromDate, $toDate
            ), $lifetimeInMinutes * 60
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}