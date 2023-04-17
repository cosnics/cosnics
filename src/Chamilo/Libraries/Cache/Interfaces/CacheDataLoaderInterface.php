<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface CacheDataLoaderInterface extends DataLoaderInterface
{
    public function clearCache(): bool;

    public function getCacheKey(): string;

    public function loadCache(): bool;

    public function reloadCache(): bool;

}