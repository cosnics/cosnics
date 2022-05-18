<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes an element in a metadata schema
 *
 * @author Jens Vanderheyden - VUB Brussel
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Element extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_DISPLAY_NAME = 'display_name';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_FIXED = 'fixed';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SCHEMA_ID = 'schema_id';
    const PROPERTY_VALUE_LIMIT = 'value_limit';
    const PROPERTY_VALUE_TYPE = 'value_type';

    const VALUE_TYPE_FREE = 1;
    const VALUE_TYPE_VOCABULARY_BOTH = 4;
    const VALUE_TYPE_VOCABULARY_PREDEFINED = 2;
    const VALUE_TYPE_VOCABULARY_USER = 3;

    /**
     *
     * @var boolean
     */
    private $namespace = false;

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    private $schema;

    /**
     * Constructor
     *
     * @param array $default_properties
     * @param array $optional_properties
     */
    public function __construct($default_properties = [], $optional_properties = [])
    {
        parent::__construct($default_properties, $optional_properties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    public function getGlyph()
    {
        switch ($this->get_value_type())
        {
            case self::VALUE_TYPE_FREE :
                return new FontAwesomeGlyph('file', [], null, 'fas');
                break;
            case self::VALUE_TYPE_VOCABULARY_PREDEFINED :
                return new FontAwesomeGlyph('globe', [], null, 'fas');
                break;
            case self::VALUE_TYPE_VOCABULARY_USER :
                return new FontAwesomeGlyph('user', [], null, 'fas');
                break;
            case self::VALUE_TYPE_VOCABULARY_BOTH :
                return new FontAwesomeGlyph('book', [], null, 'fas');
                break;
        }
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    public function getSchema()
    {
        if (!isset($this->schema))
        {
            $this->schema = DataManager::retrieve_by_id(Schema::class, $this->get_schema_id());
        }

        return $this->schema;
    }

    /**
     * Get the default properties
     *
     * @param array $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_SCHEMA_ID;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_DISPLAY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_FIXED;
        $extendedPropertyNames[] = self::PROPERTY_DISPLAY_ORDER;
        $extendedPropertyNames[] = self::PROPERTY_VALUE_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_VALUE_LIMIT;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    protected function getDependencies($dependencies = [])
    {
        $dependencies = [];

        $dependencies[EntityTranslation::class] = new AndCondition(
            array(
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
            )
        );

        $sourceConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class, RelationInstance::PROPERTY_SOURCE_TYPE
                    ), new StaticConditionVariable(static::class)
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($this->get_id())
                )
            )
        );

        $targetConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class, RelationInstance::PROPERTY_TARGET_TYPE
                    ), new StaticConditionVariable(static::class)
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_ID),
                    new StaticConditionVariable($this->get_id())
                )
            )
        );

        $dependencies[RelationInstance::class] = new OrCondition(array($sourceConditions, $targetConditions));

        $dependencies[ElementInstance::class] = new EqualityCondition(
            new PropertyConditionVariable(ElementInstance::class, ElementInstance::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id())
        );

        $dependencies[Vocabulary::class] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id())
        );

        $dependencies[ProviderLink::class] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink::class, ProviderLink::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id())
        );

        return $dependencies;
    }

    /**
     * Returns the display_name
     *
     * @return string
     */
    public function get_display_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_NAME);
    }

    /**
     * Returns the display_order
     *
     * @return int
     */
    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self::class, self::PROPERTY_SCHEMA_ID));
    }

    /**
     * Returns the property for the display order
     *
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class, self::PROPERTY_DISPLAY_ORDER);
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
     * Returns the prefix of the schema namespace
     *
     * @return string
     */
    public function get_namespace()
    {
        if (!$this->namespace)
        {
            $schema = \Chamilo\Core\Metadata\Storage\DataManager::retrieve_by_id(
                Schema::class, $this->get_schema_id()
            );

            if (!$schema)
            {
                return false;
            }

            $this->set_namespace($schema->get_namespace());
        }

        return $this->namespace;
    }

    /**
     * Sets the prefix of the schema namespace
     *
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the schema_id
     *
     * @return int
     */
    public function get_schema_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCHEMA_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'metadata_element';
    }

    /**
     * Returns the value_limit
     *
     * @return int
     */
    public function get_value_limit()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE_LIMIT);
    }

    /**
     * Returns the value_type
     *
     * @return int
     */
    public function get_value_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE_TYPE);
    }

    public function isNumberOfValuesLimited()
    {
        return $this->get_value_limit() > 0;
    }

    public function isVocabularyPredefined()
    {
        return $this->get_value_type() == self::VALUE_TYPE_VOCABULARY_PREDEFINED ||
            $this->get_value_type() == self::VALUE_TYPE_VOCABULARY_BOTH;
    }

    public function isVocabularyUserDefined()
    {
        return $this->get_value_type() == self::VALUE_TYPE_VOCABULARY_USER ||
            $this->get_value_type() == self::VALUE_TYPE_VOCABULARY_BOTH;
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
     * Moves this object with the display order
     *
     * @param int $direction
     *
     * @return bool
     */
    public function move($direction)
    {
        $this->set_display_order($this->get_display_order() + $direction);

        return $this->update();
    }

    /**
     * Renders the name of this attribute with the prefix of the namespace
     *
     * @return string
     */
    public function render_name()
    {
        $pref = $this->get_namespace();
        $prefix = (empty($pref)) ? '' : $this->get_namespace() . ':';

        return $prefix . $this->get_name();
    }

    /**
     * Sets the display_name
     *
     * @param string $display_name
     */
    public function set_display_name($display_name)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_NAME, $display_name);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Sets the display_order
     *
     * @param int display_order
     */
    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
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
     * Sets the schema_id
     *
     * @param int $schema_id
     */
    public function set_schema_id($schema_id)
    {
        $this->setDefaultProperty(self::PROPERTY_SCHEMA_ID, $schema_id);
    }

    /**
     * **************************************************************************************************************
     * Display order functionality *
     * **************************************************************************************************************
     */

    /**
     * Sets the value_limit
     *
     * @param int $value_limit
     */
    public function set_value_limit($value_limit)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE_LIMIT, $value_limit);
    }

    /**
     * Sets the value_type
     *
     * @param int $value_type
     */
    public function set_value_type($value_type)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE_TYPE, $value_type);
    }

    public function usesVocabulary()
    {
        return $this->get_value_type() != self::VALUE_TYPE_FREE;
    }
}
