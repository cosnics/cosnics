<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;

/**
 * This class represents a course group relation in the weblcms.
 * 
 * @package application\weblcms\course;
 * @author Previously Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseGroupRelation extends CourseEntityRelation
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_GROUP_ID = 'group_id';
    
    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_GROUP = 'group';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the default properties of this dataclass
     * 
     * @return String[] - The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_GROUP_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the group id of this course group relation object
     * 
     * @return int
     */
    public function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group id of this course group relation object
     * 
     * @param $group_id int
     */
    public function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the group of this course group relation object
     * 
     * @return \group\Group
     */
    public function get_group()
    {
        return $this->get_foreign_property(self :: FOREIGN_PROPERTY_GROUP, DataManager :: get_instance());
    }

    /**
     * Sets the group of this course group relation object
     * 
     * @param $group \group\Group
     */
    public function set_group(Group $group)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_GROUP, $group);
    }
}
