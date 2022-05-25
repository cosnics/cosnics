<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNode;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * Portfolio item table data provider
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNodeTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        /** @var TreeNode $treeNode */
        $treeNode = $this->get_component()->getCurrentTreeNode();

        return count($treeNode->getChildNodes());
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        /** @var TreeNode $treeNode */
        $treeNode = $this->get_component()->getCurrentTreeNode();

        return new ArrayCollection(array_values($treeNode->getChildNodes()));
    }
}