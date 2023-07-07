<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Common\Export\Zip\ZipContentObjectExport;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.assessment.component
 */
class DocumentSaverComponent extends Manager
{

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        if (Request::get(self::PARAM_USER_ASSESSMENT))
        {
            $this->retrieve_assessment_attempt_documents(Request::get(self::PARAM_USER_ASSESSMENT));
        }
        elseif (Request::get(self::PARAM_ASSESSMENT))
        {
            $this->retrieve_assessment_documents(Request::get(self::PARAM_ASSESSMENT));
        }
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    public function is_open_question_document_allowed($open_question)
    {
        switch ($open_question->get_question_type())
        {
            case AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT :
            case AssessmentOpenQuestion::TYPE_DOCUMENT :
                return true;
        }

        return false;
    }

    private function redirect_to_previous($message)
    {
        $params = [];
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_VIEW_RESULTS;
        $params[self::PARAM_ASSESSMENT] = Request::get(self::PARAM_ASSESSMENT);
        $params[self::PARAM_USER_ASSESSMENT] = Request::get(self::PARAM_USER_ASSESSMENT);
        $this->redirectWithMessage(Translation::get($message), false, $params);
    }

    /**
     * Downloads the documents for a single assessment attempt.
     *
     * @param $assessment_attempt_tracker_id int The id of the assessment attempt.
     */
    protected function retrieve_assessment_attempt_documents($assessment_attempt_tracker_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ID
            ), new StaticConditionVariable($assessment_attempt_tracker_id)
        );
        $assessment_attempts = AssessmentAttempt::get_data(
            AssessmentAttempt::class, null, $condition
        );
        $open_document_question_ids = $this->retrieve_open_document_question_ids(
            $assessment_attempts[0]->get_assessment_id()
        );
        $this->retrieve_assessment_attempts_documents($open_document_question_ids, $assessment_attempt_tracker_id);
    }

    /**
     * Retrieves the documents submitted in a set of assessment attempts.
     *
     * @param $open_document_question_ids     array The ids of the open questions where documents may be added.
     * @param $assessment_attempt_tracker_ids array The ids of the assessment attempts for which the added documents are
     *                                        to be downloaded.
     */
    protected function retrieve_assessment_attempts_documents(
        $open_document_question_ids, $assessment_attempt_tracker_ids
    )
    {
        if (!is_array($assessment_attempt_tracker_ids))
        {
            $assessment_attempt_tracker_ids = [$assessment_attempt_tracker_ids];
        }
        if (count($open_document_question_ids) < 1)
        {
            $this->redirect_to_previous('NoOpenDocumentQuestions');
        }
        $conditions = [];
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                QuestionAttempt::class, QuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID
            ), $open_document_question_ids
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                QuestionAttempt::class, QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID
            ), $assessment_attempt_tracker_ids
        );
        $condition = new AndCondition($conditions);

        $question_attempt_trackers =
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::retrieves(
                QuestionAttempt::class, new DataClassRetrievesParameters($condition)
            );

        $document_ids = [];
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

        $parameters = new ExportParameters(
            $this->getCurrentWorkspace(), $this->get_user_id(), ContentObjectExport::FORMAT_ZIP, $document_ids, [],
            ZipContentObjectExport::TYPE_FLAT
        );

        $exporter = ContentObjectExportController::factory($parameters);
        $exporter->download();
    }

    /**
     * Retrieves all the documents submitted in all the attempts of the assessment.
     *
     * @param $publication_id int The id of the assessment publication.
     */
    protected function retrieve_assessment_documents($publication_id)
    {
        $open_document_question_ids = $this->retrieve_open_document_question_ids($publication_id);
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID
            ), new StaticConditionVariable($publication_id)
        );

        $assessment_attempt_trackers =
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::retrieves(
                AssessmentAttempt::class, new DataClassRetrievesParameters($condition)
            );

        $assessment_attempt_tracker_ids = [];
        foreach ($assessment_attempt_trackers as $assessment_attempt_tracker)
        {
            $assessment_attempt_tracker_ids[] = $assessment_attempt_tracker->get_id();
        }
        $this->retrieve_assessment_attempts_documents($open_document_question_ids, $assessment_attempt_tracker_ids);
    }

    /**
     * Retrieves the ids of the open questions where documents may be added.
     *
     * @param $publication_id int The id of the assessment publication to be searched.
     *
     * @return array The ids of the open questions where documents may be added.
     */
    protected function retrieve_open_document_question_ids($publication_id)
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $publication_id
        );
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($publication->get_content_object_id())
        );
        $complex_questions = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );
        // Array of open question ids in the publication that permit documents
        // to be submitted.
        $open_document_question_ids = [];
        foreach ($complex_questions as $complex_question)
        {
            if ($complex_question->get_ref_object()->getType() == AssessmentOpenQuestion::class &&
                $this->is_open_question_document_allowed($complex_question->get_ref_object()))
            {
                $open_document_question_ids[] = $complex_question->get_id();
            }
        }

        return $open_document_question_ids;
    }
}
