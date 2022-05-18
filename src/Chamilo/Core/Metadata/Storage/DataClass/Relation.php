<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Interfaces\EntityTranslationInterface;
use Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
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
 * @package Chamilo\Core\Metadata\Relation\Storage\DataClass
 * @author Jens Vanderheyden
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Relation extends DataClass implements EntityTranslationInterface
{
    use EntityTranslationTrait;
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_NAME = 'name';

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
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
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
     * Sets the name
     * 
     * @param string $name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
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

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'metadata_relation';
    }
}