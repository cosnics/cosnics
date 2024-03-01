<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Admin;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Translation\Translation;

class NoOfCoursesBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $count = CourseDataManager::count(Course::class);

        $reporting_data->set_categories([Translation::get('CourseCount')]);
        $reporting_data->set_rows([Translation::get('count')]);

        $reporting_data->add_data_category_row(Translation::get('CourseCount'), Translation::get('count'), $count);

        return $reporting_data;
    }

    public function get_views()
    {
        return [Html::VIEW_TABLE];
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
