<?php
namespace Chamilo\Application\Survey\Cron\ExportJob;

use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\Open;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\Rating;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\Select;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Application\Survey\Cron\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class ExportJobManager
{
    const TYPE_EXPORT_FINISHED = 1;
    const TYPE_EXPORT_NOT_FINISHED = 2;
    const TYPE_DOCUMENT_NOT_AVAILABLE = 3;
    const TYPE_SYNCHRONIZATION_FINISHED = 4;
    const TYPE_SYNCHRONIZATION_NOT_FINISHED = 5;
    const STATUS_NO_DOCUMENT_REGISTRATION = 1;
    const STATUS_NO_DOCUMENT_CREATED = 2;
    const STATUS_DOCUMENT_CREATED = 3;
    const STATUS_ANSWERS_SYNCHRONIZED = 4;
    const STATUS_ANSWERS_NOT_SYNCHRONIZED = 5;

    private static $questions_cache;

    static function launch_job()
    {

        // $conditions = array();
        // $conditions[] = new EqualityCondition(ExportJob :: PROPERTY_UUID, '0');
        // $conditions[] = new EqualityCondition(ExportJob :: PROPERTY_STATUS, ExportJob :: STATUS_NEW);
        // $condition = new AndCondition($conditions);
        // $export_jobs = DataManager :: retrieve_export_jobs($condition);

        // $UUID = uniqid($_SERVER['SERVER_ADDR'], true);

        // echo ' UUID=' . $UUID . "\n";

        // while ($export_job = $export_jobs->next_result())
        // {
        // $export_job->set_UUID($UUID);
        // $export_job->update();
        // }

        // $conditions = array();
        // $conditions[] = new EqualityCondition(ExportJob :: PROPERTY_UUID, $UUID);
        // $conditions[] = new EqualityCondition(ExportJob :: PROPERTY_STATUS, ExportJob :: STATUS_NEW);
        // $condition = new AndCondition($conditions);
        // $export_jobs = DataManager :: retrieve_export_jobs($condition);

        // while ($export_job = $export_jobs->next_result())
        // {

        // if ($export_job->get_export_type() == ExportJob :: EXPORT_TYPE_TEMPLATE_EXPORT)
        // {

        // $export_template = DataManager ::
        // get_instance()->retrieve_export_template_by_id($export_job->get_export_template_id());

        // $condition = new EqualityCondition(Export :: PROPERTY_EXPORT_JOB_ID,
        // $export_job->get_id());

        // $export_tracker = Tracker :: get_singular_data(Export :: CLASS_NAME,
        // \Chamilo\Application\Survey\Manager :: APPLICATION_NAME, $condition);

        // $export_type = 'xlsx';
        // $export = Exporter :: factory($export_template, $export_template->get_publication_id());
        // $file = $export->save();

        // $conditions = array();
        // $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
        // $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, File :: get_type_name());
        // $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, true);
        // $condition = new AndCondition($conditions);

        // $registration = \Chamilo\Core\Admin\Storage\DataManager :: count_registrations($condition);
        // if ($registration > 0)
        // {
        // $html_object = new File();
        // $html_object->set_title($export_template->get_name());
        // $html_object->set_description($export_template->get_description());
        // $html_object->set_parent_id(0);
        // $html_object->set_owner_id($export_job->get_user_id());
        // $html_object->set_filename($export->get_file_name() . '.' . $export_type);

        // $html_object->set_in_memory_file($file);

        // if (! $html_object->create())
        // {
        // $status = self :: STATUS_NO_DOCUMENT_CREATED;
        // }
        // else
        // {
        // $status = self :: STATUS_DOCUMENT_CREATED;
        // }

        // }
        // else
        // {
        // $status = self :: STATUS_NO_DOCUMENT_REGISTRATION;

        // }
        // }
        // elseif ($export_job->get_export_type() == ExportJob :: EXPORT_TYPE_SYNCHRONIZE_ANSWERS)
        // {

        // $condition = new EqualityCondition(SynchronizeAnswer :: PROPERTY_SURVEY_PUBLICATION_ID,
        // $export_job->get_publication_id());
        // $synchronize_tracker = Tracker :: get_singular_data(SynchronizeAnswer :: CLASS_NAME,
        // \Chamilo\Application\Survey\Manager :: APPLICATION_NAME, $condition);

        // if ($synchronize_tracker)
        // {
        // $id = $export_job->get_publication_id();
        // ExportJobManager :: delete_old_tracker_data($id);
        // ExportJobManager :: update_tracker_data($id);

        // $status = self :: STATUS_ANSWERS_SYNCHRONIZED;
        // }
        // else
        // {
        // $status = self :: STATUS_ANSWERS_NOT_SYNCHRONIZED;
        // }

        // }

        // switch ($status)
        // {
        // case self :: STATUS_NO_DOCUMENT_REGISTRATION :
        // $export_job->set_status(ExportJob :: STATUS_NOT_DONE);
        // $export_job->update();
        // $export_tracker->set_status(Export :: STATUS_EXPORT_NOT_CREATED);
        // $export_tracker->set_finished(time());
        // $export_tracker->update();
        // echo ' Job NOT Done : ' . "\n";
        // echo ' Export registration id: ' . $export_template->get_export_registration_id() . "\n";
        // echo ' Template id: ' . $export_template->get_id() . "\n";
        // echo ' Publication id: ' . $export_template->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // ExportJobManager :: send_mail($export_job->get_user_id(), ExportJobManager :: get_mail_message(self ::
        // TYPE_DOCUMENT_NOT_AVAILABLE, $export_template->get_publication_id()), $export_template);
        // break;

        // case self :: STATUS_NO_DOCUMENT_CREATED :
        // $export_job->set_status(ExportJob :: STATUS_NOT_DONE);
        // $export_job->update();
        // $export_tracker->set_status(Export :: STATUS_EXPORT_NOT_CREATED);
        // $export_tracker->set_finished(time());
        // $export_tracker->update();
        // echo ' Job NOT Done : ' . "\n";
        // echo ' Export registration id: ' . $export_template->get_export_registration_id() . "\n";
        // echo ' Template id: ' . $export_template->get_id() . "\n";
        // echo ' Publication id: ' . $export_template->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // ExportJobManager :: send_mail($export_job->get_user_id(), ExportJobManager :: get_mail_message(self ::
        // TYPE_EXPORT_NOT_FINISHED, $export_template->get_publication_id()), $export_template);
        // break;

        // case self :: STATUS_DOCUMENT_CREATED :
        // $export_job->set_status(ExportJob :: STATUS_DONE);
        // $export_job->update();
        // $export_tracker->set_status(Export :: STATUS_EXPORT_CREATED);
        // $export_tracker->set_finished(time());
        // $export_tracker->update();
        // echo ' Job Done : ' . "\n";
        // echo ' Export registration id: ' . $export_template->get_export_registration_id() . "\n";
        // echo ' Template id: ' . $export_template->get_id() . "\n";
        // echo ' Publication id: ' . $export_template->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // ExportJobManager :: send_mail($export_job->get_user_id(), ExportJobManager :: get_mail_message(self ::
        // TYPE_EXPORT_FINISHED, $export_template->get_publication_id(), $html_object->get_id()), $export_template);
        // break;
        // case self :: STATUS_ANSWERS_SYNCHRONIZED :
        // $export_job->set_status(ExportJob :: STATUS_DONE);
        // $export_job->update();
        // $synchronize_tracker->set_created(time());
        // $synchronize_tracker->set_status(SynchronizeAnswer :: STATUS_SYNCHRONIZED);
        // $synchronize_tracker->update();
        // echo ' SYNCHRONIZATION Job Done : ' . "\n";
        // echo ' Publication id: ' . $export_job->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // ExportJobManager :: send_mail($export_job->get_user_id(), ExportJobManager :: get_mail_message(self ::
        // TYPE_SYNCHRONIZATION_FINISHED, $export_job->get_publication_id()));
        // break;
        // case self :: STATUS_ANSWERS_NOT_SYNCHRONIZED :
        // $export_job->set_status(ExportJob :: STATUS_DONE);
        // $export_job->update();
        // $synchronize_tracker->set_created(time());
        // $synchronize_tracker->set_status(SynchronizeAnswer :: STATUS_NOT_SYNCHRONIZED);
        // $synchronize_tracker->update();
        // echo ' SYNCHRONIZATION Job NOT Done : ' . "\n";
        // echo ' Publication id: ' . $export_job->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // ExportJobManager :: send_mail($export_job->get_user_id(), ExportJobManager :: get_mail_message(self ::
        // TYPE_SYNCHRONIZATION_NOT_FINISHED, $export_job->get_publication_id()));
        // break;
        // default :
        // echo ' Job NOT Done : ' . "\n";
        // echo ' NO STATUS FOUND ' . "\n";
        // echo ' Template id: ' . $export_template->get_id() . "\n";
        // echo ' Publication id: ' . $export_template->get_publication_id() . "\n";
        // echo ' Job id: ' . $export_job->get_id() . "\n";
        // break;
        // }

        // }
    }

    static function send_mail($user_id, $message, $export_template)
    {
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_user($user_id);
        $to_email = $user->get_email();

        $name = PlatformSetting :: get('administrator_firstname', 'admin') . ' ' .
             PlatformSetting :: get('administrator_surname', 'admin');
        $email = PlatformSetting :: get('administrator_email', 'admin');

        if ($export_template)
        {
            $header = Translation :: get('ExportHeader') . ' ' . $export_template->get_name();
        }
        else
        {
            $header = Translation :: get('AnswerSynchronizationHeader');
        }

        $mail = new Mail($header, $message, $to_email, true, array(), array(), $name, $email, $name, $email);

        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
            
            echo 'Mail send to: ' . $name . ' ' . $email . "\n";
            echo '     Message: ' . $message . "\n";
        }
        catch(\Exception $ex)
        {
            echo 'Mail not send to: ' . $name . ' ' . $email . "\n";
            echo '     Message: ' . $message . "\n";
        }
    }

    static function get_mail_message($type, $publication_id, $document_id)
    {
        $message = array();

        switch ($type)
        {
            case self :: TYPE_DOCUMENT_NOT_AVAILABLE :
                $message[] = Translation :: get("ExportDocumentNotAvailable");
                $click_message = Translation :: get('ClickToGoToSurveyTool');
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Survey\Manager :: package();
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Manager :: ACTION_BROWSE;

                $redirect = new Redirect($parameters);
                $url = $redirect->getUrl();
                break;
            case self :: TYPE_EXPORT_FINISHED :
                $message[] = Translation :: get("ExportAvailable");
                $click_message = Translation :: get('ClickTGoToExport');
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager :: package();
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $document_id;

                $redirect = new Redirect($parameters);
                $url = $redirect->getUrl();
                break;
            case self :: TYPE_EXPORT_NOT_FINISHED :
                $message[] = Translation :: get("ExportNotAvailable");
                $click_message = Translation :: get('ClickToGoToExportTool');
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Survey\Manager :: package();
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Manager :: ACTION_EXPORT;
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID] = $publication_id;

                $redirect = new Redirect($parameters);
                $url = $redirect->getUrl();
                break;
            case self :: TYPE_SYNCHRONIZATION_FINISHED :
                $message[] = Translation :: get("AnswerSynchronizationFinished");
                $click_message = Translation :: get('ClickToGoToExportTool');
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Survey\Manager :: package();
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Manager :: ACTION_EXPORT;
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID] = $publication_id;

                $redirect = new Redirect($parameters);
                $url = $redirect->getUrl();
                break;
            case self :: TYPE_SYNCHRONIZATION_NOT_FINISHED :
                $message[] = Translation :: get("AnswerSynchronizationNotFinished");
                $click_message = Translation :: get('ClickToGoToExportTool');
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Survey\Manager :: package();
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Manager :: ACTION_EXPORT;
                $parameters[\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID] = $publication_id;

                $redirect = new Redirect($parameters);
                $url = $redirect->getUrl();
                break;
        }

        $message[] = '<br/><br/>';
        $message[] = '<a href=' . $url . '>' . $click_message . '</a>';

        $message[] = '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
        $message[] = '<br/><a href=' . $url . '>' . $url . '</a>';
        $message[] = '</p>';

        return implode(PHP_EOL, $message);
    }

    static public function update_tracker_data($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_PUBLICATION_ID),
            $publication_id);
        $answers = DataManager :: retrieves(Answer :: class_name(), new DataClassRetrievesParameters($condition));

        while ($raw_answer = $answers->next_result())
        {
            $complex_question_id = $raw_answer->get_question_cid();
            $object = ExportJobManager :: get_question($complex_question_id);

            $type = $object->get_type();

            switch ($type)
            {
                case MultipleChoice :: get_type_name() :
                    $answer = $raw_answer->get_answer();

                    foreach ($answer as $option_id)
                    {
                        // $parameters = array();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_USER_ID] =
                        // $tracker->get_user_id();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveyMultipleChoiceQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveyMultipleChoiceQuestionAnswerTracker ::
                        // SAVE_MULTIPLE_CHOICE_QUESTION_ANSWER_EVENT, \application\survey\Manager :: APPLICATION_NAME,
                        // $parameters);
                    }
                    break;

                case Matrix :: get_type_name() :
                    $answer = $raw_answer->get_answer();

                    foreach ($answer as $ids => $match_id)
                    {
                        $ids = explode('_', $ids);
                        $option_id = $ids[1];

                        // $parameters = array();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_MATCH_ID] = $match_id;
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveyMatrixQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveyMatrixQuestionAnswerTracker :: SAVE_MATRIX_QUESTION_ANSWER_EVENT,
                        // \application\survey\Manager :: APPLICATION_NAME, $parameters);
                    }
                    break;

                case Select :: get_type_name() :
                    $answer = $raw_answer->get_answer();

                    foreach ($answer as $option_id)
                    {
                        // $parameters = array();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveySelectQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveySelectQuestionAnswerTracker :: SAVE_SELECT_QUESTION_ANSWER_EVENT,
                        // \application\survey\Manager :: APPLICATION_NAME, $parameters);
                    }
                    break;

                case Matching :: get_type_name() :
                    $answer = $raw_answer->get_answer();

                    foreach ($answer as $ids => $match_id)
                    {
                        $ids = explode('_', $ids);
                        $option_id = $ids[1];

                        // $parameters = array();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_OPTION_ID] = $option_id;
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_MATCH_ID] = $match_id;
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_USER_ID] =
                        // $tracker->get_user_id();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveyMatchingQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveyMatchingQuestionAnswerTracker :: SAVE_MATCHING_QUESTION_ANSWER_EVENT,
                        // \application\survey\Manager :: APPLICATION_NAME, $parameters);
                    }
                    break;

                case Open :: get_type_name() :
                    $answer = $raw_answer->get_answer();

                    $text = ExportJobManager :: transcode_string(array_pop($answer));
                    if (strlen(strip_tags($text)) > 0)
                    {
                        // $text = strip_tags($text);
                        // $parameters = array();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_TEXT] = $text;
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveyOpenQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveyOpenQuestionAnswerTracker :: SAVE_OPEN_QUESTION_ANSWER_EVENT,
                        // \application\survey\Manager :: APPLICATION_NAME, $parameters);
                    }
                    break;

                case Rating :: get_type_name() :

                    $answer = $raw_answer->get_answer();

                    foreach ($answer as $rating)
                    {
                        // $parameters = array();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID] =
                        // $tracker->get_survey_participant_id();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_COMPLEX_QUESTION_ID] =
                        // $complex_question_id;
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_RATING] = $rating;
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_PATH] =
                        // $tracker->get_context_path();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_PUBLICATION_ID] =
                        // $tracker->get_publication_id();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_USER_ID] = $tracker->get_user_id();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] =
                        // $tracker->get_context_template_id();
                        // $parameters[SurveyRatingQuestionAnswerTracker :: PROPERTY_CONTEXT_ID] =
                        // $tracker->get_context_id();
                        // Event :: trigger(SurveyRatingQuestionAnswerTracker :: SAVE_RATING_QUESTION_ANSWER_EVENT,
                        // \application\survey\Manager :: APPLICATION_NAME, $parameters);
                    }
                    break;
            }
        }
    }

    static function delete_old_tracker_data($publication_id)
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

    static function get_question($complex_id)
    {
        if (! isset(ExportJobManager :: $questions_cache) || ! isset(ExportJobManager :: $questions_cache[$complex_id]))
        {
            $complex_question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ComplexContentObjectItem :: class_name(),
                $complex_id);
            ExportJobManager :: $questions_cache[$complex_id] = $complex_question->get_ref_object();
        }
        return ExportJobManager :: $questions_cache[$complex_id];
    }

    static function transcode_string($string)
    {
        $stripped_answer = trim(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
        $stripped_answer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $stripped_answer);
        $stripped_answer = preg_replace('/[ \n\r\t]{2,}/', ' ', $stripped_answer);
        return $stripped_answer;
    }
}
?>