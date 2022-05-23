<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeProgress;

use ArrayIterator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Format\Table\TableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeProgressTableDataProvider extends TableDataProvider
{

    /**
     *
     * @var array
     */
    protected $data;

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return count($this->getAllData());
    }

    /**
     * Retrieves, caches and returns the data
     *
     * @return array
     */
    protected function getAllData()
    {
        if (!isset($this->data))
        {
            /** @var TreeNode $treeNode */
            $treeNode = $this->get_component()->getCurrentTreeNode();

            $this->data = array_values($treeNode->getChildNodes());
        }

        return $this->data;
    }

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \ArrayIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayIterator(array_slice($this->getAllData(), $offset, $count));
    }
}