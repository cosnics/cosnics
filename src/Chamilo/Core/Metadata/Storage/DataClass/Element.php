<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
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
    // Properties
    const PROPERTY_SCHEMA_ID = 'schema_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DISPLAY_NAME = 'display_name';
    const PROPERTY_FIXED = 'fixed';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_VALUE_TYPE = 'value_type';
    const PROPERTY_VALUE_LIMIT = 'value_limit';

    // Value types
    const VALUE_TYPE_FREE = 1;
    const VALUE_TYPE_VOCABULARY_PREDEFINED = 2;
    const VALUE_TYPE_VOCABULARY_USER = 3;
    const VALUE_TYPE_VOCABULARY_BOTH = 4;

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
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructor
     *
     * @param array $default_properties
     * @param array $optional_properties
     */
    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Get the default properties
     *
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_SCHEMA_ID;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_DISPLAY_NAME;
        $extended_property_names[] = self :: PROPERTY_FIXED;
        $extended_property_names[] = self :: PROPERTY_DISPLAY_ORDER;
        $extended_property_names[] = self :: PROPERTY_VALUE_TYPE;
        $extended_property_names[] = self :: PROPERTY_VALUE_LIMIT;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the schema_id
     *
     * @return int
     */
    public function get_schema_id()
    {
        return $this->get_default_property(self :: PROPERTY_SCHEMA_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    public function getSchema()
    {
        if (! isset($this->schema))
        {
            $this->schema = DataManager :: retrieve_by_id(Schema :: class_name(), $this->get_schema_id());
        }

        return $this->schema;
    }

    /**
     * Sets the schema_id
     *
     * @param int $schema_id
     */
    public function set_schema_id($schema_id)
    {
        $this->set_default_property(self :: PROPERTY_SCHEMA_ID, $schema_id);
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the display_name
     *
     * @return string
     */
    public function get_display_name()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_NAME);
    }

    /**
     * Sets the display_name
     *
     * @param string $display_name
     */
    public function set_display_name($display_name)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_NAME, $display_name);
    }

    /**
     * Returns whether or not this element is fixed
     *
     * @return string
     */
    public function is_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_FIXED);
    }

    /**
     * Sets whether or not the element is fixed
     *
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->set_default_property(self :: PROPERTY_FIXED, $fixed);
    }

    /**
     * Returns the display_order
     *
     * @return int
     */
    public function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Sets the display_order
     *
     * @param int display_order
     */
    public function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * Returns the value_type
     *
     * @return int
     */
    public function get_value_type()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE_TYPE);
    }

    /**
     * Sets the value_type
     *
     * @param int $value_type
     */
    public function set_value_type($value_type)
    {
        $this->set_default_property(self :: PROPERTY_VALUE_TYPE, $value_type);
    }

    public function usesVocabulary()
    {
        return $this->get_value_type() != self :: VALUE_TYPE_FREE;
    }

    public function isVocabularyPredefined()
    {
        return $this->get_value_type() == self :: VALUE_TYPE_VOCABULARY_PREDEFINED ||
             $this->get_value_type() == self :: VALUE_TYPE_VOCABULARY_BOTH;
    }

    public function isVocabularyUserDefined()
    {
        return $this->get_value_type() == self :: VALUE_TYPE_VOCABULARY_USER ||
             $this->get_value_type() == self :: VALUE_TYPE_VOCABULARY_BOTH;
    }

    /**
     * Returns the value_limit
     *
     * @return int
     */
    public function get_value_limit()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE_LIMIT);
    }

    /**
     * Sets the value_limit
     *
     * @param int $value_limit
     */
    public function set_value_limit($value_limit)
    {
        $this->set_default_property(self :: PROPERTY_VALUE_LIMIT, $value_limit);
    }

    public function isNumberOfValuesLimited()
    {
        return $this->get_value_limit() > 0;
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the prefix of the schema namespace
     *
     * @return string
     */
    public function get_namespace()
    {
        if (! $this->namespace)
        {
            $schema = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_by_id(
                Schema :: class_name(),
                $this->get_schema_id());

            if (! $schema)
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
     * **************************************************************************************************************
     * Display order functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the property for the display order
     *
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_SCHEMA_ID));
    }

    /**
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     */
    protected function get_dependencies()
    {
        $dependencies = array();

        $dependencies[EntityTranslation :: class_name()] = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation :: class_name(),
                        EntityTranslation :: PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(static :: class_name())),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation :: class_name(),
                        EntityTranslation :: PROPERTY_ENTITY_ID),
                    new StaticConditionVariable($this->get_id()))));

        $sourceConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance :: class_name(),
                        RelationInstance :: PROPERTY_SOURCE_TYPE),
                    new StaticConditionVariable(static :: class_name())),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance :: class_name(),
                        RelationInstance :: PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($this->get_id()))));

        $targetConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance :: class_name(),
                        RelationInstance :: PROPERTY_TARGET_TYPE),
                    new StaticConditionVariable(static :: class_name())),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance :: class_name(),
                        RelationInstance :: PROPERTY_TARGET_ID),
                    new StaticConditionVariable($this->get_id()))));

        $dependencies[RelationInstance :: class_name()] = new OrCondition(array($sourceConditions, $targetConditions));

        $dependencies[ElementInstance :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(ElementInstance :: class_name(), ElementInstance :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id()));

        $dependencies[Vocabulary :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id()));

        $dependencies[ProviderLink :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(ProviderLink :: class_name(), ProviderLink :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($this->get_id()));

        return $dependencies;
    }
}
