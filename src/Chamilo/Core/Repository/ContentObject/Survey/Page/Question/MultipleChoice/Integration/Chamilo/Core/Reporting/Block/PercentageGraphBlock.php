<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Integration\Chamilo\Core\Reporting\Block;

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
        
        $reporting_data = new ReportingData();
        
        $options = array();
        
        // matrix to store the answer count
        $answer_count = array();
        
        // get options and matches
        $opts = $question->get_options();
        foreach ($opts as $option)
        {
            $options[$option->get_id()] = $option->get_value();
        }
        
        // create answer matrix for answer counting
        foreach ($options as $option_id => $option)
        {
            $answer_count[$option_id] = 0;
        }
        
        // count answers
        foreach ($answers as $answer)
        {
            foreach ($answer as $key => $option_id)
            {
                $answer_count[$option_id] ++;
            }
        }
        
        // totalcount
        $total_count = 0;
        foreach ($options as $option_id => $option)
        {
            $total_count = $total_count + $answer_count[$option_id];
        }
        
        // creating actual reporing data
        $reporting_data->add_row(strip_tags(Translation :: get(self :: COUNT)));
        
        foreach ($options as $option_id => $option)
        {
            $reporting_data->add_category($option);
            $value = $answer_count[$option_id] / $total_count;
            $percentage = number_format($value * 100, 2);
            $reporting_data->add_data_category_row($option, strip_tags(Translation :: get(self :: COUNT)), $percentage);
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
