<?php
namespace Chamilo\Libraries\Storage\DataClass;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
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
    use DependencyInjectionContainerTrait;

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

    public function addError(string $errorMsg): static
    {
        if (!isset($this->errors))
        {
            $this->errors = [];
        }

        $this->errors[] = $errorMsg;

        return $this;
    }

    public function addListener(DataClassListener $listener): static
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

    public function clearErrors(): static
    {
        unset($this->errors);

        return $this;
    }

    /**
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

    public function getForeignProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_FOREIGN);
    }

    public function getForeignProperty(string $name, string $classname): mixed
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

    public function getOptionalProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * @return ?string
     */
    public function getOptionalProperty(string $name): mixed
    {
        return $this->getSpecificProperty(self::PROPERTIES_OPTIONAL, $name);
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getSpecificProperties(string $propertiesType): array
    {
        return array_key_exists($propertiesType, $this->properties) ? $this->properties[$propertiesType] : [];
    }

    public function getSpecificProperty(string $propertiesType, string $propertyName): mixed
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

    public function removeListener(int $index): static
    {
        unset($this->listeners[$index]);

        return $this;
    }

    /**
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

    public function setDefaultProperties(array $defaultProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_DEFAULT, $defaultProperties);

        return $this;
    }

    public function setDefaultProperty(string $name, mixed $value): static
    {
        $this->notify(DataClassListener::BEFORE_SET_PROPERTY, [$name, $value]);
        $this->setSpecificProperty(self::PROPERTIES_DEFAULT, $name, $value);
        $this->notify(DataClassListener::AFTER_SET_PROPERTY, [$name, $value]);

        return $this;
    }

    /**
     * @param string[] $foreignProperties
     */
    public function setForeignProperties(array $foreignProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_FOREIGN, $foreignProperties);

        return $this;
    }

    public function setForeignProperty(string $name, DataClass $value): static
    {
        $this->setSpecificProperty(self::PROPERTIES_FOREIGN, $name, $value);
        $this->setDefaultProperty($name . '_id', $value->getId());

        return $this;
    }

    public function setId(?string $id): static
    {
        $this->setDefaultProperty(static::PROPERTY_ID, $id);

        return $this;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Listeners\DataClassListener[] $listeners
     */
    public function setListeners(array $listeners): static
    {
        $this->listeners = $listeners;

        return $this;
    }

    /**
     * @param string[] $optionalProperties
     */
    public function setOptionalProperties(array $optionalProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_OPTIONAL, $optionalProperties);

        return $this;
    }

    public function setOptionalProperty(string $name, mixed $value): static
    {
        $this->setSpecificProperty(self::PROPERTIES_OPTIONAL, $name, $value);

        return $this;
    }

    /**
     * @param string[][] $properties
     */
    public function setProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @param string[] $properties
     */
    public function setSpecificProperties(string $propertiesType, array $properties): static
    {
        $this->properties[$propertiesType] = $properties;

        return $this;
    }

    public function setSpecificProperty(string $propertiesType, string $propertyName, mixed $propertyValue): static
    {
        $this->properties[$propertiesType][$propertyName] = $propertyValue;

        return $this;
    }

    /**
     * @deprecated Use setId($id) now
     */
    public function set_id(?string $id): static
    {
        return $this->setId($id);
    }

    /**
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
