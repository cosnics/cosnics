<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class PercentageGraphBlock extends ReportingBlock
{
    const COUNT = 'percentage';

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $answers = $this->get_parent()->get_answers($question->get_id());
        sort($answers);
        $reporting_data = new ReportingData();
        
        $options = array();
        
        // matrix to store the answer count
        $answer_count = array();
        
        $total_count = 0;
        foreach ($answers as $value)
        {
            $answer_count[$value] ++;
            $total_count ++;
        }
        $options = array_keys($answer_count);
        
        $answer_row = Translation :: get(self :: COUNT);
        $rows = array($answer_row);
        
        $reporting_data->set_rows($rows);
        
        foreach ($options as $option)
        {
            
            $reporting_data->add_category($option);
            $value = $answer_count[$option] / $total_count;
            $percentage = number_format($value * 100, 2);
            $reporting_data->add_data_category_row($option, Translation :: get(self :: COUNT), $percentage);
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_BAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_LINE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_PIE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_STACKED_AREA, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_STACKED_BAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_RADAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_3D_PIE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_RING, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_AREA, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_POLAR);
    }
}
