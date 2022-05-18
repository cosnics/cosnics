<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;

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
     * @var string[]
     */
    private array $errors;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    private array $listeners;

    /**
     * @var string[][]
     */
    private array $properties;

    public function __construct(?array $defaultProperties = [], ?array $optionalProperties = [])
    {
        $this->setDefaultProperties($defaultProperties);
        $this->setOptionalProperties($optionalProperties);
        $this->setListeners([]);
    }

    public function __toString(): string
    {
        return Translation::get('ToStringNotImplemented', array('TYPE' => static::class));
    }

    public function addError(string $errorMsg): DataClass
    {
        if (!isset($this->errors))
        {
            $this->errors = [];
        }

        $this->errors[] = $errorMsg;

        return $this;
    }

    public function addListener(DataClassListener $listener): DataClass
    {
        $this->listeners[] = $listener;

        return $this;
    }

    protected function checkBeforeSave(): bool
    {
        /*
         * Example: object with mandatory title if(stringUtilities::is_null_or_empty($this->get_title())) {
         * $this->addError(Translation::get('TitleIsRequired')); }
         */
        return !$this->hasErrors();
    }

    public function clearErrors(): DataClass
    {
        unset($this->errors);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function create(): bool
    {
        $this->notify(DataClassListener::BEFORE_CREATE);
        $success = false;
        if ($this->checkBeforeSave())
        {
            $success = DataManager::create($this);
        }

        $this->notify(DataClassListener::AFTER_CREATE, array($success));

        return $success;
    }

    /**
     * @throws \Exception
     */
    public function delete(): bool
    {
        $this->notify(DataClassListener::BEFORE_DELETE);

        if (!$this->deleteDependencies())
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
     * @throws \Exception
     */
    protected function deleteDependencies(): bool
    {
        foreach ($this->getDependencies() as $dependency_class => $dependency_condition)
        {
            $dependency_objects = DataManager::retrieves(
                $dependency_class, new DataClassRetrievesParameters($dependency_condition)
            );

            foreach ($dependency_objects as $dependency_object)
            {
                if (!$dependency_object->delete())
                {
                    return false;
                }
            }
        }

        return true;
    }

    public static function factory(string $class, array &$record): DataClass
    {
        $object = new $class();
        foreach ($object->getDefaultPropertyNames() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $object->setDefaultProperty($property, $record[$property]);
                unset($record[$property]);
            }
        }

        if (count($record) > 0 && $object instanceof DataClass && !$object instanceof CompositeDataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $object->setOptionalProperty($optional_property_name, $optional_property_value);
            }
        }

        return $object;
    }

    /**
     * @param string[] $cacheablePropertyNames
     *
     * @return string[]
     */
    public static function getCacheablePropertyNames(array $cacheablePropertyNames = []): array
    {
        $cacheablePropertyNames[] = static::PROPERTY_ID;

        return $cacheablePropertyNames;
    }

    /**
     * @return string[]
     */
    public function getDefaultProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getDefaultProperty(string $name)
    {
        return $this->getSpecificProperty(self::PROPERTIES_DEFAULT, $name);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = static::PROPERTY_ID;

        return $extendedPropertyNames;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $dependencies
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     * @throws \Exception
     */
    protected function getDependencies(array $dependencies = []): array
    {
        $this->notify(DataClassListener::GET_DEPENDENCIES, array(&$dependencies));

        return $dependencies;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors ?? [];
    }

    /**
     * @return string[]
     */
    public function getForeignProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_FOREIGN);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getForeignProperty(string $name, string $classname)
    {
        $foreignProperty = $this->getSpecificProperty(self::PROPERTIES_FOREIGN, $name);

        if (is_null($foreignProperty))
        {
            $foreignProperty = DataManager::retrieve_by_id(
                $classname, $this->getDefaultProperty($name . '_id')
            );

            $this->setForeignProperty($name, $foreignProperty);
        }

        return $foreignProperty;
    }

    public function getId(): ?int
    {
        return $this->getDefaultProperty(static::PROPERTY_ID);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[] $listeners
     */
    public function setListeners(array $listeners): DataClass
    {
        $this->listeners = $listeners;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOptionalProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * @return mixed
     */
    public function getOptionalProperty(string $name)
    {
        return $this->getSpecificProperty(self::PROPERTIES_OPTIONAL, $name);
    }

    /**
     * @return string[][]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string[][] $properties
     */
    public function setProperties(array $properties): DataClass
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSpecificProperties(string $propertiesType): array
    {
        return $this->properties[$propertiesType];
    }

    public function getSpecificProperty(string $propertiesType, string $propertyName)
    {
        $properties = $this->getSpecificProperties($propertiesType);

        return (array_key_exists($propertyName, $properties)) ? $properties[$propertyName] : null;
    }

    abstract public static function getTableName(): string;

    /**
     * @deprecated Use getId() now
     */
    public function get_id(): ?int
    {
        return $this->getId();
    }

    public function hasErrors(): bool
    {
        return isset($this->errors) && count($this->errors) > 0;
    }

    public static function isDefaultPropertyName(string $name): bool
    {
        return in_array($name, static::getDefaultPropertyNames());
    }

    public static function isExtended(): bool
    {
        return false;
    }

    public function isIdentified(): bool
    {
        $id = $this->getId();

        return isset($id) && strlen($id) > 0 && $id != self::NO_UID;
    }

    public function notify(string $event, ?array $parameters = []): bool
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
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 2);
    }

    public function removeListener(int $index): DataClass
    {
        unset($this->listeners[$index]);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function save(): bool
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

    public function setDefaultProperties(array $defaultProperties): DataClass
    {
        $this->setSpecificProperties(self::PROPERTIES_DEFAULT, $defaultProperties);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function setDefaultProperty(string $name, $value)
    {
        $this->notify(DataClassListener::BEFORE_SET_PROPERTY, array($name, $value));
        $this->setSpecificProperty(self::PROPERTIES_DEFAULT, $name, $value);
        $this->notify(DataClassListener::AFTER_SET_PROPERTY, array($name, $value));
    }

    /**
     * @param string[] $foreignProperties
     */
    public function setForeignProperties(array $foreignProperties): DataClass
    {
        $this->setSpecificProperties(self::PROPERTIES_FOREIGN, $foreignProperties);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setForeignProperty(string $name, DataClass $value): DataClass
    {
        $this->setSpecificProperty(self::PROPERTIES_FOREIGN, $name, $value);
        $this->setDefaultProperty($name . '_id', $value->getId());

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setId(int $id): DataClass
    {
        if (strlen($id) > 0)
        {
            $this->setDefaultProperty(static::PROPERTY_ID, $id);
        }

        return $this;
    }

    /**
     * @param string[] $optionalProperties
     */
    public function setOptionalProperties(array $optionalProperties): DataClass
    {
        $this->setSpecificProperties(self::PROPERTIES_OPTIONAL, $optionalProperties);

        return $this;
    }

    /**
     * @param mixed $value The new value for the property.
     */
    public function setOptionalProperty(string $name, $value): DataClass
    {
        $this->setSpecificProperty(self::PROPERTIES_OPTIONAL, $name, $value);

        return $this;
    }

    /**
     * @param string[] $properties
     */
    public function setSpecificProperties(string $propertiesType, array $properties): DataClass
    {
        $this->properties[$propertiesType] = $properties;

        return $this;
    }

    /**
     * @param mixed $propertyValue
     */
    public function setSpecificProperty(string $propertiesType, string $propertyName, $propertyValue): DataClass
    {
        $this->properties[$propertiesType][$propertyName] = $propertyValue;

        return $this;
    }

    /**
     * @throws \Exception
     * @deprecated Use setId($id) now
     */
    public function set_id(int $id): DataClass
    {
        return $this->setId($id);
    }

    /**
     * @throws \Exception
     */
    public function update(): bool
    {
        $success = false;

        $this->notify(DataClassListener::BEFORE_UPDATE);

        if ($this->checkBeforeSave())
        {
            $success = DataManager::update($this);
        }

        $this->notify(DataClassListener::AFTER_UPDATE, array($success));

        return $success;
    }
}
