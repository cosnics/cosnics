<?php
namespace Chamilo\Libraries\Storage\DataClass\Listeners;

/**
 * DataClassListener which can be extended to listen to the crud functionality of a dataclass
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClassListener
{
    /**
     * **************************************************************************************************************
     * Event Constants *
     * **************************************************************************************************************
     */
    const BEFORE_CREATE = 'on_before_create';
    const BEFORE_UPDATE = 'on_before_update';
    const BEFORE_DELETE = 'on_before_delete';
    const AFTER_CREATE = 'on_after_create';
    const AFTER_UPDATE = 'on_after_update';
    const AFTER_DELETE = 'on_after_delete';
    const BEFORE_SET_PROPERTY = 'on_before_set_property';
    const AFTER_SET_PROPERTY = 'on_after_set_property';
    const GET_DEPENDENCIES = 'on_get_dependencies';

    /**
     * *************************************************************************************************************
     * Dummy functionality *
     * *************************************************************************************************************
     */
    
    /**
     * Calls this function before the creation of a dataclass in the database
     */
    public function on_before_create()
    {
        return true;
    }

    /**
     * Calls this function after the creation of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_create($success)
    {
        return true;
    }

    /**
     * Calls this function before the update of a dataclass in the database
     */
    public function on_before_update()
    {
        return true;
    }

    /**
     * Calls this function after the update of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_update($success)
    {
        return true;
    }

    /**
     * Calls this function before the deletion of a dataclass in the database
     * 
     * @return bool
     */
    public function on_before_delete()
    {
        return true;
    }

    /**
     * Calls this function after the deletion of a dataclass in the database
     * 
     * @param bool $success
     *
     * @return bool
     */
    public function on_after_delete($success)
    {
        return true;
    }

    /**
     * Calls this function before a property is set
     * 
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function on_before_set_property($name, $value)
    {
        return true;
    }

    /**
     * Calls this function after a property is set
     * 
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function on_after_set_property($name, $value)
    {
        return true;
    }

    /**
     * Calls this function to return the dependencies of this class
     * 
     * @param array $dependencies
     *
     * @return bool
     */
    public function on_get_dependencies(&$dependencies = array())
    {
        return true;
    }
}
