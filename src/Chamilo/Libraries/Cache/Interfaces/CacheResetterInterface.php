<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CacheResetterInterface extends CacheClearerInterface
{

    /**
     * Clears the cache and warms it up again.
     */
    public function clearAndWarmUp();
}