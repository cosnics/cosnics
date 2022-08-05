<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface LearningPathScoreServiceInterface
{
    /**
     * @param ContentObjectPublication $publication
     * @param TreeNode $treeNode
     *
     * @return array
     */
    public function getScoresFromTreeNode(ContentObjectPublication $publication, TreeNode $treeNode): array;
}
