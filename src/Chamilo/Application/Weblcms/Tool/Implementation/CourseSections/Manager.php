<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections;

use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * This tool allows a user to publish course_sectionss in his or her course.
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseSections
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    public const ACTION_CHANGE_COURSE_SECTION_VISIBILITY = 'VisibilityChanger';
    public const ACTION_CREATE_COURSE_SECTION = 'Creator';
    public const ACTION_MOVE_COURSE_SECTION = 'Mover';
    public const ACTION_REMOVE_COURSE_SECTION = 'Deleter';
    public const ACTION_SELECT_TOOLS_COURSE_SECTION = 'ToolSelector';
    public const ACTION_UPDATE_COURSE_SECTION = 'Updater';
    public const ACTION_VIEW_COURSE_SECTIONS = 'Viewer';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_VIEW_COURSE_SECTIONS;

    public const PARAM_COURSE_SECTION_ID = 'course_section_id';
    public const PARAM_DIRECTION = 'direction';
    public const PARAM_REMOVE_SELECTED = 'remove_selected';

    /**
     * Adds a breadcrumb to the browser component
     *
     * @param BreadcrumbTrail $breadcrumbTrail
     */
    protected function addBrowserBreadcrumb(BreadcrumbTrail $breadcrumbTrail)
    {
        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_COURSE_SECTIONS]),
                Translation::getInstance()->getTranslation('ViewerComponent', [], __NAMESPACE__)
            )
        );
    }
}
