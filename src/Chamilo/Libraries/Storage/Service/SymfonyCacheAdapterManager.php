<?php
namespace Chamilo\Libraries\Storage\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Cache director to clear and / or warmup caches
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SymfonyCacheAdapterManager
{
    /**
     * @var \Symfony\Component\Cache\Adapter\AdapterInterface[]
     */
    protected array $cacheAdapters;

    public function __construct()
    {
        $this->cacheAdapters = [];
    }

    public function addCacheAdapter(string $alias, AdapterInterface $cacheAdapter): static
    {
        $this->cacheAdapters[$alias] = $cacheAdapter;

        return $this;
    }

    /**
     * @param string[] $cacheAdapterAliases
     */
    public function clear(array $cacheAdapterAliases = []): static
    {
        $cacheAdapters = $this->getCacheAdaptersByAliases($cacheAdapterAliases);

        foreach ($cacheAdapters as $cacheAdapter) {
            $cacheAdapter->clear();
        }

        return $this;
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

        if (empty($cacheAdapterAliases)) {
            return $cacheAdapters;
        }

        return array_filter($cacheAdapters, function ($cacheAdapterAlias) use ($cacheAdapterAliases) {
            return array_key_exists(get_class($cacheAdapterAlias), $cacheAdapterAliases);
        }, ARRAY_FILTER_USE_KEY);
    }
}