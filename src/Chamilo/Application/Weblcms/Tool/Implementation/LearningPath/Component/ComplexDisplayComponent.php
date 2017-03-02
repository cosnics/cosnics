<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Menu;

class ComplexDisplayComponent extends Manager implements LearningPathDisplaySupport, AssessmentDisplaySupport,
    ForumDisplaySupport, GlossaryDisplaySupport, BlogDisplaySupport, WikiDisplaySupport, DelegateComponent
{

    /**
     *
     * @var ContentObjectPublication
     */
    private $publication;

    /*
     * The question attempts @var QuestionAttempt[]
     */
    private $question_attempts;

    public function run()
    {
        $contentObjectPublicationTranslation =
            Translation::getInstance()->getTranslation('ContentObjectPublication', null, 'Chamilo\Application\Weblcms');

        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        if(empty($publication_id))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $publication_id
        );

        if (!$this->publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $publication_id);
        }

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                )
            );
        }

        if ($this->get_root_content_object()->get_type() == Assessment::class_name())
        {
            try
            {
                $this->checkMaximumAssessmentAttempts();
            }
            catch (\Exception $ex)
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = '<div class="alert alert-danger">' . $ex->getMessage() . '</div>';
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }

        // BreadcrumbTrail :: getInstance()->add(new Breadcrumb(null, $this->get_root_content_object()->get_title()));

        $context = $this->get_root_content_object()->package() . '\Display';
        $factory = new ApplicationFactory(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );

        return $factory->run();
    }

    public function get_root_content_object()
    {
        if ($this->is_embedded())
        {
            $embedded_content_object_id = $this->get_embedded_content_object_id();
            $this->set_parameter(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);

            return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $embedded_content_object_id
            );
        }
        else
        {
            $this->set_parameter(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID,
                Request::get(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID
                )
            );
            $this->set_parameter(
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID)
            );

            return $this->publication->get_content_object();
        }
    }

    /**
     *
     * @return boolean
     */
    function is_embedded()
    {
        $embedded_content_object_id = $this->get_embedded_content_object_id();

        return isset($embedded_content_object_id);
    }

    /**
     *
     * @return int
     */
    function get_embedded_content_object_id()
    {
        return Embedder::get_embedded_content_object_id();
    }

    /**
     *
     * @see \application\weblcms\tool\Manager::render_header()
     */
    public function render_header()
    {
        if ($this->is_embedded())
        {
            Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

            return Application::render_header();
        }
        else
        {
            return parent::render_header();
        }
    }

    public function get_publication()
    {
        return $this->publication;
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_STEP,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN
        );
    }

    public function retrieve_learning_path_tracker()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt::class_name(), LearningPathAttempt::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttempt::class_name(),
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($this->get_publication()->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt::class_name(), LearningPathAttempt::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $condition = new AndCondition($conditions);

        $learning_path_tracker = DataManager::retrieve(
            LearningPathAttempt::class_name(),
            new DataClassRetrieveParameters($condition)
        );

        if (!$learning_path_tracker)
        {
            $learning_path_tracker = new LearningPathAttempt();
            $learning_path_tracker->set_user_id($this->get_user_id());
            $learning_path_tracker->set_course_id($this->get_course_id());
            $learning_path_tracker->set_learning_path_id($this->get_publication()->get_id());
            $learning_path_tracker->set_progress(0);
            $learning_path_tracker->create();

            DataClassCache::truncate(LearningPathAttempt::class_name());
        }

        return $learning_path_tracker;
    }

    public function retrieve_learning_path_tracker_items($learning_path_tracker)
    {
        $learning_path_item_attempt_data = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(),
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learning_path_tracker->get_id())
        );

        $attempts = DataManager::retrieves(
            LearningPathItemAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        $attempt_data = array();

        while ($attempt = $attempts->next_result())
        {
            $attempt_data[$attempt->get_learning_path_item_id()][] = $attempt;
        }

        return $attempt_data;
    }

    public function get_learning_path_tree_menu_url()
    {
        $parameters = array();

        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $parameters[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = Request::get('course');
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                $this->package()
            );
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication->get_id();
        $parameters[\Chamilo\Core\Repository\Preview\Manager::PARAM_CONTENT_OBJECT_ID] =
            $this->get_root_content_object()->get_id();
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_STEP] =
            Menu::NODE_PLACEHOLDER;
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN] =
            $this->getRequest()->query->get(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN
            );

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     * Creates a learning path item tracker
     *
     * @param $learning_path_tracker LearningPathAttempt
     * @param $current_complex_content_object_item ComplexContentObjectItem
     */
    public function create_learning_path_item_tracker($learning_path_tracker, $current_complex_content_object_item)
    {
        $item_attempt = new LearningPathItemAttempt();
        $item_attempt->set_learning_path_attempt_id($learning_path_tracker->get_id());
        $item_attempt->set_learning_path_item_id($current_complex_content_object_item->get_id());
        $item_attempt->set_start_time(time());
        $item_attempt->set_total_time(0);
        $item_attempt->set_score(0);
        $item_attempt->set_min_score(0);
        $item_attempt->set_max_score(0);
        $item_attempt->set_status(LearningPathItemAttempt::STATUS_NOT_ATTEMPTED);
        $item_attempt->create();

        return $item_attempt;
    }

    /**
     * Get the url of the assessment result
     *
     * @param $complex_content_object_id int
     * @param $details unknown_type
     */
    public function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication->get_id(),
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_SHOW_PROGRESS => 'true',
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_DETAILS => $details
            )
        );
    }

    public function save_assessment_answer($complex_question_id, $answer, $score, $hint)
    {
        $question_attempt = $this->get_assessment_question_attempt($complex_question_id);

        $question_attempt->set_answer($answer);
        $question_attempt->set_score($score);
        $question_attempt->set_hint($hint);

        $question_attempt->update();
    }

    public function save_assessment_result($total_score)
    {
        $currentAttempt = $this->get_current_node()->get_current_attempt();

        if (!$currentAttempt)
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('NoAttemptsFound', null, Manager::context())
            );
        }

        $currentAttempt->set_score($total_score);
        $currentAttempt->set_total_time(
            $currentAttempt->get_total_time() + (time() - $currentAttempt->get_start_time())
        );

        $complex_content_object_item = $this->get_current_node()->get_complex_content_object_item();
        $learning_path_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $complex_content_object_item->get_ref()
        );
        $mastery_score = $learning_path_item->get_mastery_score();

        if ($mastery_score)
        {
            $status = ($total_score >= $mastery_score) ? AbstractItemAttempt::STATUS_PASSED :
                AbstractItemAttempt::STATUS_FAILED;
        }
        else
        {
            $status = AbstractItemAttempt::STATUS_COMPLETED;
        }

        $currentAttempt->set_status($status);
        if ($currentAttempt->update())
        {
            $this->get_current_node()->recalculateIsCompleted();
            $learningPathTracker = $this->retrieve_learning_path_tracker();
            $learningPathTracker->set_progress($this->getComplexContentObjectPath()->get_progress());
            $learningPathTracker->update();
        }
    }

    public function get_assessment_current_attempt_id()
    {
        return $this->get_parameter(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID
        );
    }

    /**
     *
     * @return \core\repository\content_object\learning_path\ComplexContentObjectPathNode
     */
    private function get_current_node()
    {
        $path = $this->getComplexContentObjectPath();

        return $path->get_node(
            Request::get(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_STEP,
                $path->get_root()->get_id()
            )
        );
    }

    /**
     * @return ComplexContentObjectPath
     */
    protected function getComplexContentObjectPath()
    {
        $root_content_object = $this->publication->get_content_object();
        $learning_path_item_attempt_data = $this->retrieve_learning_path_tracker_items(
            $this->retrieve_learning_path_tracker()
        );

        return $root_content_object->get_complex_content_object_path($learning_path_item_attempt_data);
    }

    public function get_assessment_configuration()
    {
        $complex_content_object_item = $this->get_current_node()->get_complex_content_object_item();
        $learning_path_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $complex_content_object_item->get_ref()
        );

        return $learning_path_item->get_configuration();
    }

    public function get_assessment_parameters()
    {
        return array(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID,
            \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_STEP
        );
    }

    /**
     * Returns the assessment question attempts
     *
     * @return QuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        if (is_null($this->question_attempts))
        {
            $this->question_attempts = $this->retrieve_question_attempts();
        }

        return $this->question_attempts;
    }

    /**
     * Retrieves the question attempts for the selected assessment attempt
     *
     * @return QuestionAttempt[]
     */
    protected function retrieve_question_attempts()
    {
        $question_attempts = array();

        $currentAttempt = $this->get_current_node()->get_current_attempt();

        if (!$currentAttempt)
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('NoAttemptsFound', null, Manager::context())
            );
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathQuestionAttempt::class_name(),
                LearningPathQuestionAttempt::PROPERTY_ITEM_ATTEMPT_ID
            ),
            new StaticConditionVariable($currentAttempt->get_id())
        );

        $question_attempts_result_set = DataManager::retrieves(
            LearningPathQuestionAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        while ($question_attempt = $question_attempts_result_set->next_result())
        {
            $question_attempts[$question_attempt->get_question_complex_id()] = $question_attempt;
        }

        return $question_attempts;
    }

    /**
     * Registers the question ids
     *
     * @param int[] $question_ids
     */
    public function register_question_ids($question_ids)
    {
        $currentAttempt = $this->get_current_node()->get_current_attempt();
        if (!$currentAttempt)
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('NoAttemptsFound', null, Manager::context())
            );
        }

        foreach ($question_ids as $complex_question_id)
        {
            $attempt = new LearningPathQuestionAttempt();
            $attempt->set_item_attempt_id($currentAttempt->get_id());
            $attempt->set_question_complex_id($complex_question_id);
            $attempt->set_answer('');
            $attempt->set_score(0);
            $attempt->set_feedback('');
            $attempt->set_hint(0);

            $attempt->create();

            $this->question_attempts[$complex_question_id] = $attempt;
        }
    }

    /**
     * Returns the registered question ids
     *
     * @return int[] $question_ids
     */
    public function get_registered_question_ids()
    {
        $question_ids = array();

        $question_attempts = $this->get_assessment_question_attempts();

        foreach ($question_attempts as $question_attempt)
        {
            $question_ids[] = $question_attempt->get_question_complex_id();
        }

        return $question_ids;
    }

    public function get_assessment_question_attempt($complex_question_id)
    {
        return $this->question_attempts[$complex_question_id];
    }

    public function forum_topic_viewed($complex_topic_id)
    {
        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::PROPERTY_USER_ID] =
            $this->get_user_id();
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::PROPERTY_PUBLICATION_ID] =
            $this->get_publication()->get_id();
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::PROPERTY_FORUM_TOPIC_ID] =
            $complex_topic_id;

        Event::trigger('ViewForumTopic', \Chamilo\Application\Weblcms\Manager::context(), $parameters);
    }

    public function forum_count_topic_views($complex_topic_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::PROPERTY_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->get_publication()->get_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::PROPERTY_FORUM_TOPIC_ID
            ),
            new StaticConditionVariable($complex_topic_id)
        );
        $condition = new AndCondition($conditions);

        return DataManager::count(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView::class_name(
            ),
            new DataClassCountParameters($condition)
        );
    }

    /**
     * Returns whether or not the logged in user is a forum manager
     *
     * @return boolean
     */
    public function is_forum_manager($user)
    {
        return $this->get_course()->is_course_admin($user);
    }

    public function get_wiki_page_statistics_reporting_template_name()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiPageTemplate::class_name();
    }

    public function get_wiki_statistics_reporting_template_name()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiTemplate::class_name();
    }

    public function get_wiki_publication()
    {
        throw new \Exception("Unimplemented method : " . __CLASS__ . ':' . __METHOD__);
    }

    public function get_assessment_continue_url()
    {
    }

    public function get_assessment_back_url()
    {
    }

    public function get_assessment_current_url()
    {
        return $this->get_url(array(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID => null));
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
        $this->publication->get_allow_collaboration();
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::is_allowed_to_edit_learning_path_attempt_data()
     */
    public function is_allowed_to_edit_learning_path_attempt_data()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    /**
     *
     * @see \core\repository\content_object\learning_path\display\LearningPathDisplaySupport::retrieve_learning_path_item_attempt()
     */
    public function retrieve_learning_path_item_attempt($learning_path_item_attempt_id)
    {
        return DataManager::retrieve_by_id(LearningPathItemAttempt::class_name(), $learning_path_item_attempt_id);
    }

    /**
     * Checks the maximum allowed assessment attempts
     */
    protected function checkMaximumAssessmentAttempts()
    {
        $attemptsCount = count($this->get_current_node()->get_data());

        if ($this->get_root_content_object()->get_maximum_attempts() != 0 &&
            $attemptsCount > $this->get_root_content_object()->get_maximum_attempts()
        )
        {
            throw new \Exception(
                Translation::get(
                    'YouHaveReachedYourMaximumAttempts',
                    null,
                    'Chamilo\Application\Weblcms\Tool\Implementation\Assessment'
                )
            );
        }
    }
}
