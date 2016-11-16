<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;

/**
 *
 * @package Chamilo\Libraries\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class IdentifiableCacheService implements CacheResetterInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheClearerInterface::clear()
     */
    public function clear()
    {
        return $this->clearForIdentifiers($this->getIdentifiers());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheWarmerInterface::warmUp()
     */
    public function warmUp()
    {
        return $this->warmUpForIdentifiers($this->getIdentifiers());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface::clearAndWarmUp()
     */
    public function clearAndWarmUp()
    {
        if (! $this->clear())
        {
            return false;
        }
        
        return $this->warmUp();
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     * @return boolean
     */
    abstract public function warmUpForIdentifier($identifier);

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag[]|string[] $identifiers
     * @return boolean
     */
    public function warmUpForIdentifiers($identifiers)
    {
        foreach ($identifiers as $identifier)
        {
            if (! $this->warmUpForIdentifier($identifier))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     * @return boolean
     */
    abstract public function clearForIdentifier($identifier);

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag[]|string[] $identifiers
     * @return boolean
     */
    public function clearForIdentifiers($identifiers)
    {
        foreach ($identifiers as $identifier)
        {
            if (! $this->clearForIdentifier($identifier))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag[]|string[] $identifiers
     * @return boolean
     */
    public function clearAndWarmUpForIdentifiers($identifiers)
    {
        foreach ($identifiers as $identifier)
        {
            if (! $this->clearAndWarmUpForIdentifier($identifier))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     * @return boolean
     */
    public function clearAndWarmUpForIdentifier($identifier)
    {
        if (! $this->clearForIdentifier($identifier))
        {
            return false;
        }
        
        return $this->warmUpForIdentifier($identifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     * @return mixed
     */
    abstract public function getForIdentifier($identifier);

    /**
     *
     * @return \Chamilo\Libraries\Cache\ParameterBag[]|string[]
     */
    abstract public function getIdentifiers();
}