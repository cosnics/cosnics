<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Architecture\Trait\SingleCacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class OwnedCalendarsCacheService
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
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getOwnedCalendars(User $user): array
    {
        $cacheIdentifier = $this->getCacheKeyForParts([__METHOD__, $user->getId()]);

        if (!$this->hasCacheDataForKey($cacheIdentifier))
        {
            $lifetimeInMinutes = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Core\Admin', 'refresh_external'
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $this->getCalendarRepository()->findOwnedCalendars($user), $lifetimeInMinutes * 60
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