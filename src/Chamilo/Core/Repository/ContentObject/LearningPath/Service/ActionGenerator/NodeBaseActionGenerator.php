<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * Generates the actions for a given TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeBaseActionGenerator extends NodeActionGenerator
{
    /**
     * Node action generators for specific content object types
     *
     * @var NodeActionGenerator[]
     */
    protected $contentObjectTypeNodeActionGenerators;

    /**
     * NodeActionGenerator constructor.
     *
     * @param Translation $translator
     * @param array $baseParameters
     * @param NodeActionGenerator[] $contentObjectTypeNodeActionGenerators
     */
    public function __construct(
        Translation $translator, array $baseParameters = array(), $contentObjectTypeNodeActionGenerators = array()
    )
    {
        parent::__construct($translator, $baseParameters);

        $this->contentObjectTypeNodeActionGenerators = $contentObjectTypeNodeActionGenerators;
    }

    /**
     * Generates the acions for a given TreeNode
     *
     * @param TreeNode $treeNode
     * @param bool $canEditTreeNode
     *
     * @return array|Action[]
     */
    public function generateNodeActions(
        TreeNode $treeNode, $canEditTreeNode = false
    ): array
    {
        $actions = array();

        if ($canEditTreeNode)
        {
            $actions[] = $this->getUpdateNodeAction($treeNode);
            $actions[] = $this->getNodeReportingAction($treeNode);

            /** @var LearningPath $learningPath */
            $learningPath = $treeNode->getTree()->getRoot()->getContentObject();

            if (!$learningPath->enforcesDefaultTraversingOrder() && !$treeNode->isRootNode())
            {
                $actions[] = $this->getBlockOrUnblockNodeAction($treeNode);
            }

            if (!$treeNode->isRootNode())
            {
                $actions[] = $this->getDeleteNodeAction($treeNode);
                $actions[] = $this->getMoveNodeAction($treeNode);
            }
        }

        $actions[] = $this->getMyProgressNodeAction($treeNode);
        $actions[] = $this->getNodeActivityAction($treeNode);

        if ($treeNode->hasChildNodes())
        {
            $actions[] = $this->getManageNodesAction($treeNode);
        }

        $actions[] = $this->getViewNodeAction($treeNode);

        $nodeSpecificActions = $this->getNodeSpecificActions($treeNode, $canEditTreeNode);

        if (is_array($nodeSpecificActions) && !empty($nodeSpecificActions))
        {
            $actions = array_merge($actions, $nodeSpecificActions);
        }

        return $actions;
    }

    /**
     * Returns the action to view a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getViewNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ReturnToLearningPath', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT),
            $treeNode->getId()
        );

        return new Action('view', $title, $url, 'fa-file');
    }

    /**
     * Returns the action to update a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getUpdateNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('UpdaterComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM),
            $treeNode->getId()
        );

        return new Action('edit', $title, $url, 'fa-pencil');
    }

    /**
     * Returns the action to delete a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getDeleteNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('DeleterComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
            $treeNode->getId()
        );

        return new Action(
            'delete', $title, $url, 'fa-times', $this->translator->getTranslation('Confirm', null, 'Chamilo\Libraries')
        );
    }

    /**
     * Returns the action to move a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getMoveNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('Move', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_MOVE),
            $treeNode->getId()
        );

        return new Action('move', $title, $url, 'fa-random');
    }

    /**
     * Returns the action to view the activity of a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getNodeActivityAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ActivityComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_ACTIVITY),
            $treeNode->getId()
        );

        return new Action('activity', $title, $url, 'fa-mouse-pointer');
    }

    /**
     * Returns the action to view the activity of a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getManageNodesAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ManagerComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_MANAGE),
            $treeNode->getId()
        );

        return new Action('manage', $title, $url, 'fa-bars');
    }

    /**
     * Returns the action to block or unblock a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getBlockOrUnblockNodeAction(TreeNode $treeNode)
    {
        $translationVariable = ($treeNode->getLearningPathChild() &&
            $treeNode->getLearningPathChild()->isBlocked()) ?
            'MarkAsOptional' : 'MarkAsRequired';

        $icon = ($treeNode->getLearningPathChild() &&
            $treeNode->getLearningPathChild()->isBlocked()) ?
            'unlock' : 'ban';

        $title = $this->translator->getTranslation($translationVariable, null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_TOGGLE_BLOCKED_STATUS),
            $treeNode->getId()
        );

        return new Action('block', $title, $url, 'fa-' . $icon);
    }

    /**
     * Returns the action to view the progress for a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getMyProgressNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('MyProgress', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_REPORTING),
            $treeNode->getId()
        );

        return new Action('progress', $title, $url, 'fa-pie-chart');
    }

    /**
     * Returns the action to view the reporting for a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getNodeReportingAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('Reporting', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_VIEW_USER_PROGRESS),
            $treeNode->getId()
        );

        return new Action('reporting', $title, $url, 'fa-bar-chart');
    }

    /**
     * Generates the node specific actions for the given TreeNode
     *
     * @param TreeNode $treeNode
     * @param bool $canEditTreeNode
     *
     * @return array|Action[]
     */
    protected function getNodeSpecificActions(
        TreeNode $treeNode, $canEditTreeNode = false
    )
    {
        $contentObjectType = $treeNode->getContentObject()->get_type();
        if (array_key_exists($contentObjectType, $this->contentObjectTypeNodeActionGenerators))
        {
            return $this->contentObjectTypeNodeActionGenerators[$contentObjectType]->generateNodeActions(
                $treeNode, $canEditTreeNode
            );
        }

        return array();
    }
}