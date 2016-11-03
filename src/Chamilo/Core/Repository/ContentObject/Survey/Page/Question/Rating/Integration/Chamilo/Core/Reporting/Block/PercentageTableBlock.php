<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Integration\Chamilo\Core\Reporting\Block;

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
        if (count($options) > 1)
        {
            $reporting_data->add_category(Translation :: get(self :: TOTAL));
            $reporting_data->add_data_category_row(
                Translation :: get(self :: TOTAL),
                Translation :: get(self :: COUNT),
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
