<?php
namespace Chamilo\Core\Cache;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Actions
    const ACTION_BROWSE = 'Browser';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
