<?php
namespace Chamilo\Libraries\Cache;

/**
 *
 * @package Chamilo\Libraries\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CacheServiceInterface
{

    public function getCacheProvider();

    /**
     *
     * @return boolean
     */
    public function fillCache();

    /**
     *
     * @param string $identifier
     * @return boolean
     */
    public function fillCacheForIdentifier($identifier);

    /**
     *
     * @return boolean
     */
    public function clearCacheForIdentifiers($identifiers);

    /**
     *
     * @return boolean
     */
    public function clearCache();

    /**
     *
     * @return mixed
     */
    public function getCacheForIdentifier();
}