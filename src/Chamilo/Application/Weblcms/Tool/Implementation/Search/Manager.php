<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Search;

/**
 * $Id: search_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.search
 */

/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_SEARCH = 'Searcher';
    const DEFAULT_ACTION = self :: ACTION_SEARCH;
}
