<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Exception;

/**
 * Abstract class that describes a dataclass
 * 
 * @package Chamilo\Libraries\Storage\DataClass
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClass
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    // Constants
    const PROPERTY_ID = 'id';
    const NO_UID = - 1;

    /**
     *
     * @var string[]
     */
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
     *
     * @var string[]
     */
    private $properties;

    /**
     * The listeners for this dataclass
     * 
     * @var \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    private $listeners;

    /**
     * A list of errors that this dataclass has
     * 
     * @var string[]
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
     * @param string[] $defaultProperties The default properties of the data class object. Associative array.
     * @param string[] $optionalProperties The optional properties of the data class object. Associative array.
     */
    public function __construct($defaultProperties = array(), $optionalProperties = array())
    {
        $this->set_default_properties($defaultProperties);
        $this->set_optional_properties($optionalProperties);
        $this->set_listeners(array());
    }

    /**
     *
     * @param string $class
     * @param string[] $record
     * @throws \Exception
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
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
     * Retrieves all the properties
     * 
     * @return string[][]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Sets all the properties
     * 
     * @param string[][] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Sets the properties for a specific type
     * 
     * @param string $propertiesType
     * @param string[] $properties
     */
    public function set_specific_properties($propertiesType, $properties)
    {
        $this->properties[$propertiesType] = $properties;
    }

    /**
     * Returns the properties for a specific type
     * 
     * @param string $propertiesType
     * @return string[]
     */
    public function get_specific_properties($propertiesType)
    {
        return $this->properties[$propertiesType];
    }

    /**
     * Get a property from a property type
     * 
     * @param string $propertiesType
     * @param string $propertyName
     * @return string
     */
    public function get_specific_property($propertiesType, $propertyName)
    {
        var_dump($propertyName);
        $properties = $this->get_specific_properties($propertiesType);
        
        return (isset($properties) && array_key_exists($propertyName, $properties)) ? $properties[$propertyName] : null;
    }

    /**
     * Sets a property for a specific property type
     * 
     * @param string $propertiesType
     * @param string $propertyName
     * @param string $propertyValue
     */
    public function set_specific_property($propertiesType, $propertyName, $propertyValue)
    {
        $this->properties[$propertiesType][$propertyName] = $propertyValue;
    }

    /**
     * Gets a default property of this data class object by name.
     * 
     * @param string $name The name of the property
     * @return mixed
     */
    public function get_default_property($name)
    {
        return $this->get_specific_property(self::PROPERTIES_DEFAULT, $name);
    }

    /**
     * Sets a default property of this data class by name.
     * 
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
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
     * @return string[] An associative array containing the properties.
     */
    public function get_default_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_DEFAULT);
    }

    /**
     * Sets the default properties of this dataclass
     * 
     * @param string[] $defaultProperties
     */
    public function set_default_properties($defaultProperties)
    {
        $this->set_specific_properties(self::PROPERTIES_DEFAULT, $defaultProperties);
    }

    /**
     * Get the default properties of all data classes.
     * 
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_ID;
        return $extendedPropertyNames;
    }

    /**
     * Checks if the given identifier is the name of a default data class property.
     * 
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false otherwise.
     */
    public static function is_default_property_name($name)
    {
        return in_array($name, static::get_default_property_names());
    }

    /**
     * Gets the optional properties of this data class.
     * 
     * @return string[] An associative array containing the properties.
     */
    public function get_optional_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * Sets the optional properties of this dataclass
     * 
     * @param string[] $optionalProperties
     */
    public function set_optional_properties($optionalProperties)
    {
        $this->set_specific_properties(self::PROPERTIES_OPTIONAL, $optionalProperties);
    }

    /**
     * Gets a optional property of this data class object by name.
     * 
     * @param string $name The name of the property
     * @return mixed
     */
    public function get_optional_property($name)
    {
        return $this->get_specific_property(self::PROPERTIES_OPTIONAL, $name);
    }

    /**
     * Sets a optional property of this data class by name.
     * 
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    public function set_optional_property($name, $value)
    {
        $this->set_specific_property(self::PROPERTIES_OPTIONAL, $name, $value);
    }

    /**
     * Gets the foreign properties of this data class.
     * 
     * @return string[] An associative array containing the properties.
     */
    public function get_foreign_properties()
    {
        return $this->get_specific_properties(self::PROPERTIES_FOREIGN);
    }

    /**
     * Sets the foreign properties of this dataclass
     * 
     * @param string[] $foreignProperties
     */
    public function set_foreign_properties($foreignProperties)
    {
        $this->set_specific_properties(self::PROPERTIES_FOREIGN, $foreignProperties);
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
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
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
     * Returns all (unique) properties by which a DataClass object can be cached
     * 
     * @param string[] $cacheablePropertyNames
     * @return string[]
     */
    public static function get_cacheable_property_names($cacheablePropertyNames = array())
    {
        $cacheablePropertyNames[] = self::PROPERTY_ID;
        return $cacheablePropertyNames;
    }

    /**
     * Returns the id of this object
     * 
     * @return integer The id
     * @deprecated Use getId() now
     */
    public function get_id()
    {
        return $this->getId();
    }

    /**
     * Returns the id of this object
     * 
     * @return integer The id
     */
    public function getId()
    {
        return $this->get_default_property(self::PROPERTY_ID);
    }

    /**
     * Sets id of the object
     * 
     * @param integer $id
     * @deprecated Use setId($id) now
     */
    public function set_id($id)
    {
        $this->setId($id);
    }

    /**
     * Sets id of the object
     *
     * @param integer $id
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
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
     *
     * @return boolean
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $dependencies
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    protected function get_dependencies($dependencies = array())
    {
        $this->notify(DataClassListener::GET_DEPENDENCIES, array(&$dependencies));
        
        return $dependencies;
    }

    /**
     * Sets the listeners
     * 
     * @param \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[] $listeners
     */
    public function set_listeners($listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * Returns the listeners
     * 
     * @return \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    public function get_listeners()
    {
        return $this->listeners;
    }

    /**
     * Adss a listener to the listeners
     * 
     * @param \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener $listener
     */
    public function add_listener(DataClassListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Removes a listener from the index
     * 
     * @param integer $index
     */
    public function remove_listener($index)
    {
        unset($this->listeners[$index]);
    }

    /**
     * Triggers an event in all the listeners
     * 
     * @param string $event
     * @param string[] $parameters
     * @throws \Exception
     * @return boolean
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
     * Adds an error to the error list
     * 
     * @param string $errorMsg
     */
    public function add_error($errorMsg)
    {
        if (! isset($this->errors))
        {
            $this->errors = array();
        }
        
        $this->errors[] = $errorMsg;
    }

    /**
     * Checks wether the object has errors
     * 
     * @return boolean
     */
    public function has_errors()
    {
        return isset($this->errors) && count($this->errors) > 0;
    }

    /**
     * Retrieves the list of errors
     * 
     * @return string[]
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

    /**
     * Returns whether or not this dataclass is an extended type
     *
     * @return boolean
     */
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
