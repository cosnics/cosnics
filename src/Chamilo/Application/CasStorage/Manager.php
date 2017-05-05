<?php
namespace Chamilo\Application\CasStorage;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\CasStorage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Actions
    const ACTION_BROWSE = 'Browser';

    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
