<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurationCacheService extends DoctrineFilesystemCacheService
{
    // Identifiers
    const IDENTIFIER_REGISTRATIONS = 'registrations';

    // Registration cache types
    const REGISTRATION_ID = 1;
    const REGISTRATION_DEFAULT = 2;
    const REGISTRATION_USER_ID = 3;

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $registrationObjects = DataManager :: retrieves(
            TemplateRegistration :: class_name(),
            new DataClassRetrievesParameters());
        $registrations = array();

        while ($registration = $registrationObjects->next_result())
        {
            $registrations[self :: REGISTRATION_ID][$registration->get_id()] = $registration;
            $registrations[self :: REGISTRATION_USER_ID][$registration->get_user_id()][$registration->get_content_object_type()][] = $registration;

            if ($registration->get_default())
            {
                $registrations[self :: REGISTRATION_DEFAULT][$registration->get_content_object_type()] = $registration;
            }
        }

        return $this->getCacheProvider()->save($identifier, $registrations);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Core\Repository\Configuration';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(self :: IDENTIFIER_REGISTRATIONS);
    }

    /**
     *
     * @return string[]
     */
    public function getRegistrationsCache()
    {
        return $this->getForIdentifier(self :: IDENTIFIER_REGISTRATIONS);
    }
}