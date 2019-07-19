<?php

namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Extend from this class to define your dataclass as a closure table. Extend from the closure table repository and
 * service to use closure tables in your application.
 *
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ClosureTable extends DataClass
{
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CHILD_ID = 'child_id';
    const PROPERTY_DEPTH = 'depth';

    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_CHILD_ID;
        $extendedPropertyNames[] = self::PROPERTY_DEPTH;

        return $extendedPropertyNames;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->get_default_property(self::PROPERTY_PARENT_ID);
    }

    /**
     * @return int
     */
    public function getChildId()
    {
        return $this->get_default_property(self::PROPERTY_CHILD_ID);
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->get_default_property(self::PROPERTY_DEPTH);
    }

    /**
     * @param int $parentId
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\ClosureTable
     */
    public function setParentId(int $parentId)
    {
        $this->set_default_property(self::PROPERTY_PARENT_ID, $parentId);
        return $this;
    }

    /**
     * @param int $childId
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\ClosureTable
     */
    public function setChildId(int $childId)
    {
        $this->set_default_property(self::PROPERTY_CHILD_ID, $childId);
        return $this;
    }

    /**
     * @param int $depth
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\ClosureTable
     */
    public function setDepth(int $depth)
    {
        $this->set_default_property(self::PROPERTY_DEPTH, $depth);
        return $this;
    }
}