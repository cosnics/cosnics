<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service;

use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Cache\ParameterBag;
use Sabre\VObject;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalCalendarCacheService extends DoctrineFilesystemCacheService implements UserBasedCacheInterface
{
    const PARAM_PATH = 'path';
    const PARAM_LIFETIME = 'lifetime';

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return __NAMESPACE__ . '\Ical';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $path = $identifier->get(self::PARAM_PATH);
        $lifetime = $identifier->get(self::PARAM_LIFETIME);
        
        if (! file_exists($path))
        {
            if ($f = @fopen($path, 'r'))
            {
                $calendarData = '';
                while (! feof($f))
                {
                    $calendarData .= fgets($f, 4096);
                }
                fclose($f);
            }
        }
        else
        {
            $calendarData = file_get_contents($path);
        }

        if(!empty($calendarData)) {
            $calendar = VObject\Reader::read($calendarData, VObject\Reader::OPTION_FORGIVING);

            return $this->getCacheProvider()->save($identifier, $calendar, $lifetime);
        }

        return false;
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
     * @param string $path
     * @param integer $lifetime
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function getCalendarForPath($path, $lifetime = 3600)
    {
        $cacheIdentifier = md5(serialize($path));
        $parameterBag = new ParameterBag(
            array(
                ParameterBag::PARAM_IDENTIFIER => $cacheIdentifier, 
                self::PARAM_PATH => $path, 
                self::PARAM_LIFETIME => $lifetime));
        
        return $this->getForIdentifier($parameterBag);
    }
}