<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Cron\Storage\DataClass\ExportJob;
use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataClass\SynchronizeAnswer;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class ConvertAnswersComponent extends Manager
{

    private $questions_cache;

    private $options_cache;

    private $matches_cache;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request::get(\Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID);

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {

                $cron_enabled = Configuration::getInstance()->get_setting(
                    array('Chamilo\Application\Survey', 'enable_export_cron_job'));

                $publication_id = $id;

                if (! $cron_enabled)
                {
                    $this->delete_old_tracker_data($id);
                    $this->update_tracker_data($id);
                    $status = SynchronizeAnswer::STATUS_SYNCHRONIZED;
                }
                else
                {
                    $export_job = new ExportJob();
                    $export_job->set_user_id($this->get_user_id());
                    $export_job->set_publication_id($publication_id);
                    $export_job->set_export_template_id(0);
                    $export_job->set_status(ExportJob::STATUS_NEW);
                    $export_job->set_export_type(ExportJob::EXPORT_TYPE_SYNCHRONIZE_ANSWERS);
                    $export_job->set_UUID(0);
                    if ($export_job->create())
                    {
                        $status = SynchronizeAnswer::STATUS_SYNCHRONISATION_IN_QUEUE;
                    }
                    else
                    {
                        $status = SynchronizeAnswer::STATUS_SYNCHRONISATION_NOT_IN_QUEUE;
                    }
                }

                $condition = new EqualityCondition(SynchronizeAnswer::PROPERTY_SURVEY_PUBLICATION_ID, $id);
                // $tracker = Tracker :: get_singular_data(
                // SynchronizeAnswer :: CLASS_NAME,
                // \Chamilo\Application\Survey\Manager :: APPLICATION_NAME,
                // $condition);

                // if ($tracker)
                // {
                // $tracker->set_created(time());
                // $tracker->set_status($status);
                // $tracker->update();
                // }
                // else
                // {
                // $parameters = array();
                // $parameters[SynchronizeAnswer :: PROPERTY_USER_ID] = $this->get_user_id();
                // $parameters[SynchronizeAnswer :: PROPERTY_SURVEY_PUBLICATION_ID] = $id;
                // $parameters[SynchronizeAnswer :: PROPERTY_STATUS] = $status;
                // Event :: trigger(
                // SynchronizeAnswer :: REGISTER_SYNCHRONIZE_EVENT,
                // \Chamilo\Application\Survey\Manager :: APPLICATION_NAME,
                // $parameters);
                // }
            }

            $this->redirect(
                Translation::get('AnswersSyncronized'),
                false,
                array(self::PARAM_ACTION => self::ACTION_BROWSE, Manager::PARAM_PUBLICATION_ID => $id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation::get('NoPublicationSelected')));
        }
    }

    public function update_tracker_data($publication_id)
    {
        $condition = new EqualityCondition(Answer::PROPERTY_PUBLICATION_ID, $publication_id);
        // $trackers = Tracker :: get_data(
        // Answer :: CLASS_NAME,
        // \Chamilo\Application\Survey\Manager :: APPLICATION_NAME,
        // $condition);

        $question_types = array();
        $answer_count = 0;
        $count = 0;

        // while ($tracker = $trackers->next_result())
        // {
        // $complex_question_id = $tracker->get_question_cid();
        // $object = $this->get_question($complex_question_id);

        // $type = $object->get_type();

        // switch ($type)
        // {
        // case SurveyMultipleChoiceQuestion :: get_type_name() :
        // $answer = $tracker->get_answer();

        // foreach ($answer as $option_id)
        // {
        // // $parameters = array();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_USER_ID] =
        // // $tracker->get_user_id();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveyMultipleChoiceQuestionAnswerTracker ::
        // // SAVE_MULTIPLE_CHOICE_QUESTION_ANSWER_EVENT, Manager :: APPLICATION_NAME, $parameters);
        // }
        // break;

        // case SurveyMatrixQuestion :: get_type_name() :
        // $answer = $tracker->get_answer();

        // foreach ($answer as $ids => $match_id)
        // {
        // $ids = explode('_', $ids);
        // $option_id = $ids[1];

        // // $parameters = array();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_MATCH_ID] = $match_id;
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveyMatrixQuestionAnswerTracker :: SAVE_MATRIX_QUESTION_ANSWER_EVENT,
        // // Manager :: APPLICATION_NAME, $parameters);
        // }
        // break;

        // case SurveySelectQuestion :: get_type_name() :
        // $answer = $tracker->get_answer();

        // foreach ($answer as $option_id)
        // {
        // // $parameters = array();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveySelectQuestionAnswerTracker :: SAVE_SELECT_QUESTION_ANSWER_EVENT,
        // // Manager :: APPLICATION_NAME, $parameters);
        // }
        // break;

        // case SurveyMatchingQuestion :: get_type_name() :
        // $answer = $tracker->get_answer();

        // foreach ($answer as $ids => $match_id)
        // {
        // $ids = explode('_', $ids);
        // $option_id = $ids[1];

        // // $parameters = array();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_MATCH_ID] = $match_id;
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_USER_ID] =
        // // $tracker->get_user_id();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveyMatchingQuestionAnswerTracker :: SAVE_MATCHING_QUESTION_ANSWER_EVENT,
        // // Manager :: APPLICATION_NAME, $parameters);
        // }
        // break;

        // case SurveyOpenQuestion :: get_type_name() :
        // $answer = $tracker->get_answer();

        // $text = $this->transcode_string(array_pop($answer));
        // if (strlen(strip_tags($text)) > 0)
        // {
        // // $text = strip_tags($text);
        // // $parameters = array();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_TEXT] = $text;
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveyOpenQuestionAnswerTracker :: SAVE_OPEN_QUESTION_ANSWER_EVENT, Manager
        // // :: APPLICATION_NAME, $parameters);
        // }
        // break;

        // case SurveyRatingQuestion :: get_type_name() :

        // $answer = $tracker->get_answer();

        // foreach ($answer as $rating)
        // {
        // // $parameters = array();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
        // // $tracker->get_survey_participant_id();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
        // // $complex_question_id;
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_RATING] = $rating;
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
        // // $tracker->get_context_path();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
        // // $tracker->get_publication_id();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
        // // $tracker->get_context_template_id();
        // // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
        // // $tracker->get_context_id();
        // // Event :: trigger(SurveyRatingQuestionAnswerTracker :: SAVE_RATING_QUESTION_ANSWER_EVENT,
        // // Manager :: APPLICATION_NAME, $parameters);
        // }
        // break;
        // }
        // }
    }

    private function delete_old_tracker_data($publication_id)
    {

        // $condition = new EqualityCondition(SurveyMatrixQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveyMatrixQuestionAnswerTracker :: CLASS_NAME, \application\survey\Manager
        // :: APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }

        // $condition = new EqualityCondition(SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveyMultipleChoiceQuestionAnswerTracker :: CLASS_NAME,
        // \application\survey\Manager :: APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }

        // $condition = new EqualityCondition(SurveyMatchingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveyMatchingQuestionAnswerTracker :: CLASS_NAME,
        // \application\survey\Manager :: APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }

        // $condition = new EqualityCondition(SurveySelectQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveySelectQuestionAnswerTracker :: CLASS_NAME, \application\survey\Manager
        // :: APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }

        // $condition = new EqualityCondition(SurveyRatingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveyRatingQuestionAnswerTracker :: CLASS_NAME, \application\survey\Manager
        // :: APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }

        // $condition = new EqualityCondition(SurveyOpenQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID,
        // $publication_id);
        // $trackers = Tracker :: get_data(SurveyOpenQuestionAnswerTracker :: CLASS_NAME, \application\survey\Manager ::
        // APPLICATION_NAME, $condition);
        // while ($tracker = $trackers->next_result())
        // {
        // $tracker->delete();
        // }
    }

    private function get_question($complex_id)
    {
        if (! isset($this->questions_cache) || ! isset($this->questions_cache[$complex_id]))
        {
            $complex_question = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(),
                $complex_id);
            $this->questions_cache[$complex_id] = $complex_question->get_ref_object();
        }
        return $this->questions_cache[$complex_id];
    }

    function transcode_string($string)
    {
        $stripped_answer = trim(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
        $stripped_answer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $stripped_answer);
        $stripped_answer = preg_replace('/[ \n\r\t]{2,}/', ' ', $stripped_answer);
        return $stripped_answer;
    }
}
?>