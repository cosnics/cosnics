<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeProgress;

use ArrayIterator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Format\Table\TableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

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

    public function countData(?Condition $condition = null): int
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return new ArrayIterator(array_slice($this->getAllData(), $offset, $count));
    }
}