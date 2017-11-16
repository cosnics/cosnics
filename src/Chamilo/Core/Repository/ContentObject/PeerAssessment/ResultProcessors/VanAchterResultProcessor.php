<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\ResultProcessors;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\PeerAssessmentResultprocessor;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

class VanAchterResultProcessor extends PeerAssessmentResultprocessor
{
    const ALLOW_EMPTY_SCORES = false;
    const GRAPH_RANGE = 12;
    const GRAPH_OFFSET = 6;
    const FACTOR = 3;
    // blows up the range
    protected $allowed_scores = array(- 2, - 1, 0, 1, 2);

    protected function set_scores($scores)
    {
        parent::set_scores($scores);
    }

    public function is_valid($value)
    {
        return true;
    }

    /**
     * calculates van achter PA factor
     * 
     * @return type
     */
    public function calculate()
    {
        foreach ($this->scores as $user => $user_score)
        {
            // 5. sum of 4. per user (P.A. total score)
            $pa_totals += $this->row_sum($user);
        }
        // 6. 5. / n users (P.A. factor score)
        $pa_factor_score = $pa_totals / $this->row_count;
        // 7. P.A. factor score / n indicators (P.A. factor)
        return round($pa_factor_score / $this->col_count, 2);
    }

    /**
     * retrieves all given scores for this attempt preprocesses the given scores given scores are multiplied with factor
     * and the average of all given scores for an indicator is subtracted adds scores given to user_id to score array
     * 
     * @param Application $viewer
     * @param int $user_id
     * @param int $attempt_id
     */
    public function retrieve_scores(Application $viewer, $user_id, $attempt_id)
    {
        $group = $viewer->get_user_group($user_id);
        
        if ($group)
        {
            // get all the users in the current user's group
            $users = $viewer->get_group_users($group->get_id());
            // preprocess
            // foreach ($scores_given as $giver => $scores_given_by_user)
            foreach ($users as $giver_object)
            {
                $giver = $giver_object->get_id();
                
                // get the given scores for each of the users
                $scores_given_by_user = $viewer->get_user_scores_given($giver, $attempt_id);
                
                $row_count = 0;
                
                //
                foreach ($scores_given_by_user as $recipient => $recipient_score)
                {
                    $row_count ++;
                    
                    foreach ($recipient_score as $indicator_id => $indicator_score)
                    {
                        // 1. individual score * factor
                        $scores_given[$giver][$recipient][$indicator_id] = $indicator_score * self::FACTOR;
                    }
                }
                
                // reiterate indicators to make average of all scores
                foreach ($scores_given[$giver] as $recipient => $recipient_score)
                {
                    foreach ($recipient_score as $indicator_id => $indicator_score)
                    {
                        // 2. calculate average of all students per criterium
                        if (! isset($indicator_average[$giver][$indicator_id]))
                        {
                            $sum = array_reduce(
                                $scores_given[$giver], 
                                function ($result, $item) use ($indicator_id)
                                {
                                    return $result + $item[$indicator_id];
                                });
                            
                            $indicator_average[$giver][$indicator_id] = $sum / $row_count;
                        }
                        // 3. subtract 2. from 1.
                        // 4. make round one digit after comma
                        $scores_given[$giver][$recipient][$indicator_id] = round(
                            $scores_given[$giver][$recipient][$indicator_id] - $indicator_average[$giver][$indicator_id], 
                            2);
                    }
                    // add scores given to user_id to score array
                    $user_id = intval($user_id);
                    if ($recipient == $user_id)
                    {
                        $scores[$giver] = $scores_given[$giver][$recipient];
                    }
                }
            }
        }
        $this->set_scores($scores);
        return true;
    }

    public function allow_empty_scores()
    {
        return self::ALLOW_EMPTY_SCORES;
    }

    /**
     *
     * @return int
     */
    public function get_graph_offset()
    {
        return self::GRAPH_OFFSET;
    }

    /**
     *
     * @return int
     */
    public function get_graph_range()
    {
        return self::GRAPH_RANGE;
    }

    public function get_intro_title()
    {
        return Translation::get('VanAchterIntroTitle');
    }

    public function get_intro_description()
    {
        return Translation::get('VanAchterIntroDescription');
    }

    public function render_table($indicators, $users)
    {
        if (count($this->scores) > 0) // are scores available (depends on processing method)
        {
            $factor = $this->calculate();
            
            $html = array();
            
            $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
            $html[] = '<thead>';
            $html[] = '<tr>';
            $html[] = '<th>' . Translation::get('User') . '</th>';
            
            foreach ($indicators as $i)
            {
                $html[] = '<th>' . $i->get_title() . '</th>';
            }
            
            $html[] = '<th>' . Translation::get('Total') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tfoot>';
            $html[] = '<tr>';
            $html[] = '<th></th>';
            
            foreach ($indicators as $i)
            {
                $html[] = '<th style="text-align: center">' . round($this->col_avg($i->get_id()), 2) . '</th>';
            }
            
            $html[] = '<th style="text-align: center; font-size: larger; color: red">' . round($factor, 2) .
                 '</font></th>';
            
            $html[] = '</tr>';
            $html[] = '</tfoot>';
            $html[] = '<tbody>';
            
            $r = 0;
            
            foreach ($users as $u)
            {
                
                $class = ($r ++ % 2) ? 'odd' : 'even';
                
                $html[] = '<tr class="row_' . $class . '">';
                $html[] = '<td>' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';
                // $html[] = '<td><a href="' . $url . '">' . $u->get_firstname() . ' ' . $u->get_lastname() .
                // '</a></td>';
                
                foreach ($indicators as $i)
                {
                    $html[] = '<td style="text-align: center; font-size: larger; font-weight: bold">' .
                         $this->scores[$u->get_id()][$i->get_id()] . '</td>';
                }
                
                $html[] = '<td style="text-align: center">' . round($this->row_sum($u->get_id())) . '</td>';
                $html[] = '</tr>';
            }
            
            $html[] = '</tbody>';
            $html[] = '</table>';
            
            $html[] = '<br /><br />';
            
            foreach ($users as $user)
            {
                if ($user->get_id() == $this->user_id)
                    $graph = $this->render_graph($user, $this);
            }
            
            $html[] = '<div>' . $graph . '</div>';
            
            return implode(PHP_EOL, $html);
        }
        else
        {
            return false;
        }
    }
}
