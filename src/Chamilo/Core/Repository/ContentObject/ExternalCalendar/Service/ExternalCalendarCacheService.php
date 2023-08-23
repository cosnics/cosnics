<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service;

use Chamilo\Libraries\Cache\Traits\SingleCacheAdapterHandlerTrait;
use Sabre\VObject;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalCalendarCacheService
{
    use SingleCacheAdapterHandlerTrait;

    public const PARAM_LIFETIME = 'lifetime';
    public const PARAM_PATH = 'path';

    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getCalendarForPath(string $path): VObject\Component\VCalendar
    {
        $cacheIdentifier = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $path]);

        if (!$this->hasCacheDataForKey($cacheIdentifier))
        {
            $calendarData = '';

            if (!file_exists($path))
            {
                if ($f = fopen($path, 'r'))
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
                $calendarData = file_get_contents($path);
            }

            $calendar = VObject\Reader::read($calendarData, VObject\Reader::OPTION_FORGIVING);

            $this->saveCacheDataForKey($cacheIdentifier, $calendar);
        }

        return $this->readCacheDataForKey($cacheIdentifier);
    }
}