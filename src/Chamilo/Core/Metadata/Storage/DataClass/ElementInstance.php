<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * @package Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ElementInstance extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_ELEMENT_ID = 'element_id';
    public const PROPERTY_SCHEMA_INSTANCE_ID = 'schema_instance_id';
    public const PROPERTY_USER_ID = 'user_id';
    public const PROPERTY_VOCABULARY_ID = 'vocabulary_id';

    /**
     * Get the default properties
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_SCHEMA_INSTANCE_ID;
        $extendedPropertyNames[] = self::PROPERTY_ELEMENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_VOCABULARY_ID;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_CREATION_DATE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'metadata_element_instance';
    }

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
     * @return int
     */
    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    /**
     * @return int
     */
    public function get_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    /**
     * @return int
     */
    public function get_schema_instance_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SCHEMA_INSTANCE_ID);
    }

    /**
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @return int
     */
    public function get_vocabulary_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_VOCABULARY_ID);
    }

    /**
     * @param int
     */
    public function set_creation_date($creationDate)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creationDate);
    }

    /**
     * @param int
     */
    public function set_element_id($elementId)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $elementId);
    }

    /**
     * @param int
     */
    public function set_schema_instance_id($schemaInstanceId)
    {
        $this->setDefaultProperty(self::PROPERTY_SCHEMA_INSTANCE_ID, $schemaInstanceId);
    }

    /**
     * @param int
     */
    public function set_user_id($userId)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);
    }

    /**
     * @param int
     */
    public function set_vocabulary_id($vocabularyId)
    {
        $this->setDefaultProperty(self::PROPERTY_VOCABULARY_ID, $vocabularyId);
    }
}