<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\Entity\User;
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

    public function __construct(
        protected readonly AdapterInterface $cacheAdapter, protected CalendarRepository $calendarRepository,
        protected UserSettingsService $userSettingsService, protected int $defaultLifetime = 3600
    )
    {
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, $fromDate, $toDate
    ): Google_Service_Calendar_Events
    {
        $cacheIdentifier = $this->getCacheKeyForParts(
            [__METHOD__, $user->getIdentifier()->toString(), $calendarIdentifier, $fromDate, $toDate]
        );

        if (!$this->hasCacheDataForKey($cacheIdentifier)) {
            $lifetime = $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.storage.cache.external.defaultLifetime', $this->defaultLifetime
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $this->calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $user, $calendarIdentifier, $fromDate, $toDate
            ), $lifetime
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }
}