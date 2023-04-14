<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Cache\ParameterBag;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Google_Service_Calendar_Events;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventsCacheService extends DoctrineCacheService implements UserBasedCacheInterface
{
    public const PARAM_CALENDAR_IDENTIFIER = 'calendarIdentifier';
    public const PARAM_FROM_DATE = 'fromDate';
    public const PARAM_TO_DATE = 'toDate';

    protected User $user;

    protected UserSettingService $userSettingService;

    private CalendarRepository $calendarRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder,
        CalendarRepository $calendarRepository, User $user, UserSettingService $userSettingService
    )
    {
        parent::__construct($cacheAdapter, $configurablePathBuilder);

        $this->calendarRepository = $calendarRepository;
        $this->user = $user;
        $this->userSettingService = $userSettingService;
    }

    public function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(string $calendarIdentifier, $fromDate, $toDate
    ): Google_Service_Calendar_Events
    {
        $calendarRepository = $this->getCalendarRepository();

        $cacheIdentifier = $calendarRepository->getCacheIdentifier(
            $calendarRepository->getAccessToken(), __METHOD__, [$calendarIdentifier, $fromDate, $toDate]
        );

        $identifier = new ParameterBag(
            [
                ParameterBag::PARAM_IDENTIFIER => $cacheIdentifier,
                self::PARAM_CALENDAR_IDENTIFIER => $calendarIdentifier,
                self::PARAM_FROM_DATE => $fromDate,
                self::PARAM_TO_DATE => $toDate
            ]
        );

        return $this->getForIdentifier($identifier);
    }

    /**
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers(): array
    {
        return [];
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $lifetimeInMinutes = $this->getUserSettingService()->getSettingForUser(
            $this->getUser(), 'Chamilo\Libraries\Calendar', 'refresh_external'
        );

        $calendarIdentifier = $identifier->get(self::PARAM_CALENDAR_IDENTIFIER);
        $fromDate = $identifier->get(self::PARAM_FROM_DATE);
        $toDate = $identifier->get(self::PARAM_TO_DATE);

        $result = $this->getCalendarRepository()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier, $fromDate, $toDate
        );

        $cacheItem = $this->getCacheAdapter()->getItem($identifier->__toString());
        $cacheItem->set($result);
        $cacheItem->expiresAfter($lifetimeInMinutes * 60);

        return $this->getCacheAdapter()->save($cacheItem);
    }
}