<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Base class to display the access to the tools ToolAccessBlock
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package application\weblcms\integration\core\reporting
 */
abstract class ToolAccessBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_rows(
            array(
                Translation :: get('Tool'),
                Translation :: get('FirstAccess'),
                Translation :: get('LastAccess'),
                Translation :: get('TotalVisits'),
                Translation :: get('TotalTime'),
                Translation :: get('TotalPublications')));

        $course_id = $this->get_course_id();

        $course_tools_summary_data = $this->retrieve_course_summary_data();

        while ($course_tool_summary_data = $course_tools_summary_data->next_result())
        {
            $tool_name = $course_tool_summary_data[CourseTool :: PROPERTY_NAME];

            $tool_translation = Translation :: get(
                'TypeName',
                null,
                \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($tool_name));

            $params = array();

            $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
            $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $this->get_course_id();
            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $tool_name;

            $redirect = new Redirect($params);
            $url = $redirect->getUrl();

            $link = ' <a href="' . $url . '">' . $tool_translation . '</a>';

            $reporting_data->add_category($tool_name);
            $reporting_data->add_data_category_row($tool_name, Translation :: get('Tool'), $link);

            $reporting_data->add_data_category_row(
                $tool_name,
                Translation :: get('FirstAccess'),
                $this->format_date($course_tool_summary_data[CourseVisit :: PROPERTY_FIRST_ACCESS_DATE]));

            $reporting_data->add_data_category_row(
                $tool_name,
                Translation :: get('LastAccess'),
                $this->format_date($course_tool_summary_data[CourseVisit :: PROPERTY_LAST_ACCESS_DATE]));

            $reporting_data->add_data_category_row(
                $tool_name,
                Translation :: get('TotalVisits'),
                $course_tool_summary_data[CourseVisit :: PROPERTY_TOTAL_NUMBER_OF_ACCESS] ? $course_tool_summary_data[CourseVisit :: PROPERTY_TOTAL_NUMBER_OF_ACCESS] : 0);

            $reporting_data->add_data_category_row(
                $tool_name,
                Translation :: get('TotalTime'),
                $this->convert_seconds_to_hours($course_tool_summary_data[CourseVisit :: PROPERTY_TOTAL_TIME]));

            $reporting_data->add_data_category_row(
                $tool_name,
                Translation :: get('TotalPublications'),
                $this->count_tool_publications($tool_name));
        }

        $reporting_data->hide_categories();
        return $reporting_data;
    }

    /**
     * Counts the publications of a tool
     *
     * @param int $course_id
     * @param string $tool_name
     *
     * @return int
     */
    public function count_tool_publications($tool_name)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications(
            $this->get_tool_publications_condition($tool_name));
    }

    /**
     * Returns the condition for the tools publication count
     *
     * @param string $tool_name
     *
     * @return AndCondition
     */
    public function get_tool_publications_condition($tool_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable($tool_name));
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }

    /**
     * Returns the summary data for this course
     *
     * @return RecordResultSet
     */
    abstract public function retrieve_course_summary_data();
}
