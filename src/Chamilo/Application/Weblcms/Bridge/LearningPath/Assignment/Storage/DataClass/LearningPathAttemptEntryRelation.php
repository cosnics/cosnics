<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathAttemptEntryRelation extends DataClass
{
    const PROPERTY_TREE_NODE_ATTEMPT_ID = 'tree_node_attempt_id';
    const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_TREE_NODE_ATTEMPT_ID;
        $extendedPropertyNames[] = self::PROPERTY_ENTRY_ID;

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
     * @return integer
     */
    public function getEntryId()
    {
        return $this->get_default_property(self::PROPERTY_ENTRY_ID);
    }

    /**
     *
     * @param integer $entryId
     */
    public function setEntryId($entryId)
    {
        $this->set_default_property(self::PROPERTY_ENTRY_ID, $entryId);
    }

    public static function get_table_name()
    {
        return 'tracking_weblcms_lp_attempt_rel_assignment_entry';
    }
}