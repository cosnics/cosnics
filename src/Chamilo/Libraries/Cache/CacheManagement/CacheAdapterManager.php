<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Cache director to clear and / or warmup caches
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheAdapterManager
{
    /**
     * @var \Symfony\Component\Cache\Adapter\AdapterInterface[]
     */
    protected array $cacheAdapters;

    public function __construct()
    {
        $this->cacheAdapters = [];
    }

    public function addCacheAdapter(string $alias, AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapters[$alias] = $cacheAdapter;
    }

    /**
     * @param string[] $cacheAdapterAliases
     */
    public function clear(array $cacheAdapterAliases = [])
    {
        $cacheAdapters = $this->getCacheAdaptersByAliases($cacheAdapterAliases);

        foreach ($cacheAdapters as $cacheAdapter)
        {
            $cacheAdapter->clear();
        }
    }

    /**
     * @return string[]
     */
    public function getCacheAdapterAliases(): array
    {
        return array_keys($this->cacheAdapters);
    }

    /**
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface[]
     */
    public function getCacheAdapters(): array
    {
        return $this->cacheAdapters;
    }

    /**
     * @param string[] $cacheAdapterAliases
     *
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface[]
     */
    protected function getCacheAdaptersByAliases(array $cacheAdapterAliases = []): array
    {
        $cacheAdapters = $this->getCacheAdapters();

        if (empty($cacheAdapterAliases))
        {
            return $cacheAdapters;
        }

        return array_filter($cacheAdapters, function ($cacheAdapterAlias) use ($cacheAdapterAliases) {
            return array_key_exists($cacheAdapterAlias, $cacheAdapterAliases);
        }, ARRAY_FILTER_USE_KEY);

        //        $filteredCacheAdapters = [];
        //
        //        foreach ($cacheAdapterAliases as $cacheAdapterAlias)
        //        {
        //            if (!array_key_exists($cacheAdapterAlias, $cacheAdapters))
        //            {
        //                throw new InvalidArgumentException(
        //                    sprintf('The given cache adapter alias %s does not exist', $cacheAdapterAlias)
        //                );
        //            }
        //
        //            $filteredCacheAdapters[$cacheAdapterAlias] = $cacheAdapters[$cacheAdapterAlias];
        //        }
        //
        //        return $filteredCacheAdapters;
    }
}