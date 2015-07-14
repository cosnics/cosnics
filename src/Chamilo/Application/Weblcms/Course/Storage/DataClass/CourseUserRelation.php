<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataClass;

/**
 * This class represents a course user relation in the weblcms.
 * 
 * @package application\weblcms\course;
 * @author Previously Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseUserRelation extends CourseEntityRelation
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_USER_ID = 'user_id';
    
    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_USER = 'user';

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
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the user id of this course user relation object
     * 
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user id of this course user relation object
     * 
     * @param $user_id int
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the user of this course user relation object
     * 
     * @return \core\user\User
     */
    public function get_user()
    {
        return $this->get_foreign_property(
            self :: FOREIGN_PROPERTY_USER, 
            \Chamilo\Core\User\Storage\DataManager :: get_instance());
    }

    /**
     * Sets the user of this course user relation object
     * 
     * @param $user \core\user\User
     */
    public function set_user(\Chamilo\Core\User\Storage\DataClass\User $user)
    {
        $this->set_foreign_property(self :: FOREIGN_PROPERTY_USER, $user);
    }
}
