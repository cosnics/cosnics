<?php
namespace Chamilo\Libraries\Cache\Doctrine\Service;

use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineFilesystemCacheService extends DoctrineCacheService
{

    /**
     *
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function setupCacheProvider()
    {
        return new FilesystemCache($this->getCachePath());
    }
}