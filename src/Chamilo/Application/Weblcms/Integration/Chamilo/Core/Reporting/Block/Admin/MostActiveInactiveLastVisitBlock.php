<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Translation\Translation;

class MostActiveInactiveLastVisitBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $course_count = $courses = CourseDataManager::count(Course::class, new DataClassCountParameters());
        
        $reporting_data->set_categories(
            array(
                Translation::get('Past24hr'), 
                Translation::get('PastWeek'), 
                Translation::get('PastMonth'), 
                Translation::get('PastYear'), 
                Translation::get('MoreThanOneYear'), 
                Translation::get('NeverAccessed')));
        
        $reporting_data->set_rows(array(Translation::get('TimesAccessed')));
        
        $past_24_hours = count(WeblcmsTrackingDataManager::count_courses_with_last_access_after_time(time() - 86400));
        $past_week = count(WeblcmsTrackingDataManager::count_courses_with_last_access_after_time(time() - 604800));
        $past_month = count(WeblcmsTrackingDataManager::count_courses_with_last_access_after_time(time() - 18144000));
        $past_year = count(WeblcmsTrackingDataManager::count_courses_with_last_access_after_time(time() - 31536000));
        $more_than_one_year = count(
            WeblcmsTrackingDataManager::count_courses_with_last_access_before_time(time() - 31536000));
        
        $never = $course_count - ($past_year + $more_than_one_year);
        
        $reporting_data->add_data_category_row(
            Translation::get('Past24hr'), 
            Translation::get('CoursCount'), 
            $past_24_hours);
        
        $reporting_data->add_data_category_row(Translation::get('PastWeek'), Translation::get('CoursCount'), $past_week);
        
        $reporting_data->add_data_category_row(
            Translation::get('PastMonth'), 
            Translation::get('CoursCount'), 
            $past_month);
        
        $reporting_data->add_data_category_row(Translation::get('PastYear'), Translation::get('CoursCount'), $past_year);
        
        $reporting_data->add_data_category_row(
            Translation::get('MoreThanOneYear'), 
            Translation::get('CoursCount'), 
            $more_than_one_year);
        
        $reporting_data->add_data_category_row(
            Translation::get('NeverAccessed'), 
            Translation::get('CoursCount'), 
            $never);
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE, Html::VIEW_PIE, Html::VIEW_CSV, Html::VIEW_XLSX, Html::VIEW_XML);
    }
}
