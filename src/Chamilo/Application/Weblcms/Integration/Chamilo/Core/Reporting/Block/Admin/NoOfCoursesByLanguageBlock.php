<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\Utilities;

class NoOfCoursesByLanguageBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $arr = [];
        $courses = CourseDataManager::retrieves(Course::class, new DataClassRetrievesParameters());
        
        $categories = [];
        
        foreach($courses as $course)
        {
            $lang = CourseSettingsController::getInstance()->get_course_setting(
                $course, 
                CourseSettingsConnector::LANGUAGE);
            
            $categories[$lang] = Translation::get($lang, null, Utilities::COMMON_LIBRARIES);
            
            if ($arr[$lang])
            {
                $arr[$lang] = $arr[$lang] + 1;
            }
            else
            {
                
                $arr[$lang] = 1;
            }
        }
        $reporting_data->set_categories($categories);
        $reporting_data->set_rows(array(Translation::get('count')));
        
        foreach ($categories as $key => $name)
        {
            $reporting_data->add_data_category_row($key, Translation::get('count'), ($arr[$key]));
        }
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            Html::VIEW_TABLE,
            Html::VIEW_PIE);
    }
}
