<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Result;

class PercentageGraphBlock extends ReportingBlock
{

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $results = Result::calculate_result($question, $this->get_parent()->get_answers($question->get_id()));
        
        $reporting_data = new ReportingData();
        
        foreach ($results[Result::ROW] as $match)
        {
            $reporting_data->add_row($match);
        }
        
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_BAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_LINE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_PIE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_STACKED_AREA, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_STACKED_BAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_RADAR, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_3D_PIE, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_RING, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_AREA, 
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_POLAR);
    }
}
