<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;

class PercentageTableBlock extends ReportingBlock
{
    const NO_ANSWER = 'noAnswer';
    const COUNT = 'percentage';
    const TOTAL = 'total';

    public function count_data()
    {
        $question = $this->get_parent()->get_question();
        $answers = $this->get_parent()->get_answers($question->get_id());
               
        $reporting_data = new ReportingData();

        $options = array();

        // matrix to store the answer count
        $answer_count = array();

        // get options and matches
        $options = $question->getOptions();
        

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

        if (count($options) > 1)
        {
            $reporting_data->add_category(Translation :: get(self :: TOTAL));
            $reporting_data->add_data_category_row(
                Translation :: get(self :: TOTAL),
                strip_tags(Translation :: get(self :: COUNT)),
                100);
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_CSV,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XLSX,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XML);
    }
}
