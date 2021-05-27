<?php

namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Generates the actions for a given TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeActionGenerator
    extends \Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGenerator
{

    /**
     * Generates the acions for a given TreeNode
     *
     * @param TreeNode $learningPathTreeNode
     * @param bool $canEditTreeNode
     *
     * @return array|Action[]
     */
    public function generateNodeActions(
        TreeNode $learningPathTreeNode, $canEditTreeNode = false
    ): array
    {
        $actions = [];

        if ($canEditTreeNode)
        {
            $actions[] = $this->getBuildAssessmentAction($learningPathTreeNode);
            $actions[] = $this->getSetMasteryScoreAction($learningPathTreeNode);
            $actions[] = $this->getConfigureAssessmentAction($learningPathTreeNode);
        }

        return $actions;
    }

    /**
     * Returns the action to build the assessment of a given TreeNode
     *
     * @param TreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getBuildAssessmentAction(TreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('BuilderComponent', null, Manager::context());
        $url = $this->getUrlForNode(
            array(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID =>
                    $learningPathTreeNode->getContentObject()->getId(),
                Manager::PARAM_ACTION => Manager::ACTION_BUILDER
            ),
            $learningPathTreeNode->getId()
        );

        return new Action('buildAssessment', $title, $url, 'fa-cubes');
    }

    /**
     * Returns the action to set the mastery score for the assessment for the given TreeNode
     *
     * @param TreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getSetMasteryScoreAction(TreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('SetMasteryScore', null, Manager::context());
        $url = $this->getUrlForNode(
            array(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID =>
                    $learningPathTreeNode->getContentObject()->getId(),
                Manager::PARAM_ACTION => Manager::ACTION_MASTERY
            ),
            $learningPathTreeNode->getId()
        );

        return new Action('setAssessmentMasteryScore', $title, $url, 'fa-signal');
    }

    /**
     * Returns the action to set the feedback options for the assessment for the given TreeNode
     *
     * @param TreeNode $learningPathTreeNode
     *
     * @return Action
     */
    protected function getConfigureAssessmentAction(TreeNode $learningPathTreeNode)
    {
        $title = $this->translator->getTranslation('ConfigureAssessment', null, Manager::context());
        $url = $this->getUrlForNode(
            array(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID =>
                    $learningPathTreeNode->getContentObject()->getId(),
                Manager::PARAM_ACTION => Manager::ACTION_CONFIGURE
            ),
            $learningPathTreeNode->getId()
        );

        return new Action('configureAssessment', $title, $url, 'fa-wrench');
    }
}