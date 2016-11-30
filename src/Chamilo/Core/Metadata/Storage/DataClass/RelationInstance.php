<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Storage\DataClass\Relation;
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
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_SOURCE_TYPE = 'source_type';
    const PROPERTY_SOURCE_ID = 'source_id';
    const PROPERTY_TARGET_TYPE = 'target_type';
    const PROPERTY_TARGET_ID = 'target_id';
    const PROPERTY_RELATION_ID = 'relation_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CREATION_DATE = 'creation_date';

    private $relation;

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
        $extended_property_names[] = self::PROPERTY_SOURCE_TYPE;
        $extended_property_names[] = self::PROPERTY_SOURCE_ID;
        $extended_property_names[] = self::PROPERTY_TARGET_TYPE;
        $extended_property_names[] = self::PROPERTY_TARGET_ID;
        $extended_property_names[] = self::PROPERTY_RELATION_ID;
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
    public function get_source_type()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE_TYPE);
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
     * @return string
     */
    public function get_source_id()
    {
        return $this->get_default_property(self::PROPERTY_SOURCE_ID);
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
     * @return string
     */
    public function get_target_type()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_TYPE);
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
     * @return string
     */
    public function get_target_id()
    {
        return $this->get_default_property(self::PROPERTY_TARGET_ID);
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
     * @return int
     */
    public function get_relation_id()
    {
        return $this->get_default_property(self::PROPERTY_RELATION_ID);
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
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Relation
     */
    public function getRelation()
    {
        if (! isset($this->relation))
        {
            $this->relation = DataManager::retrieve_by_id(Relation::class_name(), $this->get_relation_id());
        }
        
        return $this->relation;
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
}