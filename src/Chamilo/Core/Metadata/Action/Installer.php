<?php
namespace Chamilo\Core\Metadata\Action;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration;
use Chamilo\Libraries\Platform\Translation;

/**
 * Extension of the generic installer for metadata integrations
 * 
 * @package Chamilo\Core\Metadata\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        if (! $this->registerPropertyProviders())
        {
            return $this->failed(Translation::get('PropertyProviderRegistrationFailed', null, Manager::package()));
        }
        
        return true;
    }

    /**
     *
     * @return string[]
     */
    public function getPropertyProviderTypes()
    {
        return array();
    }

    /**
     *
     * @return boolean
     */
    public function registerPropertyProviders()
    {
        foreach ($this->getPropertyProviderTypes() as $propertyProviderType)
        {
            $propertyProvider = new $propertyProviderType();
            
            $entityType = $propertyProvider->getEntityType();
            $entityProperties = $propertyProvider->getAvailableProperties();
            
            foreach ($entityProperties as $entityProperty)
            {
                $propertyRegistration = new ProviderRegistration();
                $propertyRegistration->set_entity_type($entityType);
                $propertyRegistration->set_provider_class($propertyProviderType);
                $propertyRegistration->set_property_name($entityProperty);
                
                if (! $propertyRegistration->create())
                {
                    $this->add_message(
                        self::TYPE_ERROR, 
                        Translation::get(
                            'EntityPropertyRegistrationFailed', 
                            array(
                                'ENTITY' => $entityType, 
                                'PROVIDER_CLASS' => $propertyProviderType, 
                                'PROPERTY_NAME' => $entityProperty)));
                    return false;
                }
                else
                {
                    $this->add_message(
                        self::TYPE_NORMAL, 
                        Translation::get(
                            'EntityPropertyRegistrationAdded', 
                            array(
                                'ENTITY' => $entityType, 
                                'PROVIDER_CLASS' => $propertyProviderType, 
                                'PROPERTY_NAME' => $entityProperty)));
                }
            }
        }
        
        return true;
    }
}
