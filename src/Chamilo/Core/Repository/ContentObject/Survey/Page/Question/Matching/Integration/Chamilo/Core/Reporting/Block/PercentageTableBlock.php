<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Result;
use Chamilo\Libraries\Platform\Translation;

class PercentageTableBlock extends ReportingBlock
{
    const TOTAL = 'total';

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $results = Result::calculate_result($question, $this->get_parent()->get_answers($question->get_id()));
        
        $reporting_data = new ReportingData();
        $total = Translation::get(self::TOTAL);
        
        foreach ($results[Result::ROW] as $match)
        {
            $reporting_data->add_row($match);
        }
        $reporting_data->add_row($total);
        
        foreach ($results[Result::CATEGORY] as $option_id => $option)
        {
            $reporting_data->add_category($option);
            
            foreach ($results[Result::ROW] as $match_id => $match)
            {
                $reporting_data->add_data_category_row(
                    $option, 
                    $match, 
                    $results[Result::PERCENTAGE][$option_id][$match_id]);
            }
            $reporting_data->add_data_category_row(
                $option, 
                $total, 
                $results[Result::TOTAL][Result::CATEGORY][Result::PERCENTAGE][$option_id]);
        }
        
        if (count($results[Result::CATEGORY]) > 1)
        {
            $reporting_data->add_category($total);
            
            foreach ($results[Result::ROW] as $match_id => $match)
            {
                $reporting_data->add_data_category_row(
                    $total, 
                    $match, 
                    $results[Result::TOTAL][Result::ROW][Result::PERCENTAGE][$match_id]);
            }
            $reporting_data->add_data_category_row(
                $total, 
                $total, 
                $results[Result::TOTAL][Result::TOTAL][Result::PERCENTAGE]);
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_CSV, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_XLSX, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_XML);
    }
}
