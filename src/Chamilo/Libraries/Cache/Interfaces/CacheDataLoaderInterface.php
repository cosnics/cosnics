<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CacheDataLoaderInterface
{
    public function clearCacheData(): bool;

    public function getCacheKey(): string;

    public function loadCacheData();

    public function reloadCacheData();

    public function saveCacheData(): bool;
}