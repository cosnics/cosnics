<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\RegistrationCacheDataPreLoader;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationConsulter
{

    protected RegistrationCacheDataPreLoader $registrationCacheDataPreLoader;

    protected StringUtilities $stringUtilities;

    public function __construct(
        RegistrationCacheDataPreLoader $registrationCacheDataPreLoader, StringUtilities $stringUtilities
    )
    {
        $this->registrationCacheDataPreLoader = $registrationCacheDataPreLoader;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @return string[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getContentObjectRegistrations(bool $alsoReturnInactiveTypes = true): array
    {
        $registrations = $this->getRegistrationsByType('Chamilo\Core\Repository\ContentObject');
        $contentObjectTypes = [];

        foreach ($registrations as $registration)
        {
            if ($alsoReturnInactiveTypes || $this->isRegistrationActive($registration))
            {
                $contentObjectTypes[] = $registration;
            }
        }

        return $contentObjectTypes;
    }

    /**
     * @return string[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getIntegrationRegistrations(string $integration, ?string $root = null): array
    {
        $registrations = $this->getRegistrations();
        $integrationRegistrations =
            $registrations[RegistrationCacheDataPreLoader::REGISTRATION_INTEGRATION][$integration];

        if ($root)
        {
            $rootIntegrationRegistrations = [];

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

    public function getRegistrationCacheDataPreLoader(): RegistrationCacheDataPreLoader
    {
        return $this->registrationCacheDataPreLoader;
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getRegistrationContexts(): array
    {
        $registrations = $this->getRegistrations();

        return array_keys($registrations[RegistrationCacheDataPreLoader::REGISTRATION_CONTEXT]);
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getRegistrationForContext(string $context): array
    {
        $registrations = $this->getRegistrations();

        return $registrations[RegistrationCacheDataPreLoader::REGISTRATION_CONTEXT][$context];
    }

    /**
     * @return string[][][][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getRegistrations(): array
    {
        return $this->getRegistrationCacheDataPreLoader()->getRegistrations();
    }

    /**
     * @return string[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getRegistrationsByType(string $type): array
    {
        $registrations = $this->getRegistrations();

        return $registrations[RegistrationCacheDataPreLoader::REGISTRATION_TYPE][$type];
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isContextRegistered(string $context): bool
    {
        $registration = $this->getRegistrationForContext($context);

        return !empty($registration);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function isContextRegisteredAndActive(string $context): bool
    {
        $registration = $this->getRegistrationForContext($context);

        return $this->isContextRegistered($context) && $this->isRegistrationActive($registration);
    }

    /**
     * @param string[] $registration
     */
    public function isRegistrationActive(array $registration): bool
    {
        return $registration[Registration::PROPERTY_STATUS] == Registration::STATUS_ACTIVE;
    }
}
