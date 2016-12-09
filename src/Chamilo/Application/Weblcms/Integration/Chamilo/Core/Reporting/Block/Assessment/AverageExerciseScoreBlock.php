<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\CourseBlock;
use Chamilo\Core\Reporting\ReportingData;

class AverageExerciseScoreBlock extends CourseBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_PIE);
    }
}
