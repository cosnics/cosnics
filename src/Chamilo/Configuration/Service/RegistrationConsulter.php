<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Storage\DataClass\Registration;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationConsulter extends DataConsulter
{

    /**
     *
     * @return string[]
     */
    public function getRegistrations()
    {
        return $this->getData();
    }

    /**
     *
     * @param string $context
     * @return Registration
     */
    public function getRegistrationForContext($context)
    {
        $registrations = $this->getRegistrations();
        return $registrations[RegistrationLoader::REGISTRATION_CONTEXT][$context];
    }

    /**
     *
     * @return string[]
     */
    public function getRegistrationContexts()
    {
        $registrations = $this->getRegistrations();
        return array_keys($registrations[RegistrationLoader::REGISTRATION_CONTEXT]);
    }

    /**
     *
     * @param string $type
     * @return \configuration\Registration[]
     */
    public function getRegistrationsByType($type)
    {
        $registrations = $this->getRegistrations();
        return $registrations[RegistrationLoader::REGISTRATION_TYPE][$type];
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isContextRegistered($context)
    {
        $registration = $this->getRegistration($context);
        return ! empty($registration);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function isContextRegisteredAndActive($context)
    {
        $registration = $this->getRegistration($context);
        return $this->isContextRegistered($context) &&
             $registration[Registration::PROPERTY_STATUS] == Registration::STATUS_ACTIVE;
    }

    /**
     *
     * @param string $integration
     * @param string $root
     * @return string[]
     */
    public function getIntegrationRegistrations($integration, $root = null)
    {
        $registrations = $this->getRegistrations();
        $integrationRegistrations = $registrations[RegistrationLoader::REGISTRATION_INTEGRATION][$integration];

        if ($root)
        {
            $rootIntegrationRegistrations = array();

            foreach ($integrationRegistrations as $rootContext => $registration)
            {
                $rootContextStringUtilities = $this->getStringUtilities()->createString($rootContext);

                if ($rootContextStringUtilities->startsWith($root))
                {
                    $rootIntegrationRegistrations[$rootContext] = $registration;
                }
            }

            return $rootIntegrationRegistrations;
        }
        else
        {
            return $integrationRegistrations;
        }
    }
}
