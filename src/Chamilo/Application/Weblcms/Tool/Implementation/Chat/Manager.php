<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Chat;

/**
 * $Id: chat_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.chat
 */
/**
 * This tool allows a user to publish chatboxes in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_VIEW_CHAT = 'Viewer';
    const DEFAULT_ACTION = self :: ACTION_VIEW_CHAT;
}
