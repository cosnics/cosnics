<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_ACTION = 'learning_path_action';

    // Actions
    const ACTION_FEEDBACK = 'Feedback';
    const ACTION_BOOKMARK = 'Bookmarker';
    const ACTION_ACTIVITY = 'Activity';
    const ACTION_RIGHTS = 'Rights';
    const ACTION_MOVE = 'Mover';
    const ACTION_MANAGE = 'Manager';
    const ACTION_USER = 'User';
    const ACTION_BUILD_PREREQUISITES = 'PrerequisitesBuilder';
    const ACTION_TYPE_SPECIFIC = 'TypeSpecific';
    const ACTION_BUILD = 'Builder';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_DELETE_ATTEMPT = 'DeleteAttempt';
    const ACTION_MOVE_DIRECTLY = 'DirectMover';
    const ACTION_TOGGLE_BLOCKED_STATUS = 'ToggleBlockedStatus';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'AssessmentResultViewer';
    const ACTION_VIEW_USER_PROGRESS = 'UserProgress';
    const ACTION_AJAX = 'Ajax';
    const ACTION_MAIL_USERS_WITH_INCOMPLETE_PROGRESS = 'UserIncompleteProgressMailer';

    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_SHOW_PROGRESS = 'show_progress';
    const PARAM_DETAILS = 'details';
    const PARAM_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    const PARAM_SORT = 'sort';
    const PARAM_ITEM_ATTEMPT_ID = 'item_attempt_id';
    const PARAM_FULL_SCREEN = 'full_screen';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_DISPLAY_ORDER = 'display_order';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_CHILD_ID = 'child_id';
    const PARAM_REPORTING_USER_ID = 'reporting_user';
    const PARAM_REPORTING_MODE = 'reporting_mode';

    // Sorting
    const SORT_UP = 'Up';
    const SORT_DOWN = 'Down';

    // Default action
    const DEFAULT_ACTION = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    /**
     *
     * @var int
     */
    protected $current_step;

    /**
     * @var LearningPathTree
     */
    protected $learningPathTree;

    /**
     * @var LearningPathTrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        if(!$this->get_application() instanceof LearningPathDisplaySupport)
        {
            throw new \RuntimeException(
                'The LearningPath display application should only be run from ' .
                'a parent that implements the LearningPathDisplaySupport'
            );
        }
    }

    /**
     * Returns the currently selected learning path child id from the request
     *
     * @return int
     */
    public function getCurrentLearningPathChildId()
    {
        return (int) $this->getRequest()->get(self::PARAM_CHILD_ID, 0);
    }

    /**
     *
     * @return boolean
     */
    public function isCurrentLearningPathChildIdSet()
    {
        $currentLearningPathChildId = $this->getRequest()->get(self::PARAM_CHILD_ID);

        return !is_null($currentLearningPathChildId);
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_attempt_data()
    {
        return $this->get_application()->is_allowed_to_edit_learning_path_attempt_data();
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID, self::PARAM_FULL_SCREEN);
    }

    /**
     * Helper function to validate and possibly fix the current step when it became corrupt
     */
    protected function validateSelectedLearningPathChild()
    {
        try
        {
            $this->getCurrentLearningPathTreeNode();
        }
        catch (\Exception $ex)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('Step'), $this->getCurrentLearningPathChildId()
            );
        }
    }

    /**
     * Returns the LearningPathChildService
     *
     * @return LearningPathChildService | object
     */
    public function getLearningPathChildService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_child_service'
        );
    }

    /**
     * Returns the LearningPathTreeBuilder service
     *
     * @return LearningPathTreeBuilder | object
     */
    public function getLearningPathTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_tree_builder'
        );
    }

    /**
     * Returns the LearningPathTree for the current learning path root
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree
     */
    public function getLearningPathTree()
    {
        if (!isset($this->learningPathTree))
        {
            $this->learningPathTree = $this->getLearningPathTreeBuilder()->buildLearningPathTree(
                $this->get_root_content_object()
            );
        }

        return $this->learningPathTree;
    }

    /**
     * Returns the LearningPathTreeNode for the current step
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode
     */
    public function getCurrentLearningPathTreeNode()
    {
        $learningPathTree = $this->getLearningPathTree();

        return $learningPathTree->getLearningPathTreeNodeById($this->getCurrentLearningPathChildId());
    }

    /**
     * Returns the current content object
     *
     * @return ContentObject
     */
    protected function getCurrentContentObject()
    {
        return $this->getCurrentLearningPathTreeNode()->getContentObject();
    }

    /**
     * Checks if a complex content object path node can be editted
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return bool
     */
    public function canEditLearningPathTreeNode(LearningPathTreeNode $learningPathTreeNode)
    {
        /** @var LearningPathDisplaySupport $application */
        $application = $this->get_application();

        if ($application->is_allowed_to_edit_content_object())
        {
            return true;
        }

        return $learningPathTreeNode->getContentObject()->get_owner_id() == $this->getUser()->getId();
    }

    /**
     * @return LearningPathTrackingService
     */
    public function getLearningPathTrackingService()
    {
        if (!isset($this->learningPathTrackingService))
        {
            $this->learningPathTrackingService = $this->get_application()->buildLearningPathTrackingService();
        }

        return $this->learningPathTrackingService;
    }

    /**
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.automatic_numbering_service'
        );
    }

    /**
     * Returns the navigation url for a given LearningPathTreeNode either to the reporting component
     * or to the viewer component
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    public function getLearningPathTreeNodeNavigationUrl(LearningPathTreeNode $learningPathTreeNode)
    {
        $reportingActions = array(
            self::ACTION_REPORTING, self::ACTION_VIEW_ASSESSMENT_RESULT
        );

        $action = (in_array($this->get_action(), $reportingActions)) ? self::ACTION_REPORTING :
            self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        return $this->get_url(
            array(self::PARAM_ACTION => $action, self::PARAM_CHILD_ID => $learningPathTreeNode->getId())
        );
    }
}