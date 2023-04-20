<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Google_Service_Calendar_Events;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventsCacheService
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

    public function getEventsForCalendarIdentifierAndBetweenDates(string $calendarIdentifier, $fromDate, $toDate
    ): Google_Service_Calendar_Events
    {
        $cacheAdapter = $this->getCacheAdapter();
        $calendarRepository = $this->getCalendarRepository();

        $cacheIdentifier = md5(
            serialize([$calendarRepository->getAccessToken(), __METHOD__, $calendarIdentifier, $fromDate, $toDate])
        );

        try
        {
            $cacheItem = $cacheAdapter->getItem($cacheIdentifier);

            if (!$cacheItem->isHit())
            {
                $lifetimeInMinutes = $this->getUserSettingService()->getSettingForUser(
                    $this->getUser(), 'Chamilo\Libraries\Calendar', 'refresh_external'
                );

                $cacheItem->set(
                    $calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                        $calendarIdentifier, $fromDate, $toDate
                    )
                );

                $cacheItem->expiresAfter($lifetimeInMinutes * 60);
                $cacheAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException $e)
        {
            return new Google_Service_Calendar_Events();
        }
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