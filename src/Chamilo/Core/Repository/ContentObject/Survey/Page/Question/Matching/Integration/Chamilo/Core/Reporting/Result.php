<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting;

class Result
{
    const TOTAL = 'total';
    const ROW = 'row';
    const CATEGORY = 'category';
    const ABSOLUTE = 'absolute';
    const PERCENTAGE = 'percentage';

    static function calculate_result($question, $answers)
    {
        $result = array();
        
        $options = array();
        $matches = array();
        
        $opts = $question->get_options()->as_array();
        foreach ($opts as $option)
        {
            $options[$option->get_id()] = strip_tags($option->get_value());
        }
        
        $matchs = $question->get_matches()->as_array();
        foreach ($matchs as $match)
        {
            $matches[$match->get_id()] = strip_tags($match->get_value());
        }
        
        $result[self :: ROW] = $matches;
        $result[self :: CATEGORY] = $options;
        
        $answer_count = array();
        $total_matrix = array();
        
        foreach ($options as $option_id => $option)
        {
            foreach ($matches as $match_id => $match)
            {
                $answer_count[$option_id][$match_id] = 0;
                $total_matrix[self :: ROW][self :: ABSOLUTE][$match_id] = 0;
            }
            $total_matrix[self :: CATEGORY][self :: ABSOLUTE][$option_id] = 0;
        }
        
        $i = 0;
        foreach ($answers as $answer)
        {
            $i ++;
            $answer_count[$answer->get_option_id()][$answer->get_match_id()] ++;
            $total_matrix[self :: ROW][self :: ABSOLUTE][$answer->get_match_id()] ++;
            $total_matrix[self :: CATEGORY][self :: ABSOLUTE][$answer->get_option_id()] ++;
        }
        $total_matrix[self :: TOTAL][self :: ABSOLUTE] = $i;
        
        $result[self :: ABSOLUTE] = $answer_count;
        
        $answer_percentage = array();
        $total_count = 0;
        foreach ($answer_count as $option_id => $match_count)
        {
            $perc = 0;
            $c = 0;
            foreach ($match_count as $match_id => $count)
            {
                $percentage = number_format($count / $total_matrix[self :: TOTAL][self :: ABSOLUTE] * 100, 2);
                $answer_percentage[$option_id][$match_id] = $percentage;
                $match_percentage = number_format(
                    $total_matrix[self :: ROW][self :: ABSOLUTE][$match_id] /
                         $total_matrix[self :: TOTAL][self :: ABSOLUTE] * 100, 
                        2);
                $total_matrix[self :: ROW][self :: PERCENTAGE][$match_id] = $match_percentage;
                $c = $c + $count;
            }
            $option_percentage = number_format($c / $total_matrix[self :: TOTAL][self :: ABSOLUTE] * 100, 2);
            $total_matrix[self :: CATEGORY][self :: PERCENTAGE][$option_id] = $option_percentage;
            $total_count = $total_count + $c;
        }
        
        // $this should always be 100 %
        $total_percentage = number_format($total_count / $total_matrix[self :: TOTAL][self :: ABSOLUTE] * 100, 2);
        $total_matrix[self :: TOTAL][self :: PERCENTAGE] = $total_percentage;
        
        $result[self :: PERCENTAGE] = $answer_percentage;
        $result[self :: TOTAL] = $total_matrix;
        
        return $result;
    }
}
?>