<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OwnedCalendarsCacheService extends DoctrineFilesystemCacheService
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository $calendarRepository
     */
    private $calendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository $calendarRepository
     */
    public function getCalendarRepository()
    {
        return $this->calendarRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository $calendarRepository
     */
    public function setCalendarRepository($calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Application\Calendar\Extension\Google\OwnedCalendars';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $lifetimeInMinutes = Configuration :: get_instance()->get_setting(
            array('Chamilo\Libraries\Calendar', 'refresh_external'));

        return $this->getCacheProvider()->save(
            $identifier,
            $this->getCalendarRepository()->findOwnedCalendars(),
            $lifetimeInMinutes * 60);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array();
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function getOwnedCalendars()
    {
        $calendarRepository = $this->getCalendarRepository();
        $identifier = $calendarRepository->getCacheIdentifier($calendarRepository->getAccessToken(), __METHOD__);

        return $this->getForIdentifier($identifier);
    }
}