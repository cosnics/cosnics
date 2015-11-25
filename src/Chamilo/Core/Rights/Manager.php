<?php
namespace Chamilo\Core\Rights;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: rights_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 *
 * @package rights.lib.rights_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
