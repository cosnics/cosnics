<?php
namespace Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * @package Chamilo\Core\Metadata\Provider\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderLink extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ELEMENT_ID = 'element_id';
    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_PROVIDER_REGISTRATION_ID = 'provider_registration_id';

    /**
     * @var \Chamilo\Core\Metadata\Storage\DataClass\Element
     */
    private $element;

    /**
     * @var \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration
     */
    private $providerRegistration;

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
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element
     */
    public function getElement()
    {
        if (!isset($this->element))
        {
            $this->element = DataManager::retrieve_by_id(Element::class, $this->get_element_id());
        }

        return $this->element;
    }

    /**
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration
     */
    public function getProviderRegistration()
    {
        if (!isset($this->providerRegistration))
        {
            $this->providerRegistration = DataManager::retrieve_by_id(
                ProviderRegistration::class, $this->get_provider_registration_id()
            );
        }

        return $this->providerRegistration;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'metadata_provider_link';
    }

    /**
     * @return int
     */
    public function get_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ELEMENT_ID);
    }

    /**
     * @return string
     */
    public function get_entity_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return int
     */
    public function get_provider_registration_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PROVIDER_REGISTRATION_ID);
    }

    /**
     * @param int
     */
    public function set_element_id($elementId)
    {
        $this->setDefaultProperty(self::PROPERTY_ELEMENT_ID, $elementId);
    }

    /**
     * @param string $entityType
     */
    public function set_entity_type($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @param int
     */
    public function set_provider_registration_id($providerRegistrationId)
    {
        $this->setDefaultProperty(self::PROPERTY_PROVIDER_REGISTRATION_ID, $providerRegistrationId);
    }
}