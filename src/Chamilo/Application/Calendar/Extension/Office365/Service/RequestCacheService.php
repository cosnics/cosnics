<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Service;

use Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RequestCacheService extends DoctrineFilesystemCacheService implements UserBasedCacheInterface
{
    const PARAM_REQUEST = 'request';
    const PARAM_LIFETIME = 'lifetime';

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
     */
    private $calendarRepository;

    /**
     *
     * @param \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
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
        return 'Chamilo\Application\Calendar\Extension\Office365\Repository';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $request = $identifier->get(self :: PARAM_REQUEST);
        $calendarRepository = $this->getCalendarRepository();
        $result = $calendarRepository->sendRequest($request);

        return $this->getCacheProvider()->save((string) $identifier, $result, $identifier->get(self :: PARAM_LIFETIME));
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array();
    }
}