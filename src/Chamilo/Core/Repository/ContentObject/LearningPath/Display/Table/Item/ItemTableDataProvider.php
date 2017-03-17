<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * Portfolio item table data provider
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        /** @var LearningPathTreeNode $learningPathTreeNode */
        $learningPathTreeNode = $this->get_component()->getCurrentLearningPathTreeNode();
        return new ArrayResultSet(array_values($learningPathTreeNode->getChildNodes()));
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public function count_data($condition)
    {
        /** @var LearningPathTreeNode $learningPathTreeNode */
        $learningPathTreeNode = $this->get_component()->getCurrentLearningPathTreeNode();
        return count($learningPathTreeNode->getChildNodes());
    }
}