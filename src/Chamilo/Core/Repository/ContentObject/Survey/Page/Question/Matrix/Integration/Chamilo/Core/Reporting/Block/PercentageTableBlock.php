<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Reporting\Block;

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

        // option and matches of question
        $options = array();
        $matches = array();

        // matrix to store the answer count
        $answer_count = array();

        // get options and matches
        $opts = $question->get_options();
        foreach ($opts as $option )
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

        $matches[self :: TOTAL] = Translation :: get(self :: COUNT);

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
        $summary_totals = array();

        foreach ($totals as $index => $value)
        {
            if ($total_count == 0)
            {
                $summary_totals[$index] = 0;
            }
            else
            {
                $percentage = number_format($value / $total_count * 100, 2);
                $summary_totals[$index] = $percentage;
            }
        }

        $match_count = count($matches);
        $total_index = $match_count - 1;

        foreach ($options as $option_id => $option)
        {
            $reporting_data->add_category($option);

            foreach ($matches as $match_id => $match)
            {
                if ($match_id == self :: TOTAL)
                {
                    $value = $answer_count[$option_id][$match_id] / $total_count;
                    $percentage = number_format($value * 100, 2);
                    $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                }
                else
                {
                    $value = $answer_count[$option_id][$match_id] / $answer_count[$option_id][self :: TOTAL];
                    $percentage = number_format($value * 100, 2);
                    $reporting_data->add_data_category_row($option, strip_tags($match), $percentage);
                }
            }
        }

        if (count($options) > 1)
        {
            $reporting_data->add_category(Translation :: get(self :: TOTAL));

            foreach ($matches as $match_id => $match)
            {
                $reporting_data->add_data_category_row(
                    Translation :: get(self :: TOTAL),
                    strip_tags($match),
                    $summary_totals[$match_id]);
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
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_CSV,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XLSX,
            \Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_XML);
    }
}
