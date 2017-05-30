<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * Generates the actions for a given LearningPathTreeNode
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
     * Generates the acions for a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param bool $canEditLearningPathTreeNode
     *
     * @return array|Action[]
     */
    public function generateNodeActions(
        LearningPathTreeNode $learningPathTreeNode, $canEditLearningPathTreeNode = false
    ): array
    {
        $actions = array();

        if ($canEditLearningPathTreeNode)
        {
            $actions[] = $this->getUpdateNodeAction($learningPathTreeNode);
            $actions[] = $this->getNodeReportingAction($learningPathTreeNode);

            /** @var LearningPath $learningPath */
            $learningPath = $learningPathTreeNode->getLearningPathTree()->getRoot()->getContentObject();

            if (!$learningPath->enforcesDefaultTraversingOrder() && !$learningPathTreeNode->isRootNode())
            {
                $actions[] = $this->getBlockOrUnblockNodeAction($learningPathTreeNode);
            }

            if (!$learningPathTreeNode->isRootNode())
            {
                $actions[] = $this->getDeleteNodeAction($learningPathTreeNode);
                $actions[] = $this->getMoveNodeAction($learningPathTreeNode);
            }
        }

        $actions[] = $this->getMyProgressNodeAction($learningPathTreeNode);
        $actions[] = $this->getNodeActivityAction($learningPathTreeNode);

        if ($learningPathTreeNode->hasChildNodes())
        {
            $actions[] = $this->getManageNodesAction($learningPathTreeNode);
        }

        $actions[] = $this->getViewNodeAction($learningPathTreeNode);

        $nodeSpecificActions = $this->getNodeSpecificActions($learningPathTreeNode, $canEditLearningPathTreeNode);

        if (is_array($nodeSpecificActions) && !empty($nodeSpecificActions))
        {
            $actions = array_merge($actions, $nodeSpecificActions);
        }

        return $actions;
    }

    /**
     * Returns the action to view a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getViewNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('ReturnToLearningPath', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT),
            $learningPathTreeNode->getId()
        );

        return new Action('view', $title, $url, 'fa-file');
    }

    /**
     * Returns the action to update a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getUpdateNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('UpdaterComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM),
            $learningPathTreeNode->getId()
        );

        return new Action('edit', $title, $url, 'fa-pencil');
    }

    /**
     * Returns the action to delete a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getDeleteNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('DeleterComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
            $learningPathTreeNode->getId()
        );

        return new Action(
            'delete', $title, $url, 'fa-times', $this->translator->getTranslation('Confirm', null, 'Chamilo\Libraries')
        );
    }

    /**
     * Returns the action to move a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getMoveNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('Move', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_MOVE),
            $learningPathTreeNode->getId()
        );

        return new Action('move', $title, $url, 'fa-random');
    }

    /**
     * Returns the action to view the activity of a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getNodeActivityAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('ActivityComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_ACTIVITY),
            $learningPathTreeNode->getId()
        );

        return new Action('activity', $title, $url, 'fa-mouse-pointer');
    }

    /**
     * Returns the action to view the activity of a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getManageNodesAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('ManagerComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_MANAGE),
            $learningPathTreeNode->getId()
        );

        return new Action('manage', $title, $url, 'fa-bars');
    }

    /**
     * Returns the action to block or unblock a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getBlockOrUnblockNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $translationVariable = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'MarkAsOptional' : 'MarkAsRequired';

        $icon = ($learningPathTreeNode->getLearningPathChild() &&
            $learningPathTreeNode->getLearningPathChild()->isBlocked()) ?
            'unlock' : 'ban';

        $title = $this->translator->getTranslation($translationVariable, null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_TOGGLE_BLOCKED_STATUS),
            $learningPathTreeNode->getId()
        );

        return new Action('block', $title, $url, 'fa-' . $icon);
    }

    /**
     * Returns the action to view the progress for a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getMyProgressNodeAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('MyProgress', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_REPORTING),
            $learningPathTreeNode->getId()
        );

        return new Action('progress', $title, $url, 'fa-pie-chart');
    }

    /**
     * Returns the action to view the reporting for a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getNodeReportingAction(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('Reporting', null, Manager::context());
        $url = $this->getUrlForNode(
            array(Manager::PARAM_ACTION => Manager::ACTION_VIEW_USER_PROGRESS),
            $learningPathTreeNode->getId()
        );

        return new Action('reporting', $title, $url, 'fa-bar-chart');
    }

    /**
     * Generates the node specific actions for the given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param bool $canEditLearningPathTreeNode
     *
     * @return array|Action[]
     */
    protected function getNodeSpecificActions(
        LearningPathTreeNode $learningPathTreeNode, $canEditLearningPathTreeNode = false
    )
    {
        $contentObjectType = $learningPathTreeNode->getContentObject()->get_type();
        if (array_key_exists($contentObjectType, $this->contentObjectTypeNodeActionGenerators))
        {
            return $this->contentObjectTypeNodeActionGenerators[$contentObjectType]->generateNodeActions(
                $learningPathTreeNode, $canEditLearningPathTreeNode
            );
        }

        return array();
    }
}