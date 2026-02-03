<?php
namespace Chamilo\Libraries\Storage\Architecture\Trait;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Trait
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SimpleCacheDataPreLoaderTrait
{
    abstract public function loadCacheData();

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function preLoadCacheData(): mixed
    {
        return $this->loadCacheData();
    }
}