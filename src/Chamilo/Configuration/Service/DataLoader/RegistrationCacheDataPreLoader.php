<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataPreLoaderTrait;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationCacheDataPreLoader implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    public const REGISTRATION_CONTEXT = 1;
    public const REGISTRATION_INTEGRATION = 3;
    public const REGISTRATION_TYPE = 2;

    private RegistrationService $registrationService;

    private StringUtilities $stringUtilities;

    public function __construct(
        AdapterInterface $cacheAdapter, StringUtilities $stringUtilities, RegistrationService $registrationService
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->stringUtilities = $stringUtilities;
        $this->registrationService = $registrationService;
    }

    /**
     * @return string[][][][]
     */
    public function getDataForCache(): array
    {
        $registrations = [];

        $registrationRecords = $this->getRegistrationService()->findRegistrationsAsRecords();

        foreach ($registrationRecords as $registrationRecord)
        {
            $registrations[self::REGISTRATION_TYPE][$registrationRecord[Registration::PROPERTY_TYPE]][$registrationRecord[Registration::PROPERTY_CONTEXT]] =
                $registrationRecord;
            $registrations[self::REGISTRATION_CONTEXT][$registrationRecord[Registration::PROPERTY_CONTEXT]] =
                $registrationRecord;

            $contextStringUtilities = $this->getStringUtilities()->createString(
                $registrationRecord[Registration::PROPERTY_CONTEXT]
            );
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

    public function getRegistrationService(): RegistrationService
    {
        return $this->registrationService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getRegistrations(): array
    {
        return $this->loadCacheData();
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }
}
