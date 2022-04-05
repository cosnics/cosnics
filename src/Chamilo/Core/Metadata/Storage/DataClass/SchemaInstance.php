<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaInstance extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_SCHEMA_ID = 'schema_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CREATION_DATE = 'creation_date';

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
        $extended_property_names[] = self::PROPERTY_ENTITY_TYPE;
        $extended_property_names[] = self::PROPERTY_ENTITY_ID;
        $extended_property_names[] = self::PROPERTY_SCHEMA_ID;
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_CREATION_DATE;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     *
     * @return string
     */
    public function get_entity_type()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param string $entityType
     */
    public function set_entity_type($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     *
     * @return string
     */
    public function get_entity_id()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param string $entityId
     */
    public function set_entity_id($entityId)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entityId);
    }

    /**
     *
     * @return int
     */
    public function get_schema_id()
    {
        return $this->get_default_property(self::PROPERTY_SCHEMA_ID);
    }

    /**
     *
     * @param int $schemaId
     */
    public function set_schema_id($schemaId)
    {
        $this->set_default_property(self::PROPERTY_SCHEMA_ID, $schemaId);
    }

    public function getSchema()
    {
        if (! isset($this->schema))
        {
            $this->schema = DataManager::retrieve_by_id(Schema::class_name(), $this->get_schema_id());
        }
        
        return $this->schema;
    }

    /**
     *
     * @return integer
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function getUser()
    {
        return DataManager::retrieve_by_id(User::class_name(), $this->get_user_id());
    }

    /**
     *
     * @return integer
     */
    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATION_DATE);
    }

    /**
     *
     * @param integer
     */
    public function set_creation_date($creationDate)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $creationDate);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies($dependencies = array())
    {
        $dependencies = array();
        
        $sourceConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class_name(), 
                        RelationInstance::PROPERTY_SOURCE_TYPE), 
                    new StaticConditionVariable(static::class_name())), 
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_SOURCE_ID), 
                    new StaticConditionVariable($this->get_id()))));
        
        $targetConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class_name(), 
                        RelationInstance::PROPERTY_TARGET_TYPE), 
                    new StaticConditionVariable(static::class_name())), 
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_TARGET_ID), 
                    new StaticConditionVariable($this->get_id()))));
        
        $dependencies[RelationInstance::class_name()] = new OrCondition(array($sourceConditions, $targetConditions));
        
        $dependencies[ElementInstance::class_name()] = new EqualityCondition(
            new PropertyConditionVariable(ElementInstance::class_name(), ElementInstance::PROPERTY_SCHEMA_INSTANCE_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return $dependencies;
    }
}