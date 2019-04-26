<?php
namespace Chamilo\Libraries\Cache\Doctrine\Service;

use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Doctrine\Common\Cache\ArrayCache;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineArrayCacheService extends DoctrineCacheService
{

    /**
     *
     * @return \Doctrine\Common\Cache\ArrayCache
     */
    public function setupCacheProvider()
    {
        return new ArrayCache();
    }
}