<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingsService;
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

    protected int $defaultLifetime;

    protected UserSettingsService $userSettingsService;

    private CalendarRepository $calendarRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, CalendarRepository $calendarRepository,
        UserSettingsService $userSettingsService, int $defaultLifetime = 3600
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->calendarRepository = $calendarRepository;
        $this->userSettingsService = $userSettingsService;
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
            $lifetime = $this->getUserSettingsService()->findUserSetting(
                $user, 'cosnics.libraries.storage.cache.external.defaultLifetime', $this->getDefaultLifetime()
            );

            $this->saveCacheDataForKey(
                $cacheIdentifier, $calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $user, $calendarIdentifier, $fromDate, $toDate
            ), $lifetime
            );
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }

    public function getUserSettingsService(): UserSettingsService
    {
        return $this->userSettingsService;
    }
}