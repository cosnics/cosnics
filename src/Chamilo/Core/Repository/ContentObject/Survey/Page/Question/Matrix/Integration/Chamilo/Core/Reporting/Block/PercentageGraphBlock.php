<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;

class PercentageGraphBlock extends ReportingBlock
{
    const TOTAL = 'total';

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $answers = $this->get_parent()->get_answers($question->get_id());
        
        $reporting_data = new ReportingData();
        
        // option and matches of question
        $options = array();
        $matches = array();
        
        // matrix to store the answer count
        $answer_count = array();
        
        // get options and matches
        $opts = $question->get_options();
        foreach ($opts as $option)
        {
            $options[$option->get_id()] = $option->get_value();
        }
        
        $matchs = $question->get_matches();
        foreach ($matchs as $match)
        {
            $matches[$match->get_id()] = $match->get_value();
        }
        
        // create answer matrix for answer counting
        foreach ($options as $option_id => $option)
        {
            foreach ($matches as $match_id => $match)
            {
                $answer_count[$option_id][$match_id] = 0;
            }
        }
        
        // count answers
        foreach ($answers as $answer)
        {
            $options_answered = array();
            foreach ($answer as $key => $match_id)
            {
                $ids = explode('_', $key);
                $context_id = $ids[1];
                $options_answered[] = $context_id;
                $totals = array();
                $answer_count[$context_id][$match_id] ++;
                $answer_count[$context_id][self :: TOTAL] ++;
            }
        }
        
        // creating actual reporing data
        foreach ($matches as $match)
        {
            $reporting_data->add_row(strip_tags($match));
        }
        
        $totals = array();
        
        // percentage figures
        // total count
        foreach ($options as $option_id => $option)
        {
            foreach ($matches as $match_id => $match)
            {
                $totals[$match_id] = $totals[$match_id] + $answer_count[$option_id][$match_id];
            }
        }
        
        $total_count = $totals[self :: TOTAL];
        
        $match_count = count($matches);
        $total_index = $match_count - 1;
        
        foreach ($options as $option_id => $option)
        {
            $reporting_data->add_category($option);
            
            foreach ($matches as $match_id => $match)
            {
                if ($match_id != self :: TOTAL)
                {
                    $value = $answer_count[$option_id][$match_id] / $answer_count[$option_id][self :: TOTAL];
                    $percentage = number_format($value * 100, 2);
                    $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                }
            }
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
