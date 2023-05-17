<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use RuntimeException;

/**
 * @package core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    public const ACTION_ACTIVITY = 'Activity';
    public const ACTION_AJAX = 'Ajax';
    public const ACTION_BOOKMARK = 'Bookmarker';
    public const ACTION_BUILD = 'Builder';
    public const ACTION_BUILD_PREREQUISITES = 'PrerequisitesBuilder';
    public const ACTION_COPY_SECTIONS = 'SectionCopier';
    public const ACTION_DELETE_ATTEMPTS_FOR_TREE_NODE = 'DeleteAttemptsForTreeNode';
    public const ACTION_DELETE_TREE_NODE_ATTEMPT = 'DeleteTreeNodeAttempt';
    public const ACTION_DISABLE_STUDENT_VIEW = 'DisableStudentView';
    public const ACTION_EXPORT_REPORTING = 'ReportingExporter';
    public const ACTION_FEEDBACK = 'Feedback';
    public const ACTION_MAIL_USERS_WITH_INCOMPLETE_PROGRESS = 'UserIncompleteProgressMailer';
    public const ACTION_MANAGE = 'Manager';
    public const ACTION_MOVE = 'Mover';
    public const ACTION_MOVE_DIRECTLY = 'DirectMover';
    public const ACTION_REPORTING = 'Reporting';
    public const ACTION_RIGHTS = 'Rights';
    public const ACTION_SHOW_STUDENT_VIEW = 'ShowStudentView';
    public const ACTION_TOGGLE_BLOCKED_STATUS = 'ToggleBlockedStatus';
    public const ACTION_TOGGLE_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'ToggleEnforceDefaultTraversingOrder';
    public const ACTION_TYPE_SPECIFIC = 'TypeSpecific';
    public const ACTION_USER = 'User';
    public const ACTION_VIEW_ASSESSMENT_RESULT = 'AssessmentResultViewer';
    public const ACTION_VIEW_USER_PROGRESS = 'UserProgress';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    public const PARAM_ACTION = 'learning_path_action';
    public const PARAM_CHILD_ID = 'child_id';
    public const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    public const PARAM_DETAILS = 'details';
    public const PARAM_DISPLAY_ORDER = 'display_order';
    public const PARAM_FULL_SCREEN = 'full_screen';
    public const PARAM_ITEM_ATTEMPT_ID = 'item_attempt_id';
    public const PARAM_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    public const PARAM_PARENT_ID = 'parent_id';
    public const PARAM_REPORTING_MODE = 'reporting_mode';
    public const PARAM_REPORTING_USER_ID = 'reporting_user';
    public const PARAM_SHOW_PROGRESS = 'show_progress';
    public const PARAM_SORT = 'sort';
    public const PARAM_STEP = 'step';
    public const PARAM_STUDENT_VIEW_SESSION = 'learning_path_student_view';

    public const SORT_DOWN = 'Down';
    public const SORT_UP = 'Up';

    /**
     * @var int
     */
    protected $current_step;

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        if (!$this->get_application() instanceof LearningPathDisplaySupport)
        {
            throw new RuntimeException(
                'The LearningPath display application should only be run from ' .
                'a parent that implements the LearningPathDisplaySupport'
            );
        }

        $this->learningPath = $this->get_application()->get_root_content_object();
    }

    /**
     * Convenience method to check if the current selected tree node can be edited
     *
     * @return bool
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function canEditCurrentTreeNode()
    {
        return $this->canEditTreeNode($this->getCurrentTreeNode());
    }

    /**
     * Checks if a complex content object path node can be edited
     *
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function canEditTreeNode(TreeNode $treeNode)
    {
        if ($this->inStudentView())
        {
            return false;
        }

        /** @var LearningPathDisplaySupport $application */
        $application = $this->get_application();

        if ($application->is_allowed_to_edit_content_object())
        {
            return true;
        }

        return $treeNode->getContentObject()->get_owner_id() == $this->getUser()->getId();
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_CHILD_ID;
        $additionalParameters[] = self::PARAM_FULL_SCREEN;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(AutomaticNumberingService::class);
    }

    /**
     * Returns the current content object
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    protected function getCurrentContentObject()
    {
        return $this->getCurrentTreeNode()->getContentObject();
    }

    /**
     * Returns the TreeNode for the current step
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    public function getCurrentTreeNode()
    {
        $tree = $this->getTree();

        return $tree->getTreeNodeById($this->getCurrentTreeNodeDataId());
    }

    /**
     * Returns the currently selected learning path child id from the request
     *
     * @return int
     */
    public function getCurrentTreeNodeDataId()
    {
        return (int) $this->getRequest()->getFromRequestOrQuery(self::PARAM_CHILD_ID, 0);
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return LearningPathService | object
     */
    public function getLearningPathService()
    {
        return $this->getService(LearningPathService::class);
    }

    /**
     * Returns the session variable for the student view for the current learning path
     *
     * @return string
     */
    protected function getStudentViewSessionVariable()
    {
        return self::PARAM_STUDENT_VIEW_SESSION . '_' . $this->learningPath->getId();
    }

    /**
     * @return TrackingService
     */
    public function getTrackingService()
    {
        if (!isset($this->trackingService))
        {
            $this->trackingService = $this->get_application()->buildTrackingService();
        }

        return $this->trackingService;
    }

    /**
     * Returns the Tree for the current learning path root
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree
     */
    public function getTree()
    {
        return $this->getLearningPathService()->getTree($this->learningPath);
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return TreeNodeDataService | object
     */
    public function getTreeNodeDataService()
    {
        return $this->getService(TreeNodeDataService::class);
    }

    /**
     * Returns the navigation url for a given TreeNode either to the reporting component
     * or to the viewer component
     *
     * @param TreeNode $treeNode
     *
     * @return string
     */
    public function getTreeNodeNavigationUrl(TreeNode $treeNode)
    {
        $reportingActions = [
            self::ACTION_REPORTING,
            self::ACTION_VIEW_ASSESSMENT_RESULT
        ];

        $action = (in_array($this->get_action(), $reportingActions)) ? self::ACTION_REPORTING :
            self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        return $this->get_url(
            [self::PARAM_ACTION => $action, self::PARAM_CHILD_ID => $treeNode->getId()]
        );
    }

    /**
     * Returns whether or not the user is currently in studentview
     *
     * @return bool
     */
    protected function inStudentView()
    {
        $studentViewSessionVariable = $this->getStudentViewSessionVariable();

        return Session::get($studentViewSessionVariable) == 1;
    }

    /**
     * @return bool
     */
    public function isCurrentTreeNodeDataIdSet()
    {
        $currentTreeNodeDataId = $this->getRequest()->getFromRequestOrQuery(self::PARAM_CHILD_ID);

        return !is_null($currentTreeNodeDataId);
    }

    /**
     * @return bool
     */
    public function is_allowed_to_edit_attempt_data()
    {
        return $this->canEditCurrentTreeNode() &&
            $this->get_application()->is_allowed_to_edit_learning_path_attempt_data();
    }

    /**
     * @return string
     */
    protected function renderRepoDragPanel(): string
    {
        $html = [];

        $javascriptFiles = [
            'Repository/app.js',
            'Repository/service/RepositoryService.js',
            'RepoDragPanel/app.js',
            'RepoDragPanel/filter/limitText.js',
            'RepoDragPanel/directive/ckDraggable.js',
            'RepoDragPanel/controller/DragPanelController.js'
        ];

        foreach ($javascriptFiles as $javascriptFile)
        {
            $html[] = ResourceManager::getInstance()->getResourceHtml(
                $this->getWebPathBuilder()->getResourcesPath(Manager::CONTEXT, true) . 'Javascript/' . $javascriptFile
            );
        }

        $repoDragPanelPath = $this->getSystemPathBuilder()->getResourcesPath(
                "Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Display"
            ) . '/Templates/RepoDragPanel.html';
        $repoDragPanel = file_get_contents($repoDragPanelPath);

        if ($repoDragPanel)
        {
            $html[] = $repoDragPanel;
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Helper function to validate and possibly fix the current step when it became corrupt
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function validateSelectedTreeNodeData()
    {
        try
        {
            $this->getCurrentTreeNode();
        }
        catch (Exception $ex)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('Step'), $this->getCurrentTreeNodeDataId()
            );
        }
    }
}