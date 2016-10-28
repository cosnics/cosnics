<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Configuration\Repository\RegistrationRepository $registrationRepository
     */
    public function __construct(StringUtilities $stringUtilities, DataLoaderInterface $dataLoader)
    {
        parent::__construct($dataLoader);
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

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
