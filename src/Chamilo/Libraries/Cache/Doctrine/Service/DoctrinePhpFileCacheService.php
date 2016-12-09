<?php
namespace Chamilo\Libraries\Cache\Doctrine\Service;

use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrinePhpFileCacheService extends DoctrineCacheService
{

    /**
     *
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache
     */
    public function setupCacheProvider()
    {
        return new PhpFileCache($this->getCachePath());
    }
}