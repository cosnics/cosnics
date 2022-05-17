<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package common.libraries
 * @author Hans De Bisschop
 * @author Magali Gillard
 */
class Language extends DataClass
{
    
    /**
     * E.g.
     * Français
     * 
     * @var string
     */
    const PROPERTY_ORIGINAL_NAME = 'original_name';
    
    /**
     * E.g.
     * French
     * 
     * @var string
     */
    const PROPERTY_ENGLISH_NAME = 'english_name';
    
    /**
     * E.g.
     * romance
     * 
     * @var string
     */
    const PROPERTY_FAMILY = 'family';
    
    /**
     * E.g.
     * fr
     * 
     * @var string
     */
    const PROPERTY_ISOCODE = 'isocode';
    
    /**
     *
     * @var int
     */
    const PROPERTY_AVAILABLE = 'available';

    /**
     * Get the default properties of all languages
     * 
     * @param string[] $extendedPropertyNames
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_ORIGINAL_NAME, 
                self::PROPERTY_ENGLISH_NAME, 
                self::PROPERTY_FAMILY, 
                self::PROPERTY_ISOCODE, 
                self::PROPERTY_AVAILABLE));
    }

    /**
     *
     * @return \configuration\storage\DataManager
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the native name of the language
     * 
     * @return string
     */
    public function get_original_name()
    {
        return $this->get_default_property(self::PROPERTY_ORIGINAL_NAME);
    }

    /**
     * Get the english name of the language
     * 
     * @return string
     */
    public function get_english_name()
    {
        return $this->get_default_property(self::PROPERTY_ENGLISH_NAME);
    }

    /**
     * Get the family of the language
     * 
     * @return string
     */
    public function get_family()
    {
        return $this->get_default_property(self::PROPERTY_FAMILY);
    }

    /**
     * Get the ISO 639-1 code of the language
     * 
     * @return string
     */
    public function get_isocode()
    {
        return $this->get_default_property(self::PROPERTY_ISOCODE);
    }

    /**
     * Get the availability of the language
     * 
     * @return int
     */
    public function get_available()
    {
        return $this->get_default_property(self::PROPERTY_AVAILABLE);
    }

    /**
     * Set the native name of the language
     * 
     * @param string $original_name
     */
    public function set_original_name($original_name)
    {
        $this->set_default_property(self::PROPERTY_ORIGINAL_NAME, $original_name);
    }

    /**
     * Set the english name of the language
     * 
     * @param string $original_name
     */
    public function set_english_name($english_name)
    {
        $this->set_default_property(self::PROPERTY_ENGLISH_NAME, $english_name);
    }

    /**
     * Set the family of the language
     * 
     * @param string $family
     */
    public function set_family($family)
    {
        $this->set_default_property(self::PROPERTY_FAMILY, $family);
    }

    /**
     * Set the ISO 639-1 code of the language
     * 
     * @param string $isocode
     */
    public function set_isocode($isocode)
    {
        $this->set_default_property(self::PROPERTY_ISOCODE, $isocode);
    }

    public function set_available($available)
    {
        $this->set_default_property(self::PROPERTY_AVAILABLE, $available);
    }

    /**
     * Set the availability of the language
     * 
     * @return int
     */
    public function is_available()
    {
        return $this->get_available();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'configuration_language';
    }
}
