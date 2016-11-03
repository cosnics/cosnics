<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Utilities\StringUtilities;

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
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Configuration\Storage\Repository\RegistrationRepository
     */
    private $registrationRepository;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Configuration\Storage\Repository\RegistrationRepository $registrationRepository
     */
    public function __construct(StringUtilities $stringUtilities, RegistrationRepository $registrationRepository)
    {
        $this->stringUtilities = $stringUtilities;
        $this->registrationRepository = $registrationRepository;
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
     * @return \Chamilo\Configuration\Storage\Repository\RegistrationRepository
     */
    public function getRegistrationRepository()
    {
        return $this->registrationRepository;
    }

    /**
     *
     * @param \Chamilo\Configuration\Storage\Repository\RegistrationRepository $registrationRepository
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
        $registrationRecords = $this->getRegistrationRepository()->findRegistrationsAsRecords();
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
