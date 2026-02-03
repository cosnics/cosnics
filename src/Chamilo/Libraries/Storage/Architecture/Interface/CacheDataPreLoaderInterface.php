<?php
namespace Chamilo\Libraries\Storage\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Interface
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CacheDataPreLoaderInterface
{
    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function preLoadCacheData();
}