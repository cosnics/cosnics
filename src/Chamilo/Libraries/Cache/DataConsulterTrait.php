<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataConsulterTrait
{
    protected CacheDataLoaderInterface $dataLoader;

    public function getDataLoader(): CacheDataLoaderInterface
    {
        return $this->dataLoader;
    }
}