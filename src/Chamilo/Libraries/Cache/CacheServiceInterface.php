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
     * @return boolean
     */
    public function clearCacheForKeys($keys);

    /**
     *
     * @return boolean
     */
    public function clearCache();

    /**
     *
     * @return mixed
     */
    public function getCache();
}