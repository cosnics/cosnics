<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class UpdateAnswerExporterComponent extends Manager
{

    private $questions_cache;

    private $options_cache;

    private $matches_cache;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);

        $this->update_tracker_data($publication_id);
    }

    public function update_tracker_data($publication_id)
    {
        $condition = new EqualityCondition(Answer :: PROPERTY_PUBLICATION_ID, $publication_id);
        // $trackers = Tracker :: get_data(Answer :: CLASS_NAME, Manager :: APPLICATION_NAME, $condition);

        $question_types = array();
        $answer_count = 0;
        $count = 0;

        // while ($tracker = $trackers->next_result())
        // {

        // $count ++;
        // if ($count > 10000)
        // {
        // break;
        // }

        // $complex_question_id = $tracker->get_question_cid();
        // $object = $this->get_question($complex_question_id);

        // $type = $object->get_type();

        // switch ($type)
        // {
        // case SurveyMultipleChoiceQuestion :: get_type_name() :
        // $id = $object->get_id();
        // $options = $this->get_options($complex_question_id);

        // // dump($options);

        // $answer = $tracker->get_answer();
        // // dump("multioldanswer ".$id);
        // // dump($answer);
        // $new_answer = array();
        // foreach ($answer as $key => $option)
        // {
        // if ($object->get_answer_type() == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
        // {
        // $option_id = $options[$key];
        // $new_answer[$complex_question_id . "_" . $option_id] = 1;
        // }
        // else
        // {
        // // $option_id = $options[$option];
        // $option_id = $option;
        // $new_answer[$complex_question_id] = $option_id;
        // }
        // }
        // // dump("new answer");
        // // dump($new_answer);

        // // $tracker->set_answer($new_answer);
        // // $tracker->update();
        // break;

        // case SurveyMatrixQuestion :: get_type_name() :

        // $id = $object->get_id();
        // $options = $this->get_options($complex_question_id);

        // $matches = $this->get_matches($complex_question_id);

        // $answer = $tracker->get_answer();

        // foreach ($answer as $key => $option)
        // {
        // foreach ($option as $match_key => $match)
        // {
        // $n_answer = array();

        // if ($object->get_matrix_type() == SurveyMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
        // {
        // $option_id = $options[$key];
        // $match_id = $matches[$match_key];
        // }
        // else
        // {
        // $option_id = $options[$key];
        // $match_id = $matches[$match];
        // }
        // $n_answer[$option_id] = $match_id;
        // $new_answer[] = $n_answer;
        // }
        // }

        // // $tracker->set_answer($new_answer);
        // // $tracker->update();
        // break;

        // case SurveySelectQuestion :: get_type_name() :
        // $id = $object->get_id();
        // $options = $this->get_options($complex_question_id);
        // $answer = $tracker->get_answer();
        // // dump("selectoldanswer: ".$id);
        // // dump($answer);
        // $new_answer = array();
        // foreach ($answer as $key => $option)
        // {
        // if ($object->get_answer_type() == SurveySelectQuestion :: ANSWER_TYPE_CHECKBOX)
        // {
        // $option_id = $options[$key];
        // }
        // else
        // {
        // $option_id = $options[$option];
        // }
        // $new_answer[] = $option_id;
        // }

        // // dump("new answer");
        // // dump($new_answer);

        // // $tracker->set_answer($new_answer);
        // // $tracker->update();
        // break;

        // case SurveyMatchingQuestion :: get_type_name() :
        // // to implement
        // $id = $object->get_id();
        // $question_types[4][$id] = $answer_count ++;
        // break;

        // case SurveyOpenQuestion :: get_type_name() :
        // $id = $object->get_id();
        // $answer = $tracker->get_answer();
        // // dump("openoldanswer: ".$id);
        // // dump($answer);
        // // dump($answer);
        // // dump($answer[$complex_question_id.'_'.'0']);
        // if (strlen(strip_tags($answer[$complex_question_id . '_' . '0'])) > 0)
        // {
        // $text = strip_tags($answer[$complex_question_id . '_' . '0']);
        // }
        // break;

        // case SurveyRatingQuestion :: get_type_name() :

        // // to implement
        // $id = $object->get_id();
        // $question_types[6][$id] = $answer_count ++;
        // break;
        // }
        // }
    }

    private function get_question($complex_id)
    {
        if (! isset($this->questions_cache) || ! isset($this->questions_cache[$complex_id]))
        {
            $complex_question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ComplexContentObjectItem :: class_name(),
                $complex_id);
            $this->questions_cache[$complex_id] = $complex_question->get_ref_object();
        }
        return $this->questions_cache[$complex_id];
    }

    private function get_options($complex_id)
    {
        if (! isset($this->options_cache) || ! isset($this->options_cache[$complex_id]))
        {
            $object = $this->get_question($complex_id);
            $options = $object->get_options();
            $opts = array();
            while ($option = $options->next_result())
            {
                $opts[$option->get_display_order()] = $option->get_id();

                // $opts[$option->get_id()] = $option->get_id();
            }
            $this->options_cache[$complex_id] = $opts;
        }

        return $this->options_cache[$complex_id];
    }

    private function get_matches($complex_id)
    {
        if (! isset($this->matches_cache) || ! isset($this->matches_cache[$complex_id]))
        {
            $object = $this->get_question($complex_id);
            $matches = $object->get_matches();
            $mats = array();
            while ($match = $matches->next_result())
            {
                $mats[$match->get_display_order()] = $match->get_id();

                // $mats[$match->get_id()] = $match->get_id();
            }
            $this->matches_cache[$complex_id] = $mats;
        }

        return $this->matches_cache[$complex_id];
    }
}
?>