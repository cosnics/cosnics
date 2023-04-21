<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CacheDataLoaderInterface
{
    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCachedData();
}