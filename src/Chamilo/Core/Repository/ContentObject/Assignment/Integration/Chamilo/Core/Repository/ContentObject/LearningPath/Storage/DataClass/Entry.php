<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Entry extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
{
    const PROPERTY_TREE_NODE_ATTEMPT_ID = 'tree_node_attempt_id';
    const PROPERTY_TREE_NODE_DATA_ID = 'tree_node_data_id';

    // Entity types
    const ENTITY_TYPE_USER = 0;

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_TREE_NODE_ATTEMPT_ID;
        $extendedPropertyNames[] = self::PROPERTY_TREE_NODE_DATA_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function getTreeNodeAttemptId()
    {
        return $this->get_default_property(self::PROPERTY_TREE_NODE_ATTEMPT_ID);
    }

    /**
     *
     * @param int $treeNodeAttemptId
     */
    public function setTreeNodeAttemptId($treeNodeAttemptId)
    {
        $this->set_default_property(self::PROPERTY_TREE_NODE_ATTEMPT_ID, $treeNodeAttemptId);
    }

    /**
     *
     * @return int
     */
    public function getTreeNodeDataId()
    {
        return $this->get_default_property(self::PROPERTY_TREE_NODE_DATA_ID);
    }

    /**
     *
     * @param int $treeNodeAttemptId
     */
    public function setTreeNodeDataId($treeNodeAttemptId)
    {
        $this->set_default_property(self::PROPERTY_TREE_NODE_DATA_ID, $treeNodeAttemptId);
    }
}