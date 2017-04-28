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

        if($canEditLearningPathTreeNode)
        {
            $actions[] = $this->getUpdateNodeAction($learningPathTreeNode);
            $actions[] = $this->getDeleteNodeAction($learningPathTreeNode);
            $actions[] = $this->getMoveNodeAction($learningPathTreeNode);
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
        $url = $this->getUrl(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                Manager::PARAM_CHILD_ID => $learningPathTreeNode->getId()
            )
        );

        return new Action('edit', $title, $url, 'fa fa-pencil');
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
        $url = $this->getUrl(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                Manager::PARAM_CHILD_ID => $learningPathTreeNode->getId()
            )
        );

        return new Action('delete', $title, $url, 'glyphicon glyphicon-remove');
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
        $url = $this->getUrl(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_MOVE,
                Manager::PARAM_CHILD_ID => $learningPathTreeNode->getId()
            )
        );

        return new Action('move', $title, $url, 'glyphicon glyphicon-random');
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
    public function getUrl($parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->baseParameters, $parameters) : $this->baseParameters);

        $redirect = new Redirect($parameters, $filter, $encode_entities);

        return $redirect->getUrl();
    }
}