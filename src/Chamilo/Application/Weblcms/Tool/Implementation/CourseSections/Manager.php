<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections;

/**
 * $Id: course_sections_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_sections
 */

/**
 * This tool allows a user to publish course_sectionss in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const ACTION_VIEW_COURSE_SECTIONS = 'viewer';
    const ACTION_CREATE_COURSE_SECTION = 'creator';
    const ACTION_REMOVE_COURSE_SECTION = 'deleter';
    const ACTION_UPDATE_COURSE_SECTION = 'updater';
    const ACTION_MOVE_COURSE_SECTION = 'mover';
    const ACTION_CHANGE_COURSE_SECTION_VISIBILITY = 'visibility_changer';
    const ACTION_SELECT_TOOLS_COURSE_SECTION = 'tool_selector';
    const ACTION_CHANGE_SECTION = 'change_section';
    const DEFAULT_ACTION = self :: ACTION_VIEW_COURSE_SECTIONS;
    const PARAM_COURSE_SECTION_ID = 'course_section_id';
    const PARAM_DIRECTION = 'direction';
    const PARAM_REMOVE_SELECTED = 'remove_selected';
}
