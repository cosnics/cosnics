<?php
namespace Chamilo\Application\Weblcms\Tool\Action;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package application.lib.weblcms.tool
 */

/**
 * ============================================================================== This is the base class component for
 * all tool components used in applications.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop ==============================================================================
 */
abstract class Manager extends Application implements NoContextComponent
{
    const PARAM_ACTION = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION;
    const ACTION_VIEW = 'Viewer';
    const ACTION_BROWSE = 'Browser';
    const ACTION_PUBLISH = 'Publisher';
    const ACTION_DELETE = 'Deleter';
    const ACTION_TOGGLE_VISIBILITY = 'ToggleVisibility';
    const ACTION_MOVE = 'Mover';
    const ACTION_UPDATE_CONTENT_OBJECT = 'ContentObjectUpdater';
    const ACTION_UPDATE_PUBLICATION = 'PublicationUpdater';
    const MOVE_TO_CATEGORY_COMPONENT = 'CategoryMover';
    const INTRODUCTION_PUBLISHER_COMPONENT = 'IntroductionPublisher';
    const MANAGE_CATEGORIES_COMPONENT = 'CategoryManager';
    const VIEW_REPORTING_COMPONENT = 'ReportingViewer';
    const BUILD_COMPLEX_CONTENT_OBJECT_COMPONENT = 'ComplexBuilder';
    const DISPLAY_COMPLEX_CONTENT_OBJECT_COMPONENT = 'ComplexDisplay';
    const RIGHTS_EDITOR_COMPONENT = 'RightsEditor';
    const DEFAULT_ACTION = \Chamilo\Application\Weblcms\Tool\Manager::DEFAULT_ACTION;

    public static function factory($type, $tool_component)
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'Component';

        if (! class_exists($class))
        {
            throw new Exception(Translation::get('ToolComponentTypeDoesNotExist', array('type' => $type)));
        }

        return new $class($tool_component);
    }

    /**
     * Check if the current user has a given right in this tool
     *
     * @param $right int
     * @param ContentObjectPublication $publication
     *
     * @param null $category_id
     *
     * @return bool True if the current user has the right
     */
    public function is_allowed($right, $publication = null, $category_id = null)
    {
        return $this->get_parent()->is_allowed($right, $publication, $category_id);
    }

    /**
     *
     * @see WeblcmsManager :: get_last_visit_date()
     */
    public function get_last_visit_date()
    {
        return $this->get_parent()->get_last_visit_date();
    }

    public function perform_requested_actions()
    {
        return $this->get_parent()->perform_requested_actions();
    }

    public function get_categories($list = false)
    {
        return $this->get_parent()->get_categories($list);
    }

    public function get_category($id)
    {
        return $this->get_parent()->get_category($id);
    }

    public function display_introduction_text($introduction_text)
    {
        return $this->get_parent()->display_introduction_text($introduction_text);
    }

    public function get_access_details_toolbar_item($parent)
    {
        return $this->get_parent()->get_access_details_toolbar_item($parent);
    }

    public function get_allowed_types()
    {
        return $this->get_parent()->get_allowed_types();
    }

    public function get_complex_builder_url($pid)
    {
        return $this->get_parent()->get_complex_builder_url($pid);
    }

    public function get_complex_display_url($pid)
    {
        return $this->get_parent()->get_complex_display_url($pid);
    }

    /**
     *
     * @return Course
     */
    public function get_course()
    {
        return $this->get_parent()->get_course();
    }

    public function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    public function get_course_groups()
    {
        return $this->get_parent()->get_course_groups();
    }

    public function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    public function get_tool_id()
    {
        return $this->get_parent()->get_tool_id();
    }

    public function render_header()
    {
        return $this->get_parent()->render_header();
    }

    public function get_allowed_content_object_types()
    {
        return $this->get_parent()->get_allowed_types();
    }

    public function get_location($category_id = null)
    {
        return $this->get_parent()->get_location($category_id);
    }

    public function get_locations()
    {
        return $this->get_parent()->get_locations();
    }

    public function get_entities()
    {
        return $this->get_parent()->get_entities();
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_parent()->get_content_object_display_attachment_url($attachment);
    }
}
