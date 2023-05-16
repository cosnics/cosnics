<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a metadata vocabulary
 *
 * @package Chamilo\Core\Metadata\Vocabulary\Storage\DataClass
 * @author  Jens Vanderheyden
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderRegistration extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_PROPERTY_NAME = 'property_name';
    public const PROPERTY_PROVIDER_CLASS = 'provider_class';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     * Get the default properties
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_PROVIDER_CLASS;
        $extendedPropertyNames[] = self::PROPERTY_PROPERTY_NAME;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     */
    protected function getDependencies(array $dependencies = []): array
    {
        $dependencies = [];

        $dependencies[ProviderLink::class] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink::class, ProviderLink::PROPERTY_PROVIDER_REGISTRATION_ID),
            new StaticConditionVariable($this->get_id())
        );

        return $dependencies;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'metadata_provider_registration';
    }

    /**
     * @return string
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return string
     */
    public function get_property_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_PROPERTY_NAME);
    }

    /**
     * @return string
     */
    public function get_provider_class()
    {
        return $this->getDefaultProperty(self::PROPERTY_PROVIDER_CLASS);
    }

    /**
     * @param string $entity_type
     */
    public function set_entity_type($entity_type)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entity_type);
    }

    /**
     * @param string $property_name
     */
    public function set_property_name($property_name)
    {
        $this->setDefaultProperty(self::PROPERTY_PROPERTY_NAME, $property_name);
    }

    /**
     * @param string $provider_class
     */
    public function set_provider_class($provider_class)
    {
        $this->setDefaultProperty(self::PROPERTY_PROVIDER_CLASS, $provider_class);
    }
}