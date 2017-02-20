<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPath;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Provides functionality to download documents included with an assessment.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 */
class DocumentSaverComponent extends Manager
{

    /**
     * Launches this component.
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->retrieve_assessment_documents(
            Request::get(self::PARAM_ATTEMPT_ID),
            Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID),
            Request::get(self::PARAM_ASSESSMENT_ID)
        );
    }

    /**
     * Retrieve assessment documents
     *
     * @param $attempt_id int The attempt it for the learning path
     * @param $ccoi_id int The complex content object item id
     * @param $assessment_id int The assessment id
     */
    protected function retrieve_assessment_documents($attempt_id, $ccoi_id, $assessment_id)
    {
        $document_open_question_ids = $this->retrieve_document_open_question_ids($assessment_id);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(),
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ITEM_ID
            ),
            new StaticConditionVariable($ccoi_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(),
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($attempt_id)
        );
        $condition = new AndCondition($conditions);

        $assessment_attempts = DataManager::retrieves(
            LearningPathItemAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );
        $assessment_attempt_ids = array();

        while ($assessment_attempt = $assessment_attempts->next_result())
        {
            $assessment_attempt_ids[] = $assessment_attempt->get_id();
        }

        $this->retrieve_assessment_attempts_documents($document_open_question_ids, $assessment_attempt_ids);
    }

    /**
     * Downloads the documents from the open questions in the assessment attempts.
     *
     * @param $document_open_question_ids array The ids of the open questions that can contain documents
     * @param $assessment_attempt_ids array The ids of the assessment attempts
     */
    protected function retrieve_assessment_attempts_documents($document_open_question_ids, $assessment_attempt_ids)
    {
        if (!is_array($assessment_attempt_ids))
        {
            $assessment_attempt_ids = array($assessment_attempt_ids);
        }

        if (count($document_open_question_ids) < 1)
        {
            $this->redirect_to_previous('NoOpenDocumentQuestions');
        }

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID
            ),
            $document_open_question_ids
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            $assessment_attempt_ids
        );

        $condition = new AndCondition($conditions);

        $question_attempt_trackers =
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt::class_name(
                ),
                null,
                $condition
            )->as_array();

        $document_ids = array();

        foreach ($question_attempt_trackers as $question_attempt_tracker)
        {
            $answer = unserialize($question_attempt_tracker->get_answer());
            if (!is_null($answer[2]) && strlen($answer[2]) > 0)
            {
                // Assign key to get ids without duplicates.
                $document_ids[$answer[2]] = $answer[2];
            }
        }

        if (count($document_ids) < 1)
        {
            $this->redirect_to_previous('NoDocumentsForAssessment');
        }

        $parameters = new ExportParameters($this->get_user_id(), ContentObjectExport::FORMAT_ZIP, $document_ids);
        $exporter = ContentObjectExportController::factory($parameters);
        $exporter->download();
    }

    /**
     * Returns the ids of the open questions that contain a document.
     *
     * @param $assessment_id int The assessment id
     *
     * @return array The ids of the open questions that can contain a document
     */
    protected function retrieve_document_open_question_ids($assessment_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectPath::class_name(), ComplexContentObjectItem::PROPERTY_PARENT
            ),
            new StaticConditionVariable($assessment_id)
        );
        
        $complex_questions = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(),
            $condition
        )->as_array();

        $open_document_question_ids = array();

        foreach ($complex_questions as $complex_question)
        {
            if ($complex_question->get_ref_object()->get_type() == AssessmentOpenQuestion::class_name() &&
                $this->is_open_question_document_allowed($complex_question->get_ref_object())
            )
            {
                $open_document_question_ids[] = $complex_question->get_id();
            }
        }

        return $open_document_question_ids;
    }

    /**
     * Returns true if the open question can contain documents.
     *
     * @param $open_question AssessmentOpenQuestion
     *
     * @return boolean True if the open question can contain documents
     */
    protected function is_open_question_document_allowed($open_question)
    {
        switch ($open_question->get_question_type())
        {
            case AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT :
                return true;

            case AssessmentOpenQuestion::TYPE_DOCUMENT :
                return true;
        }

        return false;
    }

    /**
     * Redirects to the previous page show the given message.
     *
     * @param $message string The message to display
     */
    protected function redirect_to_previous($message)
    {
        $params = array();

        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_VIEW_ASSESSMENT_RESULTS;
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = Request::get(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
        );
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = Request::get(
            \Chamilo\Application\Weblcms\Manager::PARAM_USERS
        );
        $params[self::PARAM_ATTEMPT_ID] = Request::get(self::PARAM_ATTEMPT_ID);
        $params[\Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = Request::get(
            \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );
        $params[self::PARAM_ASSESSMENT_ID] = Request::get(self::PARAM_ASSESSMENT_ID);
        $params[self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID] = Request::get(self::PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID);

        $this->redirect(Translation::get($message), false, $params);
    }
}
