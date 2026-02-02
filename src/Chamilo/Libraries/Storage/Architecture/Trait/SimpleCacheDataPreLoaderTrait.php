<?php
namespace Chamilo\Libraries\Storage\Architecture\Trait;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SimpleCacheDataPreLoaderTrait
{
    abstract public function loadCacheData();

    /**
     * @return mixed
     */
    public function preLoadCacheData(): mixed
    {
        return $this->loadCacheData();
    }
}