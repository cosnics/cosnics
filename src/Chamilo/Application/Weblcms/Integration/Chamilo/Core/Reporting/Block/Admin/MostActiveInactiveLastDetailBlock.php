<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class MostActiveInactiveLastDetailBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $this->add_reporting_data_rows_for_course_visit_data($reporting_data);
        $reporting_data->add_row(Translation :: get('LastPublication'));

        $courses = CourseDataManager :: retrieves(Course :: class_name(), new DataClassRetrievesParameters());
        while ($course = $courses->next_result())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($course->get_id()));
            $publications = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publications(
                $condition);

            while ($publication = $publications->next_result())
            {
                $last_publication = DatetimeUtilities :: format_locale_date(
                    null,
                    $publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]);
            }

            $reporting_data->add_category($course->get_title());

            $course_visit = WeblcmsTrackingDataManager :: retrieve_course_access_summary_data($course->get_id());
            $this->add_reporting_data_from_course_visit_as_row($course->get_title(), $reporting_data, $course_visit);

            $reporting_data->add_data_category_row(
                $course->get_title(),
                Translation :: get('LastPublication'),
                $last_publication);
        }
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
