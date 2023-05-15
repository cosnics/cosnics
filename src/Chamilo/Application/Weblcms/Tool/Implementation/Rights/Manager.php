<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Rights;

/**
 * @package application.lib.weblcms.tool.rights
 */

/**
 * This tool allows a user to manage rights in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_EDIT_RIGHTS;
}
