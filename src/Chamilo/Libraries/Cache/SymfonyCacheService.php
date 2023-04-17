<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheInterface;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Libraries\Cache\Doctrine
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class SymfonyCacheService implements CacheInterface
{
    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected ConfigurablePathBuilder $configurablePathBuilder;

    private AdapterInterface $cacheAdapter;

    public function __construct(AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function clear(): bool
    {
        return $this->getCacheAdapter()->clear();
    }

    public function clearAndWarmUp(): bool
    {
        if (!$this->clear())
        {
            return false;
        }

        return $this->warmUp();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearAndWarmUpForIdentifier($identifier): bool
    {
        if (!$this->clearForIdentifier($identifier))
        {
            return false;
        }

        return $this->warmUpForIdentifier($identifier);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearAndWarmUpForIdentifiers($identifiers): bool
    {
        foreach ($identifiers as $identifier)
        {
            if (!$this->clearAndWarmUpForIdentifier($identifier))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearForIdentifier($identifier): bool
    {
        return $this->getCacheAdapter()->deleteItem((string) $identifier);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearForIdentifiers($identifiers): bool
    {
        foreach ($identifiers as $identifier)
        {
            if (!$this->clearForIdentifier($identifier))
            {
                return false;
            }
        }

        return true;
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     */
    public function getForIdentifier($identifier)
    {
        $cacheItem = $this->getCacheAdapter()->getItem((string) $identifier);

        if (!$cacheItem->isHit())
        {
            if (!$this->warmUpForIdentifier($identifier))
            {
                throw new Exception('CacheError');
            }
        }

        return $this->getCacheAdapter()->getItem((string) $identifier)->get();
    }

    /**
     * @return string[]
     */
    abstract public function getIdentifiers(): array;

    public function warmUp(): bool
    {
        return $this->warmUpForIdentifiers($this->getIdentifiers());
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     */
    abstract public function warmUpForIdentifier($identifier): bool;

    /**
     * @param string[] $identifiers
     */
    public function warmUpForIdentifiers(array $identifiers): bool
    {
        foreach ($identifiers as $identifier)
        {
            if (!$this->warmUpForIdentifier($identifier))
            {
                return false;
            }
        }

        return true;
    }
}