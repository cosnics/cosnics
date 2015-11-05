<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;

/**
 *
 * @package Chamilo\Libraries\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TranslationCacheService extends DoctrinePhpFileCacheService
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCacheIdentifiers()
     */
    public function getCacheIdentifiers()
    {
        return array_keys(Configuration :: get_instance()->getLanguages());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return __NAMESPACE__ . '\Translation';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::fillCache()
     */
    public function fillCache()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\CacheServiceInterface::fillCacheForIdentifier()
     */
    public function fillCacheForIdentifier($identifier)
    {
        // TODO Auto-generated method stub
    }
}