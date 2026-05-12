<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\Entity\User;
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

    public function __construct(
        protected readonly AdapterInterface $cacheAdapter, protected CalendarRepository $calendarRepository,
        protected UserSettingsService $userSettingsService, protected int $defaultLifetime = 3600
    )
    {
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getOwnedCalendars(?User $user = null): array
    {
        if (!$user instanceof User) {
            return [];
        }

        $cacheIdentifier = $this->getCacheKeyForParts([__METHOD__, $user->getIdentifier()->toString()]);

        if (!$this->hasCacheDataForKey($cacheIdentifier)) {
            $lifetime = $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.storage.cache.external.defaultLifetime', $this->defaultLifetime
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $this->calendarRepository->findOwnedCalendars($user), $lifetime
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }
}