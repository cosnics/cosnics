<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CacheClearerInterface extends CacheWarmerInterface
{
    public function clear(): bool;
}