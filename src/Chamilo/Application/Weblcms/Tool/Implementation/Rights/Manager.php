<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Rights;

/**
 * $Id: rights_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.rights
 */

/**
 * This tool allows a user to manage rights in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const DEFAULT_ACTION = self :: ACTION_EDIT_RIGHTS;
}
