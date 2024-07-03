<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\AssignmentServiceBridge;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\EphorusServiceBridge;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\FeedbackServiceBridge;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\NotificationServiceBridge;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiPageTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\WikiTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Blog\Display\BlogDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\LearningPathEmbedderTrait;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\LearningPathDisplaySupport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\WikiDisplaySupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

class ComplexDisplayComponent extends Manager
    implements LearningPathDisplaySupport, AssessmentDisplaySupport, ForumDisplaySupport, GlossaryDisplaySupport,
    BlogDisplaySupport, WikiDisplaySupport, BreadcrumbLessComponentInterface
{
    use LearningPathEmbedderTrait;

    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * @var ContentObjectPublication
     */
    private $publication;

    public function run()
    {
        $contentObjectPublicationTranslation = Translation::getInstance()->getTranslation(
            'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
        );

        $publication_id =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        if (empty($publication_id))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $publication_id
        );

        if (!$this->publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $publication_id);
        }

        $this->buildTrackingService();

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            $this->redirectWithMessage(
                Translation::getInstance()->getTranslation('NotAllowed', null, StringUtilities::LIBRARIES), true, [], [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                ]
            );
        }

        $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForContentObjectPublication(
            $this->getBreadcrumbTrail(), $this, $this->publication
        );

        if ($this->get_root_content_object()->getType() == Assessment::class)
        {
            try
            {
                $this->checkMaximumAssessmentAttempts();
            }
            catch (Exception $ex)
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = '<div class="alert alert-danger">' . $ex->getMessage() . '</div>';
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }

        $this->buildBridgeServices();

        try
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
            )->run();
        }
        catch (TreeNodeNotFoundException $ex)
        {
            throw new UserException(
                $this->getTranslator()->trans(
                    'TreeNodeNotFound', [
                    '{TREE_NODE_ID}' => $ex->getTreeNodeDataId(),
                    '{LEARNING_PATH}' => $this->get_root_content_object()->get_title()
                ], 'Chamilo\Application\Weblcms\Tool\Implementation\LearningPath'
                )
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function buildBridgeServices()
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->publication->getContentObject();

        /** @var AssignmentServiceBridge $assignmentServiceBridge */
        $assignmentServiceBridge = $this->getService(AssignmentServiceBridge::class);

        $assignmentServiceBridge->setCanEditAssignment(
            $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication)
        );

        $assignmentServiceBridge->setContentObjectPublication($this->publication);
        $assignmentServiceBridge->setLearningPathTrackingService($this->trackingService);

        $assignmentServiceBridge->setTargetUserIds(
            $this->getTrackingParameters($this->publication->getId())->getLearningPathTargetUserIds($learningPath)
        );

        $this->getBridgeManager()->addBridge($assignmentServiceBridge);

        /** @var FeedbackServiceBridge $assignmentFeedbackServiceBridge */
        $assignmentFeedbackServiceBridge = $this->getService(FeedbackServiceBridge::class);
        $assignmentFeedbackServiceBridge->setContentObjectPublication($this->publication);
        $this->getBridgeManager()->addBridge($assignmentFeedbackServiceBridge);

        /** @var EphorusServiceBridge $assignmentEphorusServiceBridge */
        $assignmentEphorusServiceBridge = $this->getService(EphorusServiceBridge::class);
        $assignmentEphorusServiceBridge->setEphorusEnabled($this->isEphorusEnabled());
        $assignmentEphorusServiceBridge->setContentObjectPublication($this->publication);
        $this->getBridgeManager()->addBridge($assignmentEphorusServiceBridge);

        /** @var NotificationServiceBridge $assignmentNotificationServiceBridge */
        $assignmentNotificationServiceBridge = $this->getService(NotificationServiceBridge::class);
        $assignmentNotificationServiceBridge->setContentObjectPublication($this->publication);
        $this->getBridgeManager()->addBridge($assignmentNotificationServiceBridge);
    }

    /**
     * Builds the TrackingService
     *
     * @return TrackingService
     */
    public function buildTrackingService()
    {
        if (!isset($this->trackingService))
        {
            $this->trackingService = $this->createTrackingServiceForPublicationAndCourse(
                (int) $this->publication->getId(), (int) $this->get_course_id()
            );
        }

        return $this->trackingService;
    }

    /**
     * Checks the maximum allowed assessment attempts
     */
    protected function checkMaximumAssessmentAttempts()
    {
        if ($this->trackingService->isMaximumAttemptsReachedForAssessment(
            $this->getSelectedLearningPath(), $this->getUser(), $this->getCurrentTreeNode()
        ))
        {
            throw new Exception(
                Translation::getInstance()->getTranslation(
                    'YouHaveReachedYourMaximumAttempts', null,
                    'Chamilo\Application\Weblcms\Tool\Implementation\Assessment'
                )
            );
        }
    }

    public function create_learning_path_item_tracker($learning_path_tracker, $current_complex_content_object_item)
    {
    }

    public function forum_count_topic_views($complex_topic_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumTopicView::class, ForumTopicView::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication()->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumTopicView::class, ForumTopicView::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($complex_topic_id)
        );
        $condition = new AndCondition($conditions);

        return DataManager::count(ForumTopicView::class, new StorageParameters(condition: $condition));
    }

    public function forum_topic_viewed($complex_topic_id)
    {
        $parameters = [];
        $parameters[ForumTopicView::PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[ForumTopicView::PROPERTY_PUBLICATION_ID] = $this->get_publication()->getId();
        $parameters[ForumTopicView::PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;

        Event::trigger('ViewForumTopic', \Chamilo\Application\Weblcms\Manager::CONTEXT, $parameters);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_STEP;
        $additionalParameters[] =
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * Returns the TreeNode for the current step
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode
     */
    public function getCurrentTreeNode()
    {
        return parent::getCurrentTreeNodeForLearningPath($this->getSelectedLearningPath());
    }

    /**
     * Returns the currently selected LearningPath
     *
     * @return LearningPath | ContentObject
     */
    protected function getSelectedLearningPath()
    {
        return $this->publication->getContentObject();
    }

    public function get_assessment_back_url()
    {
    }

    public function get_assessment_configuration()
    {
        return $this->getCurrentTreeNode()->getTreeNodeData()->getAssessmentConfiguration();
    }

    public function get_assessment_continue_url()
    {
    }

    public function get_assessment_current_attempt_id()
    {
        return $this->get_parameter(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID
        );
    }

    public function get_assessment_current_url()
    {
        $parameters = [];
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID] =
            $this->getCurrentTreeNodeDataId();

        return $this->get_url($parameters, [Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID]);
    }

    public function get_assessment_parameters()
    {
        return [
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID,
            \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID
        ];
    }

    public function get_assessment_question_attempt($complex_question_id)
    {
        return $this->retrieve_question_attempts()[$complex_question_id];
    }

    /**
     * Returns the assessment question attempts
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        return $this->retrieve_question_attempts();
    }

    /**
     * @return int
     */
    public function get_embedded_content_object_id()
    {
        return $this->getEmbeddedContentObjectIdentifier();
    }

    public function get_learning_path_content_object_assessment_result_url($complex_content_object_id, $details)
    {
        return $this->get_url(
            [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $this->publication->getId(),
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_SHOW_PROGRESS => 'true',
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_id,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_DETAILS => $details
            ]
        );
    }

    public function get_publication()
    {
        return $this->publication;
    }

    /**
     * Returns the registered question ids
     *
     * @return int[] $question_ids
     */
    public function get_registered_question_ids()
    {
        return array_keys($this->get_assessment_question_attempts());
    }

    public function get_root_content_object()
    {
        if ($this->is_embedded())
        {
            $embedded_content_object_id = $this->getEmbeddedContentObjectIdentifier();
            $this->set_parameter(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);

            return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $embedded_content_object_id
            );
        }
        else
        {
            $this->set_parameter(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID,
                $this->getRequest()->query->get(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID
                )
            );
            $this->set_parameter(
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $this->getRequest()->query->get(
                    \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID
                )
            );

            return $this->getSelectedLearningPath();
        }
    }

    public function get_tree_menu_url()
    {
        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->getRequest()->query->get('course');
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                Manager::CONTEXT
            );
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->publication->getId();
        $parameters[\Chamilo\Core\Repository\Preview\Manager::PARAM_CONTENT_OBJECT_ID] =
            $this->get_root_content_object()->getId();

        $reportingActions = [
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_ASSESSMENT_RESULT,
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
        ];

        $requestedAction = $this->getRequest()->getFromRequestOrQuery(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION
        );

        if (in_array($requestedAction, $reportingActions) || $this->getRequest()->getFromRequestOrQuery(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_REPORTING_MODE
            ))
        {
            $action = $requestedAction ==
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS ?
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS :
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING;

            $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_REPORTING_USER_ID] =
                $this->getRequest()->getFromRequestOrQuery(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_REPORTING_USER_ID
                );
        }
        else
        {
            $action =
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
        }

        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION] = $action;
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID] = '__NODE__';
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN] =
            $this->getRequest()->query->get(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_FULL_SCREEN
            );

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function get_wiki_page_statistics_reporting_template_name()
    {
        return WikiPageTemplate::class;
    }

    public function get_wiki_publication()
    {
        throw new Exception('Unimplemented method : ' . __CLASS__ . ':' . __METHOD__);
    }

    public function get_wiki_statistics_reporting_template_name()
    {
        return WikiTemplate::class;
    }

    /**
     * @return null|string
     */
    public function isEphorusEnabled()
    {
        $ephorusToolRegistration =
            \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_course_tool_by_name('Ephorus');

        if (!$ephorusToolRegistration)
        {
            return false;
        }

        $toolActive = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $ephorusToolRegistration->get_id()
        );

        return $toolActive;
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_content_object(ComplexContentObjectPathNode $node)
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
            ($this->publication->get_allow_collaboration() ||
                $this->publication->getContentObject()->get_owner_id() == $this->getUser()->getId());
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS

    public function is_allowed_to_edit_learning_path_attempt_data()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_view_content_object(ComplexContentObjectPathNode $node)
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    /**
     * @return bool
     */
    public function is_embedded()
    {
        $embedded_content_object_id = $this->getEmbeddedContentObjectIdentifier();

        return isset($embedded_content_object_id);
    }

    /**
     * Returns whether or not the logged in user is a forum manager
     *
     * @param User $user
     *
     * @return bool
     */
    public function is_forum_manager($user)
    {
        return $this->get_course()->is_course_admin($user);
    }

    /**
     * Registers the question ids
     *
     * @param int[] $question_ids
     */
    public function register_question_ids($question_ids)
    {
        $this->question_attempts = $this->trackingService->registerQuestionAttempts(
            $this->getSelectedLearningPath(), $this->getUser(), $this->getCurrentTreeNode(), $question_ids
        );
    }

    /**
     * @param string $pageTitle
     *
     * @return string
     */
    public function render_header(string $pageTitle = ''): string
    {
        if ($this->is_embedded())
        {
            $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

            return Application::render_header($pageTitle);
        }
        else
        {
            return parent::render_header($pageTitle);
        }
    }

    /**
     * @param int $learning_path_item_attempt_id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieve_learning_path_item_attempt($learning_path_item_attempt_id)
    {
        return DataManager::retrieve_by_id(LearningPathTreeNodeAttempt::class, $learning_path_item_attempt_id);
    }

    public function retrieve_learning_path_tracker()
    {
    }

    public function retrieve_learning_path_tracker_items($learning_path_tracker)
    {
    }

    /**
     * Retrieves the question attempts for the selected assessment attempt
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt[]
     */
    protected function retrieve_question_attempts()
    {
        return $this->trackingService->getQuestionAttempts(
            $this->getSelectedLearningPath(), $this->getUser(), $this->getCurrentTreeNode()
        );
    }

    public function save_assessment_answer($complex_question_id, $answer = '', $score = 0, $hint = '')
    {
        $this->trackingService->saveAnswerForQuestion(
            $this->getSelectedLearningPath(), $this->getUser(), $this->getCurrentTreeNode(), $complex_question_id,
            $answer, $score, $hint
        );
    }

    public function save_assessment_result($total_score)
    {
        $this->trackingService->saveAssessmentScore(
            $this->getSelectedLearningPath(), $this->getUser(), $this->getCurrentTreeNode(), $total_score
        );
    }
}
