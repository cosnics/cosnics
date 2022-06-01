<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Metadata\Schema\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTranslation extends DataClass
{
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ISOCODE = 'isocode';
    const PROPERTY_VALUE = 'value';

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
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_ID;
        $extendedPropertyNames[] = self::PROPERTY_ISOCODE;
        $extendedPropertyNames[] = self::PROPERTY_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     *
     * @return integer
     */
    public function get_entity_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @return integer
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * Get the ISO 639-1 code of the language
     *
     * @return string
     */
    public function get_isocode()
    {
        return $this->getDefaultProperty(self::PROPERTY_ISOCODE);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'metadata_entity_translation';
    }

    /**
     *
     * @return string
     */
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     *
     * @param integer
     */
    public function set_entity_id($entity_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_ID, $entity_id);
    }

    /**
     *
     * @param integer
     */
    public function set_entity_type($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * Set the ISO 639-1 code of the language
     *
     * @param string $isocode
     */
    public function set_isocode($isocode)
    {
        $this->setDefaultProperty(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     *
     * @param string
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }
}