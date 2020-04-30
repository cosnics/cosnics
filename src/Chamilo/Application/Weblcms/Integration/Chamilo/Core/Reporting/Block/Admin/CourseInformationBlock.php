<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;

class CourseInformationBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $course = CourseDataManager::retrieve_by_id(Course::class, $this->getCourseId());
        
        $reporting_data->set_categories(array(Translation::get('Name'), Translation::get('Titular')));
        $reporting_data->set_rows(array(Translation::get('count')));
        
        $reporting_data->add_data_category_row(
            Translation::get('Name'), 
            Translation::get('count'), 
            $course->get_title());
        $reporting_data->add_data_category_row(
            Translation::get('Titular'), 
            Translation::get('count'), 
            $course->get_titular_string());
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }
}
