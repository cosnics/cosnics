<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheDataPreLoaderManager
{
    /**
     * @var \Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface[]
     */
    protected array $cacheDataPreLoaderServices;

    public function __construct()
    {
        $this->cacheDataPreLoaderServices = [];
    }

    public function addCacheDataPreLoaderService(string $alias, CacheDataPreLoaderInterface $cacheDataPreLoaderService
    ): static
    {
        $this->cacheDataPreLoaderServices[$alias] = $cacheDataPreLoaderService;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCacheDataPreLoaderServiceAliases(): array
    {
        return array_keys($this->cacheDataPreLoaderServices);
    }

    /**
     * @param string[] $cacheDataPreLoaderServiceAliases
     *
     * @return \Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface[]
     */
    protected function getCacheDataPreLoaderServicesByAliases(array $cacheDataPreLoaderServiceAliases = []): array
    {
        if (empty($cacheDataPreLoaderServiceAliases)) {
            return $this->cacheDataPreLoaderServices;
        }

        return array_filter(
            $this->cacheDataPreLoaderServices,
            function ($cacheDataPreLoaderServiceAlias) use ($cacheDataPreLoaderServiceAliases) {
                return array_key_exists(get_class($cacheDataPreLoaderServiceAlias), $cacheDataPreLoaderServiceAliases);
            }, ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param string[] $cacheDataPreLoaderServiceAliases
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function preLoad(array $cacheDataPreLoaderServiceAliases = []): static
    {
        $cacheDataPreLoaderServices = $this->getCacheDataPreLoaderServicesByAliases($cacheDataPreLoaderServiceAliases);

        foreach ($cacheDataPreLoaderServices as $cacheService) {
            $cacheService->preLoadCacheData();
        }

        return $this;
    }
}