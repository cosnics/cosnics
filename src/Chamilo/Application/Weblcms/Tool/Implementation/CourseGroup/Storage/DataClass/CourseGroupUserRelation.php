<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: course_group_user_relation.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.course_group
 */
class CourseGroupUserRelation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_COURSE_GROUP = 'course_group_id';
    const PROPERTY_USER = 'user_id';

    private $defaultProperties;

    /**
     * Creates a new course user relation object.
     * 
     * @param $id int The numeric ID of the course user relation object. May be omitted if creating a new object.
     * @param $defaultProperties array The default properties of the course user relation object. Associative array.
     */
    public function __construct($defaultProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this course user relation object by name.
     * 
     * @param $name string The name of the property.
     */
    public function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this course user relation object.
     * 
     * @return array An associative array containing the properties.
     */
    public function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets a default property of this course user relation object by name.
     * 
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    public function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all course user relations.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return array(self :: PROPERTY_COURSE_GROUP, self :: PROPERTY_USER);
    }

    /**
     * Returns the course group of this course group user relation object
     * 
     * @return int
     */
    public function get_course_group()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_GROUP);
    }

    /**
     * Sets the course group of this course group user relation object
     * 
     * @param $course int
     */
    public function set_course_group($course_group)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_GROUP, $course_group);
    }

    /**
     * Returns the user of this course user relation object
     * 
     * @return int
     */
    public function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this course user relation object
     * 
     * @param $user int
     */
    public function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    /**
     * Gets the user
     * 
     * @return User
     * @todo The functions get_user and set_user should work with a User object and not with the user id's!
     */
    public function get_user_object()
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
            $this->get_user());
    }
}
