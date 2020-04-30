<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

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
    const PROPERTY_CREATION_DATE = 'creation_date';

    const PROPERTY_ENTITY_ID = 'entity_id';

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ENTITY_TYPE = 'entity_type';

    const PROPERTY_SCHEMA_ID = 'schema_id';

    const PROPERTY_USER_ID = 'user_id';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Schema
     */
    private $schema;

    /**
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Schema
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
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
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    public function getUser()
    {
        return DataManager::retrieve_by_id(User::class, $this->get_user_id());
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
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     */
    protected function get_dependencies()
    {
        $dependencies = array();

        $sourceConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class_name(), RelationInstance::PROPERTY_SOURCE_TYPE
                    ), new StaticConditionVariable(static::class_name())
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_SOURCE_ID),
                    new StaticConditionVariable($this->get_id())
                )
            )
        );

        $targetConditions = new AndCondition(
            array(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        RelationInstance::class_name(), RelationInstance::PROPERTY_TARGET_TYPE
                    ), new StaticConditionVariable(static::class_name())
                ),
                new EqualityCondition(
                    new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_TARGET_ID),
                    new StaticConditionVariable($this->get_id())
                )
            )
        );

        $dependencies[RelationInstance::class_name()] = new OrCondition(array($sourceConditions, $targetConditions));

        $dependencies[ElementInstance::class_name()] = new EqualityCondition(
            new PropertyConditionVariable(ElementInstance::class_name(), ElementInstance::PROPERTY_SCHEMA_INSTANCE_ID),
            new StaticConditionVariable($this->get_id())
        );

        return $dependencies;
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
     * @return string
     */
    public function get_entity_type()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
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
    public function set_creation_date($creationDate)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $creationDate);
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
     * @param string $entityType
     */
    public function set_entity_type($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     *
     * @param int $schemaId
     */
    public function set_schema_id($schemaId)
    {
        $this->set_default_property(self::PROPERTY_SCHEMA_ID, $schemaId);
    }

    /**
     *
     * @param integer
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }
}