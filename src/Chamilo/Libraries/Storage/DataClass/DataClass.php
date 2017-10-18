<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Exception;

/**
 * Abstract class that describes a dataclass
 * 
 * @package common.libraries
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClass
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Constants
    const PROPERTY_ID = 'id';
    const NO_UID = - 1;

    protected static $tableNames = array();

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * Properties of the data class object, stored in an associative array.
     * Combination of different types of
     * properties. Default properties => properties that are a mapping of dataclass and data table Optional properties
     * => other properties that were added in join queries Foreign properties => objects of other dataclasses
     */
    private $properties;

    /**
     * The listeners for this dataclass
     * 
     * @var DataClassListener[]
     */
    private $listeners;

    /**
     * A list of errors that this dataclass has
     * 
     * @var array
     */
    private $errors;
    
    /**
     * **************************************************************************************************************
     * Property Types
     * **************************************************************************************************************
     */
    const PROPERTIES_DEFAULT = 'default_properties';
    const PROPERTIES_OPTIONAL = 'optional_properties';
    const PROPERTIES_FOREIGN = 'foreign_properties';

    /**
     * **************************************************************************************************************
     * Main functionality
     * **************************************************************************************************************
     */
    
    /**
     * Creates a new data class object.
     * 
     * @param $default_properties array The default properties of the data class object. Associative array.
     * @param $optional_properties array The optional properties of the data class object. Associative array.
     */
    public function __construct($default_properties = array(), $optional_properties = array())
    {
        $this->set_default_properties($default_properties);
        $this->set_optional_properties($optional_properties);
        $this->set_listeners(array());
    }

    /**
     *
     * @param $class string
     * @param $record multitype:string
     * @throws Exception
     * @return \libraries\storage\DataClass
     */
    public static function factory($class, &$record)
    {
        $object = new $class();
        foreach ($object->get_default_property_names() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $object->set_default_property($property, $record[$property]);
                unset($record[$property]);
            }
        }
        
        if (count($record) > 0 && $object instanceof DataClass && ! $object instanceof CompositeDataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $object->set_optional_property($optional_property_name, $optional_property_value);
            }
        }
        return $object;
    }

    /**
     * *************************************************************************************************************
     * Properties functionality
     * *************************************************************************************************************
     */
    
    /**
     * Retrieves all the properties
     * 
     * @return array
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Sets all the properties
     * 
     * @param $properties array
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * *************************************************************************************************************
     * Specific properties functionality
     * *************************************************************************************************************
     */
    
    /**
     * Sets the properties for a specific type
     * 
     * @param $properties_type string
     * @param $properties array
     */
    public function set_specific_properties($properties_type, $properties)
    {
        $this->properties[$properties_type] = $properties;
    }

    /**
     * Returns the properties for a specific type
     * 
     * @param $properties_type string
     *
     * @return array
     */
    public function get_specific_properties($properties_type)
    {
        return $this->properties[$properties_type];
    }

    /**
     * Get a property from a property type
     * 
     * @param $properties_type string
     * @param $property_name string
     *
     * @return string
     */
    public function get_specific_property($properties_type, $property_name)
    {
        $properties = $this->get_specific_properties($properties_type);
        
        return (isset($properties) && array_key_exists($property_name, $properties)) ? $properties[$property_name] : null;
    }

    /**
     * Sets a property for a specific property type
     * 
     * @param $properties_type string
     * @param $property_name string
     * @param $property_value string
     */
    public function set_specific_property($properties_type, $property_name, $property_value)
    {
        $this->properties[$properties_type][$property_name] = $property_value;
    }

    /**
     * *************************************************************************************************************
     * Default properties functionality
     * *************************************************************************************************************
     */
    
    /**
     * Gets a default property of this data class object by name.
     * 
     * @param $name string The name of the property.
     */
    public function get_default_property($name)
    {
        return $this->get_specific_property(self::PROPERTIES_DEFAULT, $name);
    }

    /**
     * Sets a default property of this data class by name.
     * 
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function set_default_property($name, $value)
    {
        $this->notify(DataClassListener::BEFORE_SET_PROPERTY, array($name, $value));
        $this->set_specific_property(self::PROPERTIES_DEFAULT, $name, $value);
        $this->notify(DataClassListener::AFTER_SET_PROPERTY, array($name, $value));
    }

    /**
     * Gets the default properties of this data class.
     * 
     * @return array An associative array containing the properties.
     */
    public function get_default_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_DEFAULT);
    }

    /**
     * Sets the default properties of this dataclass
     * 
     * @param $default_properties array
     */
    public function set_default_properties($default_properties)
    {
        $this->set_specific_properties(self::PROPERTIES_DEFAULT, $default_properties);
    }

    /**
     * Get the default properties of all data classes.
     * 
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_ID;
        return $extended_property_names;
    }

    /**
     * Checks if the given identifier is the name of a default data class property.
     * 
     * @param $name string The identifier.
     * @return boolean True if the identifier is a property name, false otherwise.
     */
    public static function is_default_property_name($name)
    {
        return in_array($name, static::get_default_property_names());
    }

    /**
     * **************************************************************************************************************
     * Optional properties functionality
     * *************************************************************************************************************
     */
    
    /**
     * Gets the optional properties of this data class.
     * 
     * @return array An associative array containing the properties.
     */
    public function get_optional_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * Sets the optional properties of this dataclass
     * 
     * @param $optional_properties array
     */
    public function set_optional_properties($optional_properties)
    {
        $this->set_specific_properties(self::PROPERTIES_OPTIONAL, $optional_properties);
    }

    /**
     * Gets a optional property of this data class object by name.
     * 
     * @param $name string The name of the property.
     */
    public function get_optional_property($name)
    {
        return $this->get_specific_property(self::PROPERTIES_OPTIONAL, $name);
    }

    /**
     * Sets a optional property of this data class by name.
     * 
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function set_optional_property($name, $value)
    {
        $this->set_specific_property(self::PROPERTIES_OPTIONAL, $name, $value);
    }

    /**
     * *************************************************************************************************************
     * Foreign properties functionality
     * *************************************************************************************************************
     */
    
    /**
     * Gets the foreign properties of this data class.
     * 
     * @return array An associative array containing the properties.
     */
    public function get_foreign_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_FOREIGN);
    }

    /**
     * Sets the foreign properties of this dataclass
     * 
     * @param $foreign_properties array
     */
    public function set_foreign_properties($foreign_properties)
    {
        $this->set_specific_properties(self::PROPERTIES_FOREIGN, $foreign_properties);
    }

    /**
     * Gets a foreign property of this data class object by name and retrieves it with lazy loading if the property does
     * not yet exists in this dataclass
     * 
     * @param string $name
     * @param string $classname The type of the foreign object
     * @return mixed
     */
    public function get_foreign_property($name, $classname)
    {
        $foreign_property = $this->get_specific_property(self::PROPERTIES_FOREIGN, $name);
        
        if (is_null($foreign_property))
        {
            $foreign_property = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                $classname, 
                $this->get_default_property($name . '_id'));
            
            $this->set_foreign_property($name, $foreign_property);
        }
        
        return $foreign_property;
    }

    /**
     * Sets a foreign property of this data class by name and pushes the id of this
     * 
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     * @throws \Exception
     */
    public function set_foreign_property($name, $value)
    {
        if (is_null($value) || ! $value instanceof DataClass)
        {
            throw new \Exception(
                Translation::get(
                    'ForeignObjectPropertyCanNotBeNull', 
                    array('OBJECT' => $this->class_name(), 'FOREIGN_OBJECT' => $name)));
        }
        
        $this->set_specific_property(self::PROPERTIES_FOREIGN, $name, $value);
        $this->set_default_property($name . '_id', $value->get_id());
    }

    /**
     * **************************************************************************************************************
     * Properties Metadata Functionality
     * **************************************************************************************************************
     */
    
    /**
     * Returns all (unique) properties by which a DataClass object can be cached
     * 
     * @param string[] $extended_property_names
     *
     * @return string[]
     */
    public static function get_cacheable_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_ID;
        return $extended_property_names;
    }

    /**
     * *************************************************************************************************************
     * Getters and setters
     * *************************************************************************************************************
     */
    
    /**
     * Returns the id of this object
     * 
     * @return int The id.
     * @deprecated Use getId() now
     */
    public function get_id()
    {
        return $this->getId();
    }

    /**
     * Returns the id of this object
     * 
     * @return int The id.
     */
    public function getId()
    {
        return $this->get_default_property(self::PROPERTY_ID);
    }

    /**
     * Sets id of the object
     * 
     * @param $id int
     * @deprecated Use setId($id) now
     */
    public function set_id($id)
    {
        $this->setId($id);
    }

    /**
     * Sets id of the object
     *
     * @param $id int
     *
     * @return $this
     */
    public function setId($id)
    {
        if (isset($id) && strlen($id) > 0)
        {
            $this->set_default_property(self::PROPERTY_ID, $id);
        }

        return $this;
    }

    /**
     * *************************************************************************************************************
     * CRUD functionality
     * *************************************************************************************************************
     */
    
    /**
     * Saves the object
     * 
     * @return boolean
     */
    public function save()
    {
        if ($this->is_identified())
        {
            return $this->update();
        }
        else
        {
            return $this->create();
        }
    }

    /**
     * Check if the data class has an id or not (and therefore exists in the database)
     * 
     * @return boolean
     */
    public function is_identified()
    {
        $id = $this->getId();
        return isset($id) && strlen($id) > 0 && $id != self::NO_UID;
    }

    /**
     * Check wether the object contains all mandatory properties to be saved in datasource This method should be
     * overriden in classes inheriting from DataClass
     * 
     * @return boolean Return true if the object can be saved, false otherwise
     */
    protected function check_before_save()
    {
        /*
         * Example: object with mandatory title if(stringUtilities :: is_null_or_empty($this->get_title())) {
         * $this->add_error(Translation :: get('TitleIsRequired')); }
         */
        return ! $this->has_errors();
    }

    /**
     * Creates the object
     * 
     * @return boolean
     */
    public function create()
    {
        $this->notify(DataClassListener::BEFORE_CREATE);
        $success = false;
        if ($this->check_before_save())
        {
            $success = \Chamilo\Libraries\Storage\DataManager\DataManager::create($this);
        }
        
        $this->notify(DataClassListener::AFTER_CREATE, array($success));
        return $success;
    }

    /**
     * Updates the object
     * 
     * @throws \Exception
     * @return boolean
     */
    public function update()
    {
        $success = false;
        
        $this->notify(DataClassListener::BEFORE_UPDATE);
        
        if ($this->check_before_save())
        {
            $success = \Chamilo\Libraries\Storage\DataManager\DataManager::update($this);
        }
        
        $this->notify(DataClassListener::AFTER_UPDATE, array($success));
        
        return $success;
    }

    /**
     * Deletes the object
     * 
     * @return boolean
     */
    public function delete()
    {
        $this->notify(DataClassListener::BEFORE_DELETE);
        
        $success = true;
        
        if (! $this->delete_dependencies())
        {
            $success = false;
        }
        else
        {
            $success = \Chamilo\Libraries\Storage\DataManager\DataManager::delete($this);
        }
        
        $this->notify(DataClassListener::AFTER_DELETE, array($success));
        
        return $success;
    }

    /**
     * Deletes the dependencies
     */
    protected function delete_dependencies()
    {
        foreach ($this->get_dependencies() as $dependency_class => $dependency_condition)
        {
            $dependency_objects = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
                $dependency_class, 
                new DataClassRetrievesParameters($dependency_condition));
            
            while ($dependency_object = $dependency_objects->next_result())
            {
                if (! $dependency_object->delete())
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @param array $dependencies
     *
     * @return string[string]
     */
    protected function get_dependencies($dependencies = array())
    {
        $this->notify(DataClassListener::GET_DEPENDENCIES, array(&$dependencies));
        
        return $dependencies;
    }

    /**
     * *************************************************************************************************************
     * Listener functionality *
     * *************************************************************************************************************
     */
    
    /**
     * Sets the listeners
     * 
     * @param DataClassListener[] $listeners
     */
    public function set_listeners($listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * Returns the listeners
     * 
     * @return DataClassListener[]
     */
    public function get_listeners()
    {
        return $this->listeners;
    }

    /**
     * Adss a listener to the listeners
     * 
     * @param DataClassListener $listener
     */
    public function add_listener(DataClassListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Removes a listener from the index
     * 
     * @param $index
     */
    public function remove_listener($index)
    {
        unset($this->listeners[$index]);
    }

    /**
     * Triggers an event in all the listeners
     * 
     * @param string $event
     * @param mixed $parameters
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function notify($event, $parameters = array())
    {
        foreach ($this->listeners as $listener)
        {
            if (method_exists($listener, $event))
            {
                if (! call_user_func_array(array($listener, $event), $parameters))
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * *************************************************************************************************************
     * Error handling
     * *************************************************************************************************************
     */
    
    /**
     * Adds an error to the error list
     * 
     * @param $error_msg string
     */
    public function add_error($error_msg)
    {
        if (! isset($this->errors))
        {
            $this->errors = array();
        }
        
        $this->errors[] = $error_msg;
    }

    /**
     * Checks wether the object has errors
     * 
     * @return bool
     */
    public function has_errors()
    {
        return isset($this->errors) && count($this->errors) > 0;
    }

    /**
     * Retrieves the list of errors
     * 
     * @return array
     */
    public function get_errors()
    {
        return isset($this->errors) ? $this->errors : array();
    }

    /**
     * Clears the errors
     */
    public function clear_errors()
    {
        unset($this->errors);
    }

    /**
     * *************************************************************************************************************
     * Helper functionality
     * *************************************************************************************************************
     */
    
    /**
     * Returns the name of the object
     * 
     * @return string
     * @deprecated Only used for legacy calls to CUD method implementations, should no longer be necessary
     */
    public function get_object_name()
    {
        return ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
    }

    /**
     * *************************************************************************************************************
     * Static functionality
     * *************************************************************************************************************
     */
    
    /**
     * Returns whether or not this dataclass is an extended type
     * 
     * @return boolean
     */
    public static function is_extended_type()
    {
        return false;
    }

    /**
     * Returns the table name for this dataclass
     * 
     * @return string
     */
    public static function get_table_name()
    {
        if (! isset(self::$tableNames[static::class_name()]))
        {
            $data_manager = static::package() . '\Storage\DataManager';
            self::$tableNames[static::class_name()] = $data_manager::PREFIX .
                 ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_called_class(), true);
        }
        
        return self::$tableNames[static::class_name()];
    }

    public static function is_extended()
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 2);
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return Translation::get('ToStringNotImplemented', array('TYPE' => static::class_name()));
    }
}
