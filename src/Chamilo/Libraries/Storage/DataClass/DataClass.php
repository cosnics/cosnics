<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;
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
    use ClassContext;

    const NO_UID = - 1;

    const PROPERTIES_DEFAULT = 'default_properties';
    const PROPERTIES_FOREIGN = 'foreign_properties';
    const PROPERTIES_OPTIONAL = 'optional_properties';

    const PROPERTY_ID = 'id';

    /**
     *
     * @var string[]
     */
    protected static $tableNames = [];

    /**
     * Properties of the data class object, stored in an associative array.
     * Combination of different types of
     * properties. Default properties => properties that are a mapping of dataclass and data table Optional properties
     * => other properties that were added in join queries Foreign properties => objects of other dataclasses
     *
     * @var string[][]
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
     * Creates a new data class object.
     *
     * @param string[] $defaultProperties The default properties of the data class object. Associative array.
     * @param string[] $optionalProperties The optional properties of the data class object. Associative array.
     */
    public function __construct($defaultProperties = [], $optionalProperties = [])
    {
        $this->set_default_properties($defaultProperties);
        $this->set_optional_properties($optionalProperties);
        $this->set_listeners([]);
    }

    /**
     *
     * @return string
     */

    public function __toString()
    {
        return Translation::get('ToStringNotImplemented', array('TYPE' => static::class));
    }

    /**
     * Adds an error to the error list
     *
     * @param string $errorMsg
     */
    public function add_error($errorMsg)
    {
        if (!isset($this->errors))
        {
            $this->errors = [];
        }

        $this->errors[] = $errorMsg;
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
     * Check wether the object contains all mandatory properties to be saved in datasource This method should be
     * overriden in classes inheriting from DataClass
     *
     * @return boolean Return true if the object can be saved, false otherwise
     */
    protected function check_before_save()
    {
        /*
         * Example: object with mandatory title if(stringUtilities::is_null_or_empty($this->get_title())) {
         * $this->add_error(Translation::get('TitleIsRequired')); }
         */
        return !$this->has_errors();
    }

    /**
     * Clears the errors
     */
    public function clear_errors()
    {
        unset($this->errors);
    }

    /**
     * Creates the object
     *
     * @return boolean
     * @throws \Exception
     */
    public function create()
    {
        $this->notify(DataClassListener::BEFORE_CREATE);
        $success = false;
        if ($this->check_before_save())
        {
            $success = DataManager::create($this);
        }

        $this->notify(DataClassListener::AFTER_CREATE, array($success));

        return $success;
    }

    /**
     * Deletes the object
     *
     * @return boolean
     * @throws \Exception
     */
    public function delete()
    {
        $this->notify(DataClassListener::BEFORE_DELETE);

        $success = true;

        if (!$this->delete_dependencies())
        {
            $success = false;
        }
        else
        {
            $success = DataManager::delete($this);
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
            $dependency_objects = DataManager::retrieves(
                $dependency_class, new DataClassRetrievesParameters($dependency_condition)
            );

            foreach($dependency_objects as $dependency_object)
            {
                if (!$dependency_object->delete())
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     * @param string $class
     * @param string[] $record
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
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

        if (count($record) > 0 && $object instanceof DataClass && !$object instanceof CompositeDataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $object->set_optional_property($optional_property_name, $optional_property_value);
            }
        }

        return $object;
    }

    /**
     * Gets the default properties of this data class.
     *
     * @return string[] An associative array containing the properties.
     */
    public function getDefaultProperties()
    {
        return $this->get_specific_properties(self::PROPERTIES_DEFAULT);
    }

    /**
     * Gets a default property of this data class object by name.
     *
     * @param string $name The name of the property
     *
     * @return mixed
     */
    public function getDefaultProperty($name)
    {
        return $this->get_specific_property(self::PROPERTIES_DEFAULT, $name);
    }

    /**
     * Returns the id of this object
     *
     * @return integer The id
     */
    public function getId()
    {
        return $this->getDefaultProperty(static::PROPERTY_ID);
    }

    /**
     * Returns all (unique) properties by which a DataClass object can be cached
     *
     * @param string[] $cacheablePropertyNames
     *
     * @return string[]
     */
    public static function get_cacheable_property_names($cacheablePropertyNames = [])
    {
        $cacheablePropertyNames[] = static::PROPERTY_ID;

        return $cacheablePropertyNames;
    }

    /**
     * Gets the default properties of this data class.
     *
     * @return string[] An associative array containing the properties.
     * @deprecated Use DataClass::getDefaultProperties() now
     */
    public function get_default_properties()
    {
        return $this->getDefaultProperties();
    }

    /**
     * Gets a default property of this data class object by name.
     *
     * @param string $name The name of the property
     *
     * @return mixed
     * @deprecated Use DataClass::getDefaultProperty() now
     */
    public function get_default_property($name)
    {
        return $this->getDefaultProperty($name);
    }

    /**
     * Get the default properties of all data classes.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = [])
    {
        $extendedPropertyNames[] = static::PROPERTY_ID;

        return $extendedPropertyNames;
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $dependencies
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     * @throws \Exception
     */
    protected function get_dependencies($dependencies = [])
    {
        $this->notify(DataClassListener::GET_DEPENDENCIES, array(&$dependencies));

        return $dependencies;
    }

    /**
     * Retrieves the list of errors
     *
     * @return string[]
     */
    public function get_errors()
    {
        return isset($this->errors) ? $this->errors : [];
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
     * Gets a foreign property of this data class object by name and retrieves it with lazy loading if the property does
     * not yet exists in this dataclass
     *
     * @param string $name
     * @param string $classname The type of the foreign object
     *
     * @return mixed
     * @throws \Exception
     */
    public function get_foreign_property($name, $classname)
    {
        $foreign_property = $this->get_specific_property(self::PROPERTIES_FOREIGN, $name);

        if (is_null($foreign_property))
        {
            $foreign_property = DataManager::retrieve_by_id(
                $classname, $this->get_default_property($name . '_id')
            );

            $this->set_foreign_property($name, $foreign_property);
        }

        return $foreign_property;
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
     * Returns the listeners
     *
     * @return \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    public function get_listeners()
    {
        return $this->listeners;
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
     * Returns the name of the object
     *
     * @return string
     * @throws \ReflectionException
     * @deprecated Only used for legacy calls to CUD method implementations, should no longer be necessary
     */
    public function get_object_name()
    {
        return ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
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
     * Gets a optional property of this data class object by name.
     *
     * @param string $name The name of the property
     *
     * @return mixed
     */
    public function get_optional_property($name)
    {
        return $this->get_specific_property(self::PROPERTIES_OPTIONAL, $name);
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
     * Returns the properties for a specific type
     *
     * @param string $propertiesType
     *
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
     *
     * @return string
     */
    public function get_specific_property($propertiesType, $propertyName)
    {
        $properties = $this->get_specific_properties($propertiesType);

        return (isset($properties) && array_key_exists($propertyName, $properties)) ? $properties[$propertyName] : null;
    }

    /**
     * Returns the table name for this dataclass
     *
     * @return string
     */
    abstract public static function get_table_name();

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
     * Check if the data class has an id or not (and therefore exists in the database)
     *
     * @return boolean
     */
    public function isIdentified()
    {
        $id = $this->getId();

        return isset($id) && strlen($id) > 0 && $id != self::NO_UID;
    }

    /**
     * Checks if the given identifier is the name of a default data class property.
     *
     * @param string $name The identifier.
     *
     * @return boolean True if the identifier is a property name, false otherwise.
     */
    public static function is_default_property_name($name)
    {
        return in_array($name, static::get_default_property_names());
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
     * @return boolean
     * @deprecated
     */
    public function is_identified()
    {
        return $this->isIdentified();
    }

    /**
     * Triggers an event in all the listeners
     *
     * @param string $event
     * @param string[] $parameters
     *
     * @return boolean
     * @throws \Exception
     */
    public function notify($event, $parameters = [])
    {
        foreach ($this->listeners as $listener)
        {
            if (method_exists($listener, $event))
            {
                if (!call_user_func_array(array($listener, $event), $parameters))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 2);
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
     * Saves the object
     *
     * @return boolean
     * @throws \Exception
     */
    public function save()
    {
        if ($this->isIdentified())
        {
            return $this->update();
        }
        else
        {
            return $this->create();
        }
    }

    /**
     * Sets a default property of this data class by name.
     *
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     *
     * @throws \Exception
     */
    public function setDefaultProperty($name, $value)
    {
        $this->notify(DataClassListener::BEFORE_SET_PROPERTY, array($name, $value));
        $this->set_specific_property(self::PROPERTIES_DEFAULT, $name, $value);
        $this->notify(DataClassListener::AFTER_SET_PROPERTY, array($name, $value));
    }

    /**
     * Sets id of the object
     *
     * @param integer $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Exception
     */
    public function setId($id)
    {
        if (isset($id) && strlen($id) > 0)
        {
            $this->setDefaultProperty(static::PROPERTY_ID, $id);
        }

        return $this;
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
     * Sets a default property of this data class by name.
     *
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     *
     * @throws \Exception
     * @deprecated Use DataClass::setDefaultProperty() now
     */
    public function set_default_property($name, $value)
    {
        $this->setDefaultProperty($name, $value);
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
     * Sets a foreign property of this data class by name and pushes the id of this
     *
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     *
     * @throws \Exception
     */
    public function set_foreign_property($name, $value)
    {
        if (is_null($value) || !$value instanceof DataClass)
        {
            throw new Exception(
                Translation::get(
                    'ForeignObjectPropertyCanNotBeNull', array('OBJECT' => static::class, 'FOREIGN_OBJECT' => $name)
                )
            );
        }

        $this->set_specific_property(self::PROPERTIES_FOREIGN, $name, $value);
        $this->setDefaultProperty($name . '_id', $value->getId());
    }

    /**
     * Sets id of the object
     *
     * @param integer $id
     *
     * @throws \Exception
     * @deprecated Use setId($id) now
     */
    public function set_id($id)
    {
        $this->setId($id);
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
     * Updates the object
     *
     * @return boolean
     * @throws \Exception
     */
    public function update()
    {
        $success = false;

        $this->notify(DataClassListener::BEFORE_UPDATE);

        if ($this->check_before_save())
        {
            $success = DataManager::update($this);
        }

        $this->notify(DataClassListener::AFTER_UPDATE, array($success));

        return $success;
    }
}
