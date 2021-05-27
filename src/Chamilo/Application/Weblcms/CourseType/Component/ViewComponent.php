<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes a viewer for a given course type
 *
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class ViewComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    public function run()
    {
        $html = [];
        $html[] = $this->render_header();
        $html[] = $this->get_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component...
     * Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_type_viewer');
        $breadcrumbtrail->add(
            new Breadcrumb($this->get_browse_course_type_url(), Translation::get('CourseTypeManagerBrowseComponent'))
        );
    }

    /**
     * Creates and returns the action bar
     *
     * @return ButtonToolBarRenderer
     */
    private function getButtonToolbarRenderer(CourseType $course_type)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_update_course_type_url($course_type->get_id()), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            if (!DataManager::has_course_type_courses($course_type->get_id()))
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_delete_course_type_url($course_type->get_id()), ToolbarItem::DISPLAY_ICON_AND_LABEL,
                        true
                    )
                );
            }
            else
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('DeleteNA', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('times', array('text-muted')), null, ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns additional parameters that need to be registered and are used in every url generated by this component
     *
     * @return String[]
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_COURSE_TYPE_ID);
    }

    /**
     * Returns the html for the courses table that belong to the given course type
     *
     * @param CourseType $course_type
     *
     * @return String
     */
    private function get_courses_table_html(CourseType $course_type)
    {
        // $table = new AdminCourseBrowserTable($this, $this->get_parameters(), $this->get_condition($course_type));
        // return $table->as_html();
    }

    /**
     * Returns the html for this component
     *
     * @return String
     */
    private function get_html()
    {
        $html = [];

        $course_type = $this->get_selected_course_type();
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($course_type);

        $html[] = $this->buttonToolbarRenderer->render();

        // Description
        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('info-circle', array('fa-lg'), null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $glyph->render() . ' ' .
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES) . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $course_type->get_description();
        $html[] = '</div>';

        $html[] = '</div>';

        // Courses
        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('chalkboard', array('fa-lg'), null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $glyph->render() . ' ' . Translation::get('Courses') . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->get_courses_table_html($course_type);
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
