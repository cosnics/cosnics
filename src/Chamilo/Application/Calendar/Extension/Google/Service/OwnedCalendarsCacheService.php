<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class OwnedCalendarsCacheService
{
    use CacheAdapterHandlerTrait;

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
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getOwnedCalendars(): array
    {
        $calendarRepository = $this->getCalendarRepository();

        $cacheIdentifier = $this->getCacheKeyForParts([$calendarRepository->getAccessToken(), __METHOD__]);

        if (!$this->hasCacheDataForKey($cacheIdentifier))
        {
            $lifetimeInMinutes = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries\Calendar', 'refresh_external'
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $this->getCalendarRepository()->findOwnedCalendars(), $lifetimeInMinutes * 60
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