<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNode;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * Portfolio item table data provider
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNodeTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        /** @var TreeNode $treeNode */
        $treeNode = $this->get_component()->getCurrentTreeNode();

        return count($treeNode->getChildNodes());
    }

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode>
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        /** @var TreeNode $treeNode */
        $treeNode = $this->get_component()->getCurrentTreeNode();

        return new DataClassIterator(TreeNode::class, array_values($treeNode->getChildNodes()));
    }
}