<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service;

use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Sabre\VObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalCalendarCacheService extends DoctrineFilesystemCacheService implements UserBasedCacheInterface
{
    public const PARAM_LIFETIME = 'lifetime';
    public const PARAM_PATH = 'path';

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCalendarForPath(string $path): VObject\Component\VCalendar
    {
        return $this->getForIdentifier($path);
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return [];
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $calendarData = '';

        if (!file_exists($identifier))
        {
            if ($f = fopen($identifier, 'r'))
            {

                while (!feof($f))
                {
                    $calendarData .= fgets($f, 4096);
                }
                fclose($f);
            }
        }
        else
        {
            $calendarData = file_get_contents($identifier);
        }

        $calendar = VObject\Reader::read($calendarData, VObject\Reader::OPTION_FORGIVING);

        $cacheItem = $this->getCacheAdapter()->getItem($identifier);
        $cacheItem->set($calendar);

        return $this->getCacheAdapter()->save($cacheItem);
    }
}