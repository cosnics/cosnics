<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\Repository\RegistrationRepository;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataLoaderTrait;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationCacheDataLoader implements CacheDataLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataLoaderTrait;

    public const REGISTRATION_CONTEXT = 1;
    public const REGISTRATION_INTEGRATION = 3;
    public const REGISTRATION_TYPE = 2;

    private RegistrationRepository $registrationRepository;

    private StringUtilities $stringUtilities;

    public function __construct(
        AdapterInterface $cacheAdapter, StringUtilities $stringUtilities, RegistrationRepository $registrationRepository
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->stringUtilities = $stringUtilities;
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @return string[][][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getDataForCache(): array
    {
        $registrations = [];

        $registrationRecords = $this->getRegistrationRepository()->findRegistrationsAsRecords();

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

    public function getRegistrationRepository(): RegistrationRepository
    {
        return $this->registrationRepository;
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
