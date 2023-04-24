<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;

/**
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheDataPreLoaderManager
{
    /**
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[]
     */
    protected array $cacheDataPreLoaderServices;

    public function __construct()
    {
        $this->cacheDataPreLoaderServices = [];
    }

    public function addCacheDataPreLoaderService(string $alias, CacheDataPreLoaderInterface $cacheDataPreLoaderService)
    {
        $this->cacheDataPreLoaderServices[$alias] = $cacheDataPreLoaderService;
    }

    /**
     * @return string[]
     */
    public function getCacheDataPreLoaderServiceAliases(): array
    {
        return array_keys($this->cacheDataPreLoaderServices);
    }

    /**
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[]
     */
    public function getCacheDataPreLoaderServices(): array
    {
        return $this->cacheDataPreLoaderServices;
    }

    /**
     * @param string[] $cacheDataPreLoaderServiceAliases
     *
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[]
     */
    protected function getCacheDataPreLoaderServicesByAliases(array $cacheDataPreLoaderServiceAliases = []): array
    {
        $cacheDataPreLoaderServices = $this->getCacheDataPreLoaderServices();

        if (empty($cacheDataPreLoaderServiceAliases))
        {
            return $cacheDataPreLoaderServices;
        }

        return array_filter(
            $cacheDataPreLoaderServices,
            function ($cacheDataPreLoaderServiceAlias) use ($cacheDataPreLoaderServiceAliases) {
                return array_key_exists($cacheDataPreLoaderServiceAlias, $cacheDataPreLoaderServiceAliases);
            }, ARRAY_FILTER_USE_KEY
        );

        //        $cacheDataPreLoaderServices = [];
        //
        //        foreach ($cacheDataPreLoaderServiceAliases as $cacheDataPreLoaderServiceAlias)
        //        {
        //            if (!array_key_exists($cacheDataPreLoaderServiceAlias, $this->cacheDataPreLoaderServices))
        //            {
        //                throw new InvalidArgumentException(
        //                    sprintf('The given cache service alias %s does not exist', $cacheDataPreLoaderServiceAlias)
        //                );
        //            }
        //
        //            $cacheDataPreLoaderServices[$cacheDataPreLoaderServiceAlias] =
        //                $this->cacheDataPreLoaderServices[$cacheDataPreLoaderServiceAlias];
        //        }
        //
        //        return $cacheDataPreLoaderServices;
    }

    /**
     * @param string[] $cacheDataPreLoaderServiceAliases
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function preLoad(array $cacheDataPreLoaderServiceAliases = [])
    {
        $cacheDataPreLoaderServices = $this->getCacheDataPreLoaderServicesByAliases($cacheDataPreLoaderServiceAliases);

        foreach ($cacheDataPreLoaderServices as $cacheService)
        {
            $cacheService->preLoadCacheData();
        }
    }
}