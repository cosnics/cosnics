<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * Generates the actions for a given LearningPathTreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeActionGenerator
{
    /**
     * @var Translation
     */
    protected $translator;

    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * @var string[]
     */
    protected $urlCache;

    /**
     * NodeActionGenerator constructor.
     *
     * @param Translation $translator
     * @param array $baseParameters
     */
    public function __construct(Translation $translator, array $baseParameters = array())
    {
        $this->translator = $translator;
        $this->baseParameters = $baseParameters;
    }

    /**
     * Generates the acions for a given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
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
            $actions[] = $this->getBlockOrUnblockNodeAction($learningPathTreeNode);
            $actions[] = $this->getNodeReportingAction($learningPathTreeNode);

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

        return $actions;
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
     * Generates a URL for the given parameters and filters, includes the base parameters given in this service
     *
     * @param array $parameters
     * @param array $filter
     * @param bool $encode_entities
     *
     * @return string
     */
    protected function getUrl($parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->baseParameters, $parameters) : $this->baseParameters);

        $redirect = new Redirect($parameters, $filter, $encode_entities);

        return $redirect->getUrl();
    }

    /**
     * Returns a url for a given set of parameters and a given node. Caches the urls for faster access
     *
     * @param array $parameters
     * @param int $learningPathTreeNodeId
     *
     * @return string
     */
    protected function getUrlForNode($parameters = array(), $learningPathTreeNodeId = 0)
    {
        $nodePlaceholder = '__NODE__';

        $cacheKey = md5(serialize($parameters));
        if(!array_key_exists($cacheKey, $this->urlCache))
        {
            $parameters[Manager::PARAM_CHILD_ID] = $nodePlaceholder;
            $this->urlCache[$cacheKey] = $this->getUrl($parameters);
        }

        return str_replace($nodePlaceholder, $learningPathTreeNodeId, $this->urlCache[$cacheKey]);
    }
}