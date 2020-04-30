<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
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
 * @author Jens Vanderheyden
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Schema extends DataClass implements EntityTranslationInterface
{
    use EntityTranslationTrait;
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_NAMESPACE = 'namespace';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_URL = 'url';
    const PROPERTY_FIXED = 'fixed';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Get the default properties
     * 
     * @param string[] $extended_property_names
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_NAMESPACE;
        $extended_property_names[] = self::PROPERTY_NAME;
        $extended_property_names[] = self::PROPERTY_DESCRIPTION;
        $extended_property_names[] = self::PROPERTY_URL;
        $extended_property_names[] = self::PROPERTY_FIXED;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the namespace
     * 
     * @return string
     */
    public function get_namespace()
    {
        return $this->get_default_property(self::PROPERTY_NAMESPACE);
    }

    /**
     * Sets the namespace
     * 
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->set_default_property(self::PROPERTY_NAMESPACE, $namespace);
    }

    /**
     * Returns the name
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name
     * 
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the description
     * 
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description
     * 
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the url
     * 
     * @return string
     */
    public function get_url()
    {
        return $this->get_default_property(self::PROPERTY_URL);
    }

    /**
     * Sets the url
     * 
     * @param string $url
     */
    public function set_url($url)
    {
        $this->set_default_property(self::PROPERTY_URL, $url);
    }

    /**
     * Returns whether or not this element is fixed
     * 
     * @return string
     */
    public function is_fixed()
    {
        return $this->get_default_property(self::PROPERTY_FIXED);
    }

    /**
     * Sets whether or not the element is fixed
     * 
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->set_default_property(self::PROPERTY_FIXED, $fixed);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies()
    {
        $dependencies = array();
        
        $dependencies[EntityTranslation::class] = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class,
                        EntityTranslation::PROPERTY_ENTITY_TYPE), 
                    new StaticConditionVariable(static::class)),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        EntityTranslation::class,
                        EntityTranslation::PROPERTY_ENTITY_ID), 
                    new StaticConditionVariable($this->get_id()))));
        
        $sourceConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class,
                        RelationInstance::PROPERTY_SOURCE_TYPE), 
                    new StaticConditionVariable(static::class)),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($this->get_id()))));
        
        $targetConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class,
                        RelationInstance::PROPERTY_TARGET_TYPE), 
                    new StaticConditionVariable(static::class)),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_ID),
                    new StaticConditionVariable($this->get_id()))));
        
        $dependencies[RelationInstance::class] = new OrCondition(array($sourceConditions, $targetConditions));
        
        $dependencies[SchemaInstance::class] = new EqualityCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_SCHEMA_ID),
            new StaticConditionVariable($this->get_id()));
        
        $dependencies[Element::class] = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_SCHEMA_ID),
            new StaticConditionVariable($this->get_id()));
        
        return $dependencies;
    }

    /**
     *
     * @return string
     */
    public function getTranslationFallback()
    {
        return $this->get_name();
    }
}