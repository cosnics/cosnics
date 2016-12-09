<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class PeerAssessmentResultprocessor
{

    protected $scores = array();

    protected $row_count;

    protected $col_count;

    function get_scores()
    {
        return $this->scores;
    }

    protected function set_scores($scores, $default = 0)
    {
        foreach ($scores as $r => $row)
        {
            if (! isset($col_count))
            {
                $col_count = count($row);
            }
            elseif (count($row) !== $col_count)
            {
                trigger_error('Different column counts', E_USER_ERROR);
                return;
            }
            foreach ($row as $c => $col)
            {
                if (! isset($scores[$r][$c]))
                {
                    $scores[$r][$c] = $default;
                }
                elseif (! $this->is_valid($scores[$r][$c]))
                {
                    trigger_error('Illegal value found', E_USER_ERROR);
                    return;
                }
            }
        }
        
        $this->scores = $scores;
        $this->row_count = count($scores);
        $this->col_count = $col_count;
    }

    public function row_sum($key)
    {
        return array_sum($this->scores[$key]);
    }

    public function row_avg($key)
    {
        return $this->row_sum($key) / $this->col_count;
    }

    public function col_sum($key)
    {
        return array_reduce(
            $this->scores, 
            function ($result, $item) use ($key)
            {
                return $result + $item[$key];
            });
    }

    public function col_avg($key)
    {
        return $this->col_sum($key) / $this->row_count;
    }

    public function get_allowed_scores()
    {
        return $this->allowed_scores;
    }

    /**
     *
     * @return int
     */
    abstract public function get_graph_offset();

    /**
     *
     * @return int
     */
    abstract public function get_graph_range();

    abstract public function is_valid($value);

    abstract public function calculate();

    /**
     * allows each processor to retrieve it's needed type of scores
     * 
     * @param Application $viewer
     * @param int $user_id
     * @param int $attempt_id
     * @return array
     */
    abstract public function retrieve_scores(Application $viewer, $user_id, $attempt_id);

    /**
     * used in PeerAssessmentViewerForm to enable/disable empty input field rule
     */
    abstract public function allow_empty_scores();

    abstract public function get_intro_title();

    abstract public function get_intro_description();

    abstract public function render_table($indicators, $users);
}
