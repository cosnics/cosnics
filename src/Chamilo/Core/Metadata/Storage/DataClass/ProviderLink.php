<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Storage\DataClass
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderLink extends DataClass
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration
     */
    private $providerRegistration;

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Element
     */
    private $element;
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ELEMENT_ID = 'element_id';
    const PROPERTY_PROVIDER_REGISTRATION_ID = 'provider_registration_id';

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
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ENTITY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_ELEMENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_PROVIDER_REGISTRATION_ID;
        
        return parent::getDefaultPropertyNames($extendedPropertyNames);
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
     * @return integer
     */
    public function get_element_id()
    {
        return $this->get_default_property(self::PROPERTY_ELEMENT_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element
     */
    public function getElement()
    {
        if (! isset($this->element))
        {
            $this->element = DataManager::retrieve_by_id(Element::class, $this->get_element_id());
        }
        
        return $this->element;
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
     * @return integer
     */
    public function get_provider_registration_id()
    {
        return $this->get_default_property(self::PROPERTY_PROVIDER_REGISTRATION_ID);
    }

    /**
     *
     * @param integer
     */
    public function set_provider_registration_id($providerRegistrationId)
    {
        $this->set_default_property(self::PROPERTY_PROVIDER_REGISTRATION_ID, $providerRegistrationId);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration
     */
    public function getProviderRegistration()
    {
        if (! isset($this->providerRegistration))
        {
            $this->providerRegistration = DataManager::retrieve_by_id(
                ProviderRegistration::class,
                $this->get_provider_registration_id());
        }
        
        return $this->providerRegistration;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'metadata_provider_link';
    }
}