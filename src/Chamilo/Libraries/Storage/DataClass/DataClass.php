<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;

/**
 * Abstract class that describes a dataclass
 *
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClass
{
    public const NO_UID = - 1;

    public const PROPERTIES_DEFAULT = 'default_properties';
    public const PROPERTIES_FOREIGN = 'foreign_properties';
    public const PROPERTIES_OPTIONAL = 'optional_properties';

    public const PROPERTY_ID = 'id';

    /**
     * @var string[]
     */
    private ?array $errors;

    /**
     * @var \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[]
     */
    private array $listeners;

    /**
     * @var string[][]
     */
    private array $properties;

    public function __construct(array $defaultProperties = [], array $optionalProperties = [])
    {
        $this->setDefaultProperties($defaultProperties);
        $this->setOptionalProperties($optionalProperties);
        $this->setListeners([]);
    }

    public function __toString(): string
    {
        return Translation::get('ToStringNotImplemented', ['TYPE' => static::class]);
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
     * @deprecated Should be handled through services
     */
    public function create(): bool
    {
        $this->notify(DataClassListener::BEFORE_CREATE);
        $success = false;
        if ($this->checkBeforeSave())
        {
            $success = DataManager::create($this);
        }

        $this->notify(DataClassListener::AFTER_CREATE, [$success]);

        return $success;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @deprecated Should be handled through services
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

        $this->notify(DataClassListener::AFTER_DELETE, [$success]);

        return $success;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
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

    public function getDefaultProperty(string $name): mixed
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
     */
    protected function getDependencies(array $dependencies = []): array
    {
        $this->notify(DataClassListener::GET_DEPENDENCIES, [&$dependencies]);

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

    public function getId(): ?string
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
     * @return string[]
     */
    public function getOptionalProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * @return ?string
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
     * @return string[]
     */
    public function getSpecificProperties(string $propertiesType): array
    {
        return array_key_exists($propertiesType, $this->properties) ? $this->properties[$propertiesType] : [];
    }

    /**
     * @return ?string
     */
    public function getSpecificProperty(string $propertiesType, string $propertyName)
    {
        $properties = $this->getSpecificProperties($propertiesType);

        return (array_key_exists($propertyName, $properties)) ? $properties[$propertyName] : null;
    }

    abstract public static function getStorageUnitName(): string;

    /**
     * @deprecated Use getId() now
     */
    public function get_id(): ?string
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
                if (!call_user_func_array([$listener, $event], $parameters))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function removeListener(int $index): DataClass
    {
        unset($this->listeners[$index]);

        return $this;
    }

    /**
     * @throws \Exception
     * @deprecated Should be handled through services
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
     */
    public function setDefaultProperty(string $name, $value)
    {
        $this->notify(DataClassListener::BEFORE_SET_PROPERTY, [$name, $value]);
        $this->setSpecificProperty(self::PROPERTIES_DEFAULT, $name, $value);
        $this->notify(DataClassListener::AFTER_SET_PROPERTY, [$name, $value]);
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

    public function setId(?string $id): DataClass
    {
        $this->setDefaultProperty(static::PROPERTY_ID, $id);

        return $this;
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
     * @param string[][] $properties
     */
    public function setProperties(array $properties): DataClass
    {
        $this->properties = $properties;

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
    public function set_id(?string $id): DataClass
    {
        return $this->setId($id);
    }

    /**
     * @throws \Exception
     * @deprecated Should be handled through services
     */
    public function update(): bool
    {
        $success = false;

        $this->notify(DataClassListener::BEFORE_UPDATE);

        if ($this->checkBeforeSave())
        {
            $success = DataManager::update($this);
        }

        $this->notify(DataClassListener::AFTER_UPDATE, [$success]);

        return $success;
    }
}
