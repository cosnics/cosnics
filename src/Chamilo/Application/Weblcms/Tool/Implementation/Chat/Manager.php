<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Chat;

/**
 * @package application.lib.weblcms.tool.chat
 */

/**
 * This tool allows a user to publish chatboxes in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    public const ACTION_VIEW_CHAT = 'Viewer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_VIEW_CHAT;
}
