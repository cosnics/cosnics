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
     * @param string $identifier
     * @return boolean
     */
    abstract public function warmUpForIdentifier($identifier);

    /**
     *
     * @return boolean
     */
    abstract public function clearForIdentifiers($identifiers);

    /**
     *
     * @param string $identifier
     * @return mixed
     */
    abstract public function getForIdentifier($identifier);

    /**
     *
     * @return string[]
     */
    abstract public function getIdentifiers();
}