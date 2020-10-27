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
class ElementInstance extends DataClass
{
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_ELEMENT_ID = 'element_id';
    const PROPERTY_SCHEMA_INSTANCE_ID = 'schema_instance_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_VOCABULARY_ID = 'vocabulary_id';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    public function getUser()
    {
        return DataManager::retrieve_by_id(User::class, $this->get_user_id());
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    public function getVocabulary()
    {
        return DataManager::retrieve_by_id(Vocabulary::class, $this->get_vocabulary_id());
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
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_SCHEMA_INSTANCE_ID;
        $extendedPropertyNames[] = self::PROPERTY_ELEMENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_VOCABULARY_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_CREATION_DATE;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     *
     * @return integer
     */
    public function get_element_id()
    {
        return $this->get_default_property(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return integer
     */
    public function get_schema_instance_id()
    {
        return $this->get_default_property(self::PROPERTY_SCHEMA_INSTANCE_ID);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'metadata_element_instance';
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
     * @return integer
     */
    public function get_vocabulary_id()
    {
        return $this->get_default_property(self::PROPERTY_VOCABULARY_ID);
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
     * @param integer
     */
    public function set_element_id($elementId)
    {
        $this->set_default_property(self::PROPERTY_ELEMENT_ID, $elementId);
    }

    /**
     *
     * @param integer
     */
    public function set_schema_instance_id($schemaInstanceId)
    {
        $this->set_default_property(self::PROPERTY_SCHEMA_INSTANCE_ID, $schemaInstanceId);
    }

    /**
     *
     * @param integer
     */
    public function set_user_id($userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @param integer
     */
    public function set_vocabulary_id($vocabularyId)
    {
        $this->set_default_property(self::PROPERTY_VOCABULARY_ID, $vocabularyId);
    }
}