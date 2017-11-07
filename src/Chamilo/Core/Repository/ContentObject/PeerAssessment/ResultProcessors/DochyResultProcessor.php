<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\ResultProcessors;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\PeerAssessmentResultprocessor;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

class DochyResultProcessor extends PeerAssessmentResultprocessor
{
    const ALLOW_EMPTY_SCORES = true;
    const GRAPH_RANGE = 4;
    const GRAPH_OFFSET = 1;

    protected $allowed_scores = array(- 1, 0, 1, 2, 3);

    private $neutral_score = 2;

    private $viewer;

    private $user_id;

    protected function set_scores($scores)
    {
        parent::set_scores($scores, $this->neutral_score);
    }

    public function is_valid($value)
    {
        return in_array($value, $this->allowed_scores);
    }

    public function calculate()
    {
        // get the settings
        $settings = $this->viewer->get_settings($this->viewer->get_publication_id());
        
        $count = $this->row_count;
        
        // get an array of the row totals
        $row_totals = array_map(function ($item)
        {
            return array_sum($item);
        }, $this->scores);
        
        // filter self assessment
        if ($settings->get_filter_self())
        {
            unset($row_totals[$this->user_id]);
            $count --;
        }
        
        // get the sum of the row totals
        $sum = array_sum($row_totals);
        
        // filter high/low values
        if ($settings->get_filter_min_max())
        {
            $sum -= min($row_totals);
            $sum -= max($row_totals);
            $count -= 2;
        }
        
        // return the user average divided by the number of indicators times two (= neutral score)
        return ($sum / $count) / ($this->col_count * $this->neutral_score);
    }

    public function retrieve_scores(Application $viewer, $user_id, $attempt_id)
    {
        $this->viewer = $viewer;
        $this->user_id = $user_id;
        
        $this->set_scores($viewer->get_user_scores_received($user_id, $attempt_id));
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
        return Translation::get('DochyIntroTitle');
    }

    public function get_intro_description()
    {
        return Translation::get('DochyIntroDescription');
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
            
            $html[] = '<th>' . Translation::get('Average') . '</th>';
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
                
                /*
                 * $url = $this->get_url(array( self :: PARAM_ACTION => self :: ACTION_VIEW_USER_RESULTS, self
                 * :: PARAM_ATTEMPT => $this->attempt_id, self :: PARAM_USER => $this->user_id, 'user2' => $u->get_id()
                 * ));
                 */
                
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
                
                $html[] = '<td style="text-align: center">' . round($this->row_avg($u->get_id()), 2) . '</td>';
                $html[] = '</tr>';
            }
            
            $html[] = '</tbody>';
            $html[] = '</table>';
            
            $html[] = '<br /><br />';
            
            foreach ($users as $user)
            {
                // if($user->get_id() == $this->user_id) $graph = $this->render_graph($user, $this);
            }
            
            $html[] = '<div>' . '$graph' . '</div>';
            
            return implode(PHP_EOL, $html);
        }
        else
        {
            return false;
        }
    }
}
