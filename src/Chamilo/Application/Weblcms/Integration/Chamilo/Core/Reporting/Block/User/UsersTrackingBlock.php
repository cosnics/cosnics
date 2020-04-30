<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\User;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseStudentTrackerDetailTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class UsersTrackingBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Name'),
                Translation::get('UserName'),
                Translation::get('TimeOnCourse'),
                Translation::get('FirstAccess'),
                Translation::get('LastAccess'),
                Translation::get('TotalVisits'),
                Translation::get('TotalPublications'),
                Translation::get('UserDetail')
            )
        );

        $course_id = $this->getCourseId();
        $glyph = new FontAwesomeGlyph('chart-pie', array(), Translation::get('Details'));

        $count = 1;
        $users_resultset = CourseDataManager::retrieve_all_course_users($course_id);

        while ($user = $users_resultset->next_result())
        {
            $course_summary_data = WeblcmsTrackingDataManager::retrieve_course_access_summary_data(
                $course_id, $user[User::PROPERTY_ID]
            );

            $first_date = $this->format_date($course_summary_data[CourseVisit::PROPERTY_FIRST_ACCESS_DATE]);
            $last_date = $this->format_date($course_summary_data[CourseVisit::PROPERTY_LAST_ACCESS_DATE]);

            $time_spent_on_course = $this->convert_seconds_to_hours(
                $course_summary_data[CourseVisit::PROPERTY_TOTAL_TIME]
            );

            $params = $this->get_parent()->get_parameters();
            $params[Manager::PARAM_TEMPLATE_ID] = CourseStudentTrackerDetailTemplate::class;
            $params[Manager::PARAM_USERS] = $user[User::PROPERTY_ID];

            $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $glyph->render() . '</a>';

            $reporting_data->add_category($count);

            $reporting_data->add_data_category_row(
                $count, Translation::get('Name'), User::fullname(
                $user[User::PROPERTY_FIRSTNAME], $user[User::PROPERTY_LASTNAME]
            )
            );

            $reporting_data->add_data_category_row(
                $count, Translation::get('UserName'), $user[User::PROPERTY_USERNAME]
            );

            $reporting_data->add_data_category_row($count, Translation::get('TimeOnCourse'), $time_spent_on_course);

            $reporting_data->add_data_category_row($count, Translation::get('FirstAccess'), $first_date);
            $reporting_data->add_data_category_row($count, Translation::get('LastAccess'), $last_date);

            $reporting_data->add_data_category_row(
                $count, Translation::get('TotalVisits'),
                $course_summary_data[CourseVisit::PROPERTY_TOTAL_NUMBER_OF_ACCESS]
            );

            $reporting_data->add_data_category_row(
                $count, Translation::get('TotalPublications'), $this->count_publications_from_user_in_course(
                $user[User::PROPERTY_ID], $course_id
            )
            );

            $reporting_data->add_data_category_row($count, Translation::get('UserDetail'), $link);

            $count ++;
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
