<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use RuntimeException;

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
    protected $automaticNumberingCache = [];

    /**
     * Returns the automatic numbering for the given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return string
     */
    public function getAutomaticNumberingForTreeNode(TreeNode $treeNode)
    {
        /** @var LearningPath $learningPath */
        $learningPath = $treeNode->getTree()->getRoot()->getContentObject();

        if (!$learningPath->usesAutomaticNumbering())
        {
            return null;
        }

        if (!array_key_exists($learningPath->getId(), $this->automaticNumberingCache))
        {
            $this->buildAutomaticNumberingForTreeNode(
                $learningPath, $treeNode->getTree()->getRoot()
            );
        }

        if (!array_key_exists($treeNode->getId(), $this->automaticNumberingCache[$learningPath->getId()]))
        {
            throw new RuntimeException(
                'Could not generate an automatic number for the TreeNode with id ' .
                $treeNode->getId()
            );
        }

        return $this->automaticNumberingCache[$learningPath->getId()][$treeNode->getId()];
    }

    /**
     * Returns the title for the given TreeNode with the automatic numbering
     *
     * @param TreeNode $treeNode
     *
     * @return string
     */
    public function getAutomaticNumberedTitleForTreeNode(TreeNode $treeNode)
    {
        return $this->getAutomaticNumberingForTreeNode($treeNode) . ' ' .
            $treeNode->getContentObject()->get_title();
    }

    /**
     * Builds the automatic numbering for the given learning path and learning path tree node, works recursively
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param int $counter
     * @param string $prefix
     */
    protected function buildAutomaticNumberingForTreeNode(
        LearningPath $learningPath, TreeNode $treeNode, $counter = 1, $prefix = ''
    )
    {
        if(!$treeNode->isRootNode())
        {
            $automaticNumber = $prefix ? $prefix . '.' . $counter : $counter;
            $automaticNumberString = $automaticNumber . '.';
        }
        else
        {
            $automaticNumber = null;
            $automaticNumberString = '';
        }

        $this->automaticNumberingCache[$learningPath->getId()][$treeNode->getId()] = $automaticNumberString;

        if ($treeNode->hasChildNodes())
        {
            $treeChildNodes = $treeNode->getChildNodes();

            $counter = 1;
            foreach ($treeChildNodes as $treeChildNode)
            {
                $this->buildAutomaticNumberingForTreeNode(
                    $learningPath, $treeChildNode, $counter, $automaticNumber
                );

                $counter ++;
            }
        }
    }
}