<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\PreviewStorage;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeRenderer;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

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

    function run()
    {
        $className = $this->get_root_content_object()->package() . '\Display';
        
        $factory = new ApplicationFactory(
            $className, 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
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
    function save_assessment_answer($complex_question_id, $answer, $score, $hint)
    {
        $question_attempt = $this->get_assessment_question_attempt($complex_question_id);
        $question_attempt->set_answer($answer);
        $question_attempt->set_score($score);
        $question_attempt->set_hint($hint);
        
        return $question_attempt->update();
    }

    /**
     * Preview mode, so no actual total score will be saved.
     * 
     * @param $total_score int
     */
    function save_assessment_result($total_score)
    {
        $current_node = $this->get_current_node();
        $item_attempt = $current_node->get_current_attempt();
        
        if (! $item_attempt instanceof AbstractItemAttempt)
        {
            return;
        }
        else
        {
            $item_attempt->set_score($total_score);
            $item_attempt->set_total_time($item_attempt->get_total_time() + (time() - $item_attempt->get_start_time()));
            $learning_path_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $current_node->get_complex_content_object_item()->get_ref());
            $mastery_score = $learning_path_item->get_mastery_score();
            
            if ($mastery_score)
            {
                $status = ($total_score >= $mastery_score) ? AbstractItemAttempt::STATUS_PASSED : AbstractItemAttempt::STATUS_FAILED;
            }
            else
            {
                $status = AbstractItemAttempt::STATUS_COMPLETED;
            }
            
            $item_attempt->set_status($status);
            
            return $item_attempt->update();
        }
    }

    /**
     * Preview mode, so there is no acrual attempt.
     */
    function get_assessment_current_attempt_id()
    {
        return $this->get_current_node()->get_current_attempt()->get_id();
    }

    function get_assessment_question_attempts()
    {
        return PreviewStorage::getInstance()->retrieve_learning_path_question_attempts(
            $this->get_current_node()->get_current_attempt());
    }

    function get_assessment_question_attempt($complex_question_id)
    {
        return PreviewStorage::getInstance()->retrieve_learning_path_question_attempt(
            $this->get_current_node()->get_current_attempt(), 
            $complex_question_id);
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
        $complex_content_object_item = $this->get_current_node()->get_complex_content_object_item();
        $learning_path_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $complex_content_object_item->get_ref());
        
        return $learning_path_item->get_configuration();
    }

    function get_assessment_parameters()
    {
        return array();
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_tracker()
     */
    function retrieve_learning_path_tracker()
    {
        $attempt = PreviewStorage::getInstance()->retrieve_learning_path_attempt(
            $this->get_parent()->get_root_content_object()->get_id());
        
        if (! $attempt instanceof AbstractAttempt)
        {
            $attempt = new DummyAttempt();
            $attempt->set_user_id($this->get_user_id());
            $attempt->set_content_object_id($this->get_root_content_object()->get_id());
            $attempt->set_progress(0);
            $attempt->create();
        }
        return $attempt;
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_tracker_items()
     */
    function retrieve_learning_path_tracker_items($learning_path_tracker)
    {
        return PreviewStorage::getInstance()->retrieve_learning_path_item_attempts($learning_path_tracker);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_item_attempt()
     */
    function retrieve_learning_path_item_attempt($learning_path_item_attempt_id)
    {
        return PreviewStorage::getInstance()->retrieve_learning_path_item_attempt($learning_path_item_attempt_id);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::get_learning_path_tree_menu_url()
     */
    function get_learning_path_tree_menu_url()
    {
        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Preview\Manager::context();
        $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Preview\Manager::ACTION_DISPLAY;
        $parameters[\Chamilo\Core\Repository\Preview\Manager::PARAM_CONTENT_OBJECT_ID] = $this->get_root_content_object()->get_id();
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID] = LearningPathTreeRenderer::NODE_PLACEHOLDER;
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN] = $this->getRequest()->query->get(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN);
        
        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::create_learning_path_item_tracker()
     */
    function create_learning_path_item_tracker($learning_path_attempt, $current_complex_content_object_item)
    {
        $item_attempt = new DummyItemAttempt();
        
        $item_attempt->set_learning_path_attempt_id($learning_path_attempt->get_id());
        $item_attempt->set_learning_path_item_id($current_complex_content_object_item->get_id());
        $item_attempt->set_start_time(time());
        $item_attempt->set_total_time(0);
        $item_attempt->set_score(0);
        $item_attempt->set_min_score(0);
        $item_attempt->set_max_score(0);
        $item_attempt->set_status(AbstractItemAttempt::STATUS_NOT_ATTEMPTED);
        $item_attempt->create();
        
        return $item_attempt;
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
        $question_ids = array();
        $attempts = $this->get_assessment_question_attempts();
        
        foreach ($attempts as $attempt)
        {
            $question_ids[] = $attempt->get_question_complex_id();
        }
        
        return $question_ids;
    }

    /*
     * (non-PHPdoc) @see \core\repository\content_object\assessment\AssessmentDisplaySupport::register_question_ids()
     */
    public function register_question_ids($question_ids)
    {
        $current_node = $this->get_current_node();
        
        foreach ($question_ids as $complex_question_id)
        {
            $attempt = new DummyQuestionAttempt();
            $attempt->set_item_attempt_id($current_node->get_current_attempt()->get_id());
            $attempt->set_question_complex_id($complex_question_id);
            $attempt->set_answer('');
            $attempt->set_score(0);
            $attempt->set_feedback('');
            $attempt->set_hint(0);
            
            $attempt->create();
        }
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
