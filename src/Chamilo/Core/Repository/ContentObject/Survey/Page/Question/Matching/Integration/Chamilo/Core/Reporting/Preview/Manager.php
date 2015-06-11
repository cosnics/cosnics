<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Preview;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Preview\Storage\DataClass\Answer;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Template\TableTemplate;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Reporting\Preview\Manager
{
    // Actions
    const ACTION_TABLE = 'Table';
    const ACTION_GRAPH = 'Graph';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_GRAPH;

    // Url Creation
    function get_viewer_url($question_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_TABLE, TableTemplate :: PARAM_QUESTION_ID => $question_id));
    }

    public function get_answers($question_id)
    {
        $answers = array();

        $question = $this->get_question();
        $options = $question->get_options()->as_array();
        $matches = $question->get_matches()->as_array();

        $answer_count = rand(0, 100);

        for ($i = 0; $i <= $answer_count; $i ++)
        {
            foreach ($options as $option)
            {
                $answer = new Answer();
                $answer->set_question_id($question_id);
                $answer->set_option_id($option->get_id());
                $random_match = rand(0, (count($matches) - 1));
                $answer->set_match_id($matches[$random_match]->get_id());
                $answers[] = $answer;
            }
        }

        return $answers;
    }

    /**
     *
     * @return multitype:string
     */
    static public function get_available_actions()
    {
        return array(self :: ACTION_TABLE, self :: ACTION_GRAPH);
    }
}
?>