<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
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
    const ACTION_DELETE_TREE_NODE_ATTEMPT = 'DeleteTreeNodeAttempt';
    const ACTION_DELETE_ATTEMPTS_FOR_TREE_NODE = 'DeleteAttemptsForTreeNode';
    const ACTION_MOVE_DIRECTLY = 'DirectMover';
    const ACTION_TOGGLE_BLOCKED_STATUS = 'ToggleBlockedStatus';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'AssessmentResultViewer';
    const ACTION_VIEW_USER_PROGRESS = 'UserProgress';
    const ACTION_AJAX = 'Ajax';
    const ACTION_MAIL_USERS_WITH_INCOMPLETE_PROGRESS = 'UserIncompleteProgressMailer';
    const ACTION_COPY_SECTIONS = 'SectionCopier';
    const ACTION_SHOW_STUDENT_VIEW = 'ShowStudentView';
    const ACTION_DISABLE_STUDENT_VIEW = 'DisableStudentView';
    const ACTION_EXPORT_REPORTING = 'ReportingExporter';
    const ACTION_TOGGLE_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'ToggleEnforceDefaultTraversingOrder';

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
    const PARAM_STUDENT_VIEW_SESSION = 'learning_path_student_view';

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
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * @var LearningPath
     */
    protected $learningPath;

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

        $this->learningPath = $this->get_application()->get_root_content_object();
    }

    /**
     * Returns the currently selected learning path child id from the request
     *
     * @return int
     */
    public function getCurrentTreeNodeDataId()
    {
        return (int) $this->getRequest()->get(self::PARAM_CHILD_ID, 0);
    }

    /**
     *
     * @return boolean
     */
    public function isCurrentTreeNodeDataIdSet()
    {
        $currentTreeNodeDataId = $this->getRequest()->get(self::PARAM_CHILD_ID);

        return !is_null($currentTreeNodeDataId);
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_attempt_data()
    {
        return $this->canEditCurrentTreeNode() &&
            $this->get_application()->is_allowed_to_edit_learning_path_attempt_data();
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
    protected function validateSelectedTreeNodeData()
    {
        try
        {
            $this->getCurrentTreeNode();
        }
        catch (\Exception $ex)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('Step'), $this->getCurrentTreeNodeDataId()
            );
        }
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return TreeNodeDataService | object
     */
    public function getTreeNodeDataService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.tree_node_data_service'
        );
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return LearningPathService | object
     */
    public function getLearningPathService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_service'
        );
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
     * Returns the TreeNode for the current step
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode
     */
    public function getCurrentTreeNode()
    {
        $tree = $this->getTree();

        return $tree->getTreeNodeById($this->getCurrentTreeNodeDataId());
    }

    /**
     * Returns the current content object
     *
     * @return ContentObject
     */
    protected function getCurrentContentObject()
    {
        return $this->getCurrentTreeNode()->getContentObject();
    }

    /**
     * Convenience method to check if the current selected tree node can be edited
     *
     * @return bool
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
        if($this->inStudentView())
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
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.automatic_numbering_service'
        );
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
        $reportingActions = array(
            self::ACTION_REPORTING, self::ACTION_VIEW_ASSESSMENT_RESULT
        );

        $action = (in_array($this->get_action(), $reportingActions)) ? self::ACTION_REPORTING :
            self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        return $this->get_url(
            array(self::PARAM_ACTION => $action, self::PARAM_CHILD_ID => $treeNode->getId())
        );
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
     * @return string
     */
    protected function renderRepoDragPanel(): string
    {
        $html = array();

        $javascriptFiles = array(
            'Repository/app.js', 'Repository/service/RepositoryService.js',
            'RepoDragPanel/app.js', 'RepoDragPanel/filter/limitText.js',
            'RepoDragPanel/directive/ckDraggable.js', 'RepoDragPanel/controller/DragPanelController.js'
        );

        foreach ($javascriptFiles as $javascriptFile)
        {
            $html[] = ResourceManager::getInstance()->get_resource_html(
                $this->getPathBuilder()->getResourcesPath(Manager::context(), true) . 'Javascript/' . $javascriptFile
            );
        }

        $repoDragPanelPath = $this->getPathBuilder()->getResourcesPath(
                "Chamilo\\Core\\Repository\\ContentObject\\LearningPath\\Display"
            ) . '/Templates/RepoDragPanel.html';
        $repoDragPanel = file_get_contents($repoDragPanelPath);

        if ($repoDragPanel)
        {
            $html[] = $repoDragPanel;
        }

        return implode(PHP_EOL, $html);
    }
}