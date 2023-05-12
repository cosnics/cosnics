<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Core\Repository\Display\Preview;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Manager implements
    GlossaryDisplaySupport, LearningPathDisplaySupport, AssessmentDisplaySupport, ForumDisplaySupport,
    BlogDisplaySupport, WikiDisplaySupport
{

    /*
     * The question attempts @var QuestionAttempt[]
     */
    private $question_attempts;

    function run()
    {
        $this->buildTrackingService();

        $className = $this->get_root_content_object()->package() . '\Display';

        return $this->getApplicationFactory()->getApplication(
            $className,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     * Since this is a preview, no actual view event is triggered.
     *
     * @param $complex_topic_id
     */
    function forum_topic_viewed($complex_topic_id)
    {
    }

    /**
     * Since this is a preview, no views are logged and no count can be retrieved.
     *
     * @param $complex_topic_id
     * @return string
     */
    function forum_count_topic_views($complex_topic_id)
    {
        return '-';
    }

    /**
     * Returns whether or not the logged in user is a forum manager
     *
     * @param $user User
     *
     * @return boolean
     */
    function is_forum_manager($user)
    {
        return false;
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    function get_wiki_page_statistics_reporting_template_name()
    {
        $this->not_available(Translation::get('ImpossibleInPreviewMode'));
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    function get_wiki_statistics_reporting_template_name()
    {
        $this->not_available(Translation::get('ImpossibleInPreviewMode'));
    }

    /**
     * Preview mode, so no actual saving done.
     *
     * @param $complex_question_id int
     * @param $answer mixed
     * @param $score int
     */
    function save_assessment_answer($complex_question_id, $answer, $score, $hint = null)
    {
        $this->trackingService->saveAnswerForQuestion(
            Preview::get_root_content_object(),
            $this->getUser(),
            $this->getCurrentTreeNode(),
            $complex_question_id,
            $answer,
            $score,
            $hint);
    }

    /**
     * Preview mode, so no actual total score will be saved.
     *
     * @param $total_score int
     */
    function save_assessment_result($total_score)
    {
        $this->trackingService->saveAssessmentScore(
            Preview::get_root_content_object(),
            $this->getUser(),
            $this->getCurrentTreeNode(),
            $total_score);
    }

    /**
     * Preview mode, so there is no acrual attempt.
     */
    function get_assessment_current_attempt_id()
    {
        return $this->get_parameter(
            Manager::PARAM_LEARNING_PATH_ITEM_ID);
    }

    /**
     * Returns the assessment question attempts
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        return $this->trackingService->getQuestionAttempts(
            Preview::get_root_content_object(),
            $this->getUser(),
            $this->getCurrentTreeNode());
    }

    /**
     * Retrieves the question attempts for the selected assessment attempt
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt[]
     */
    protected function retrieve_question_attempts()
    {
        return $this->trackingService->getQuestionAttempts(
            Preview::get_root_content_object(),
            $this->getUser(),
            $this->getCurrentTreeNode());
    }

    function get_assessment_question_attempt($complex_question_id)
    {
        return $this->question_attempts[$complex_question_id];
    }

    /**
     * Preview mode is launched in standalone mode, so there's nothing to go back to.
     *
     * @return void
     */
    function get_assessment_back_url()
    {
    }

    /**
     * Preview mode is launched in standalone mode, so there's nothing to continue to.
     *
     * @return void
     */
    function get_assessment_continue_url()
    {
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::get_assessment_current_url()
     */
    function get_assessment_current_url()
    {
        return $this->get_url(array(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID => null));
    }

    function get_assessment_configuration()
    {
        return $this->getCurrentTreeNode()->getTreeNodeData()->getAssessmentConfiguration();
    }

    function get_assessment_parameters()
    {
        return [];
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_tracker()
     */
    function retrieve_learning_path_tracker()
    {
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_tracker_items()
     */
    function retrieve_learning_path_tracker_items($learning_path_tracker)
    {
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_item_attempt()
     */
    function retrieve_learning_path_item_attempt($learning_path_item_attempt_id)
    {
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::get_tree_menu_url()
     */
    function get_tree_menu_url()
    {
        $parameters = [];
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Preview\Manager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Preview\Manager::ACTION_DISPLAY;
        $parameters[\Chamilo\Core\Repository\Preview\Manager::PARAM_CONTENT_OBJECT_ID] = $this->get_root_content_object()->get_id();
        $parameters[Manager::PARAM_CHILD_ID] = '__NODE__';
        $parameters[Manager::PARAM_FULL_SCREEN] = $this->getRequest()->query->get(
            Manager::PARAM_FULL_SCREEN);

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::create_learning_path_item_tracker()
     */
    function create_learning_path_item_tracker($learning_path_attempt, $current_complex_content_object_item)
    {
    }

    /**
     * Get the url of the assessment result
     *
     * @param $complex_content_object_id int
     * @param $details unknown_type
     */
    function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details)
    {
    }

    /*
     * (non-PHPdoc) @see
     * \core\repository\content_object\assessment\AssessmentDisplaySupport::get_registered_question_ids()
     */
    public function get_registered_question_ids()
    {
        $question_ids = [];
        $attempts = $this->get_assessment_question_attempts();

        foreach ($attempts as $attempt)
        {
            $question_ids[] = $attempt->get_question_complex_id();
        }

        return $question_ids;
    }

    /**
     * Registers the question ids
     *
     * @param int[] $question_ids
     */
    public function register_question_ids($question_ids)
    {
        $this->question_attempts = $this->trackingService->registerQuestionAttempts(
            Preview::get_root_content_object(),
            $this->getUser(),
            $this->getCurrentTreeNode(),
            $question_ids);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::is_allowed_to_edit_attempt_data()
     */
    public function is_allowed_to_edit_learning_path_attempt_data()
    {
        return true;
    }
}
