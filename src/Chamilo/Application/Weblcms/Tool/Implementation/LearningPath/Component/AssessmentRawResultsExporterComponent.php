<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathChildAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResult;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResultsExportController;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\QuestionResult;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\ComplexLearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Exports the raw results of all the assessments of a learning path
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentRawResultsExporterComponent extends Manager
{
    const COLUMN_COURSE_GROUP_ID = 'course_group_id';

    /**
     * The complex learning path items that are connected to assessments
     * 
     * @var int[]
     */
    private $complex_learning_path_item_assessment_mapper;

    /**
     * Runs this component
     */
    public function run()
    {
        ini_set('memory_limit', - 1);
        
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $additional_information_columns = array(
            self::COLUMN_COURSE_GROUP_ID => Translation::get(
                'CourseGroups', 
                null, 
                \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace('course_group')));
        
        $controller = new AssessmentResultsExportController(
            $this->get_assessments(), 
            $this->get_assessment_results(), 
            $additional_information_columns);
        
        $path = $controller->run();
        
        Filesystem::file_send_for_download($path, true);
        Filesystem::remove($path);
    }

    /**
     * Retrieves the assessments
     * 
     * @return Assessment
     */
    protected function get_assessments()
    {
        $learning_path_publication_id = $this->get_publication_id();
        $publication = WeblcmsDataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $learning_path_publication_id);
        
        $assessments = array();
        
        $this->get_assessments_from_learning_path($publication->get_content_object_id(), $assessments);
        
        return $assessments;
    }

    /**
     * Recursive function to retrieve all the assessments from a learning path and his children
     * 
     * @param int $learning_path_id
     * @param array $assessments
     */
    protected function get_assessments_from_learning_path($learning_path_id, &$assessments)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_PARENT), 
            new StaticConditionVariable($learning_path_id));
        
        $complex_content_object_items = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->get_type() == ComplexLearningPathItem::class_name())
            {
                $learning_path_item = $complex_content_object_item->get_ref_object();
                $content_object = $learning_path_item->get_reference_object();
                if ($content_object instanceof Assessment)
                {
                    $assessments[] = $content_object;
                    $this->complex_learning_path_item_assessment_mapper[$complex_content_object_item->get_id()] = $content_object->get_id();
                }
            }
            
            if ($complex_content_object_item->get_type() == ComplexLearningPath::class_name())
            {
                $this->get_assessments_from_learning_path($complex_content_object_item->get_ref(), $assessments);
            }
        }
    }

    /**
     * Returns the assessment results
     * 
     * @return AssessmentResult[]
     */
    protected function get_assessment_results()
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                LearningPathChildAttempt::class_name(),
                LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID),
            array_keys($this->complex_learning_path_item_assessment_mapper));
        
        $learning_path_item_attempts = WeblcmsTrackingDataManager::retrieves(
            LearningPathChildAttempt::class_name(),
            new DataClassRetrievesParameters($condition));
        
        $assessment_results = array();
        
        while ($learning_path_item_attempt = $learning_path_item_attempts->next_result())
        {
            /**
             *
             * @var LearningPathChildAttempt $learning_path_item_attempt
             */
            /**
             *
             * @var LearningPathAttempt $learning_path_attempt
             */
            $learning_path_attempt = WeblcmsTrackingDataManager::retrieve_by_id(
                LearningPathAttempt::class_name(), 
                $learning_path_item_attempt->get_learning_path_attempt_id());
            
            $assessment_result = new AssessmentResult(
                $learning_path_item_attempt->get_id(), 
                $this->complex_learning_path_item_assessment_mapper[$learning_path_item_attempt->get_learning_path_item_id()], 
                null, 
                array(), 
                $learning_path_item_attempt->get_start_time(), 
                $learning_path_item_attempt->get_score(), 
                $learning_path_item_attempt->get_total_time(), 
                $learning_path_attempt->get_user_id());
            
            $question_results = $this->get_question_results_from_learning_path_item_attempt(
                $assessment_result, 
                $learning_path_item_attempt);
            
            $assessment_result->set_question_results($question_results);
            
            $assessment_results[] = $assessment_result;
        }
        
        return $assessment_results;
    }

    /**
     * Retrieves the question results from the learning path item attempt
     * 
     * @param AssessmentResult $assessment_result
     * @param LearningPathChildAttempt $learning_path_item_attempt
     *
     * @return QuestionResult[]
     */
    protected function get_question_results_from_learning_path_item_attempt(AssessmentResult $assessment_result, 
        LearningPathChildAttempt $learning_path_item_attempt)
    {
        $question_results = array();
        
        $course_groups = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::get_course_groups_from_user_as_string(
            $assessment_result->get_user_id(), 
            $this->get_course_id());
        
        $additional_information = array(self::COLUMN_COURSE_GROUP_ID => $course_groups);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(), 
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID), 
            new StaticConditionVariable($learning_path_item_attempt->get_id()));
        
        $question_attempts = WeblcmsTrackingDataManager::retrieves(
            LearningPathQuestionAttempt::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($question_attempt = $question_attempts->next_result())
        {
            $question_results[] = new QuestionResult(
                unserialize($question_attempt->get_answer()), 
                $assessment_result, 
                $question_attempt->get_question_complex_id(), 
                $question_attempt->get_score(), 
                $additional_information);
        }
        
        return $question_results;
    }

    /**
     * Returns the publication id
     * 
     * @return int
     */
    protected function get_publication_id()
    {
        return Request::get(self::PARAM_PUBLICATION_ID);
    }
}
