<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationInstance extends DataClass
{
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_RELATION_ID = 'relation_id';
    const PROPERTY_SOURCE_ID = 'source_id';
    const PROPERTY_SOURCE_TYPE = 'source_type';
    const PROPERTY_TARGET_ID = 'target_id';
    const PROPERTY_TARGET_TYPE = 'target_type';
    const PROPERTY_USER_ID = 'user_id';

    private $relation;

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Relation
     */
    public function getRelation()
    {
        if (!isset($this->relation))
        {
            $this->relation = DataManager::retrieve_by_id(Relation::class, $this->get_relation_id());
        }

        return $this->relation;
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
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_SOURCE_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_SOURCE_ID;
        $extendedPropertyNames[] = self::PROPERTY_TARGET_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_TARGET_ID;
        $extendedPropertyNames[] = self::PROPERTY_RELATION_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_CREATION_DATE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     *
     * @return int
     */
    public function get_relation_id()
    {
        return $this->get_default_property(self::PROPERTY_RELATION_ID);
    }

    /**
     *
     * @return string
     */
    public function get_source_id()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE_ID);
    }

    /**
     *
     * @return string
     */
    public function get_source_type()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE_TYPE);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'metadata_relation_instance';
    }

    /**
     *
     * @return string
     */
    public function get_target_id()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_ID);
    }

    /**
     *
     * @return string
     */
    public function get_target_type()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_TYPE);
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
     * @param int $relationId
     */
    public function set_relation_id($relationId)
    {
        $this->set_default_property(self::PROPERTY_RELATION_ID, $relationId);
    }

    /**
     *
     * @param string $sourceId
     */
    public function set_source_id($sourceId)
    {
        $this->set_default_property(self::PROPERTY_SOURCE_ID, $sourceId);
    }

    /**
     *
     * @param string $sourceType
     */
    public function set_source_type($sourceType)
    {
        $this->set_default_property(self::PROPERTY_SOURCE_TYPE, $sourceType);
    }

    /**
     *
     * @param string $targetId
     */
    public function set_target_id($targetId)
    {
        $this->set_default_property(self::PROPERTY_TARGET_ID, $targetId);
    }

    /**
     *
     * @param string $targetType
     */
    public function set_target_type($targetType)
    {
        $this->set_default_property(self::PROPERTY_TARGET_TYPE, $targetType);
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