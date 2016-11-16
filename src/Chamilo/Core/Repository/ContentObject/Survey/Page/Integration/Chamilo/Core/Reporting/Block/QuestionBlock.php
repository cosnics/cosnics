<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;

class QuestionBlock extends ReportingBlock
{

    public function count_data()
    {
        $questions = $this->get_parent()->get_questions();
        
        $reporting_data = new ReportingData();
        
        // creating actual reporing data
        foreach ($questions as $question)
        {
            $reporting_data->add_category($this->get_parent()->get_question_template_url($question));
        }
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
