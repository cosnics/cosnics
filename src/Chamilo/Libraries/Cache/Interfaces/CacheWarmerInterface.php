<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CacheWarmerInterface
{

    /**
     * Warms up the cache.
     */
    public function warmUp();
}