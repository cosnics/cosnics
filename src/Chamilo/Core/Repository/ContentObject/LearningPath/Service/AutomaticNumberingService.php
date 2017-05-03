<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;

/**
 * Service to determine the automatic numbering of tree nodes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AutomaticNumberingService
{
    /**
     * The cache for the automatic numbering
     *
     * @var string[][]
     */
    protected $automaticNumberingCache = array();

    /**
     * Returns the automatic numbering for the given LearningPathTreeNode
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    public function getAutomaticNumberingForLearningPathTreeNode(LearningPathTreeNode $learningPathTreeNode)
    {
        /** @var LearningPath $learningPath */
        $learningPath = $learningPathTreeNode->getLearningPathTree()->getRoot()->getContentObject();

        if (!$learningPath->usesAutomaticNumbering())
        {
            return null;
        }

        if (!array_key_exists($learningPath->getId(), $this->automaticNumberingCache))
        {
            $this->buildAutomaticNumberingForLearningPathTreeNode(
                $learningPath, $learningPathTreeNode->getLearningPathTree()->getRoot()
            );
        }

        if (!array_key_exists($learningPathTreeNode->getId(), $this->automaticNumberingCache[$learningPath->getId()]))
        {
            throw new \RuntimeException(
                'Could not generate an automatic number for the LearningPathTreeNode with id ' .
                $learningPathTreeNode->getId()
            );
        }

        return $this->automaticNumberingCache[$learningPath->getId()][$learningPathTreeNode->getId()];
    }

    /**
     * Returns the title for the given LearningPathTreeNode with the automatic numbering
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return string
     */
    public function getAutomaticNumberedTitleForLearningPathTreeNode(LearningPathTreeNode $learningPathTreeNode)
    {
        return $this->getAutomaticNumberingForLearningPathTreeNode($learningPathTreeNode) . ' ' .
            $learningPathTreeNode->getContentObject()->get_title();
    }

    /**
     * Builds the automatic numbering for the given learning path and learning path tree node, works recursively
     *
     * @param LearningPath $learningPath
     * @param LearningPathTreeNode $learningPathTreeNode
     * @param int $counter
     * @param string $prefix
     */
    protected function buildAutomaticNumberingForLearningPathTreeNode(
        LearningPath $learningPath, LearningPathTreeNode $learningPathTreeNode, $counter = 1, $prefix = ''
    )
    {
        if(!$learningPathTreeNode->isRootNode())
        {
            $automaticNumber = $prefix ? $prefix . '.' . $counter : $counter;
            $automaticNumberString = $automaticNumber . '.';
        }
        else
        {
            $automaticNumber = null;
            $automaticNumberString = '';
        }

        $this->automaticNumberingCache[$learningPath->getId()][$learningPathTreeNode->getId()] = $automaticNumberString;

        if ($learningPathTreeNode->hasChildNodes())
        {
            $learningPathTreeChildNodes = $learningPathTreeNode->getChildNodes();

            $counter = 1;
            foreach ($learningPathTreeChildNodes as $learningPathTreeChildNode)
            {
                $this->buildAutomaticNumberingForLearningPathTreeNode(
                    $learningPath, $learningPathTreeChildNode, $counter, $automaticNumber
                );

                $counter ++;
            }
        }
    }
}