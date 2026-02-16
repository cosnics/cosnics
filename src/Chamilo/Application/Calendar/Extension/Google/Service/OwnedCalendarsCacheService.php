<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserService;
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

    protected int $defaultLifetime;

    protected User $user;

    protected UserService $userService;

    private CalendarRepository $calendarRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, CalendarRepository $calendarRepository, User $user, UserService $userService,
        int $defaultLifetime = 3600
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->calendarRepository = $calendarRepository;
        $this->user = $user;
        $this->userService = $userService;
        $this->defaultLifetime = $defaultLifetime;
    }

    public function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    public function getDefaultLifetime(): int
    {
        return $this->defaultLifetime;
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getOwnedCalendars(User $user): array
    {
        $cacheIdentifier = $this->getCacheKeyForParts([__METHOD__, $user->getId()]);

        if (!$this->hasCacheDataForKey($cacheIdentifier)) {
            $lifetime = $this->getUserService()->findUserSetting(
                $this->getUser(), 'cosnics.libraries.storage.cache.external.defaultLifetime',
                $this->getDefaultLifetime()
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $this->getCalendarRepository()->findOwnedCalendars($user), $lifetime
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}