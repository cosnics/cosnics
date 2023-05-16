<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Traits\EntityTranslationTrait;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a metadata schema
 *
 * @package Chamilo\Core\Metadata\Schema\Storage\DataClass
 * @author  Jens Vanderheyden
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Schema extends DataClass implements EntityTranslationInterface
{
    use EntityTranslationTrait;

    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_FIXED = 'fixed';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_NAMESPACE = 'namespace';
    public const PROPERTY_URL = 'url';

    /**
     * Get the default properties
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_NAMESPACE;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_DESCRIPTION;
        $extendedPropertyNames[] = self::PROPERTY_URL;
        $extendedPropertyNames[] = self::PROPERTY_FIXED;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     */
    protected function getDependencies(array $dependencies = []): array
    {
        $dependencies = [];

        $dependencies[EntityTranslation::class] = new AndCondition(
            [
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class, EntityTranslation::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(static::class)
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class, EntityTranslation::PROPERTY_ENTITY_ID
                    ), new StaticConditionVariable($this->get_id())
                )
            ]
        );

        $sourceConditions = new AndCondition(
            [
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class, RelationInstance::PROPERTY_SOURCE_TYPE
                    ), new StaticConditionVariable(static::class)
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($this->get_id())
                )
            ]
        );

        $targetConditions = new AndCondition(
            [
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class, RelationInstance::PROPERTY_TARGET_TYPE
                    ), new StaticConditionVariable(static::class)
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_ID),
                    new StaticConditionVariable($this->get_id())
                )
            ]
        );

        $dependencies[RelationInstance::class] = new OrCondition([$sourceConditions, $targetConditions]);

        $dependencies[SchemaInstance::class] = new EqualityCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_SCHEMA_ID),
            new StaticConditionVariable($this->get_id())
        );

        $dependencies[Element::class] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_SCHEMA_ID),
            new StaticConditionVariable($this->get_id())
        );

        return $dependencies;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'metadata_schema';
    }

    /**
     * @return string
     */
    public function getTranslationFallback()
    {
        return $this->get_name();
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the namespace
     *
     * @return string
     */
    public function get_namespace()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAMESPACE);
    }

    /**
     * Returns the url
     *
     * @return string
     */
    public function get_url()
    {
        return $this->getDefaultProperty(self::PROPERTY_URL);
    }

    /**
     * Returns whether or not this element is fixed
     *
     * @return string
     */
    public function is_fixed()
    {
        return $this->getDefaultProperty(self::PROPERTY_FIXED);
    }

    /**
     * Sets the description
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets whether or not the element is fixed
     *
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->setDefaultProperty(self::PROPERTY_FIXED, $fixed);
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the namespace
     *
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->setDefaultProperty(self::PROPERTY_NAMESPACE, $namespace);
    }

    /**
     * Sets the url
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->setDefaultProperty(self::PROPERTY_URL, $url);
    }
}