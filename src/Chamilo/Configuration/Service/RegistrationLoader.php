<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Repository\RegistrationRepository;
use Chamilo\Configuration\Storage\DataClass\Registration;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationLoader implements CacheableDataLoaderInterface
{
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const REGISTRATION_INTEGRATION = 3;

    /**
     *
     * @var \Chamilo\Configuration\Repository\RegistrationRepository
     */
    private $registrationRepository;

    /**
     *
     * @param \Chamilo\Configuration\Repository\RegistrationRepository $registrationRepository
     */
    public function __construct(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Repository\RegistrationRepository
     */
    public function getRegistrationRepository()
    {
        return $this->registrationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Repository\RegistrationRepository $registrationRepository
     */
    public function setRegistrationRepository($registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        $registrationRecords = $this->getRegistrationRepository()->findRegistrations();
        $registrations = array();

        foreach ($registrationRecords as $registrationRecord)
        {
            $registrations[self::REGISTRATION_TYPE][$registrationRecord[Registration::PROPERTY_TYPE]][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;
            $registrations[self::REGISTRATION_CONTEXT][$registrationRecord[Registration::PROPERTY_CONTEXT]] = $registrationRecord;

            $contextStringUtilities = $this->getStringUtilities()->createString(
                $registrationRecord[Registration::PROPERTY_CONTEXT]);
            $isIntegration = $contextStringUtilities->contains('\Integration\\');

            if ($isIntegration)
            {
                /**
                 * Take last occurrence of integration instead of first
                 */
                $lastIntegrationIndex = $contextStringUtilities->indexOfLast('\Integration\\');

                $integrationContext = $contextStringUtilities->substr($lastIntegrationIndex + 13)->__toString();
                $rootContext = $contextStringUtilities->substr(0, $lastIntegrationIndex)->__toString();

                $registrations[self::REGISTRATION_INTEGRATION][$integrationContext][$rootContext] = $registrationRecord;
            }
        }

        return $registrations;
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(__CLASS__);
    }
}
