<?php
namespace Chamilo\Configuration\Service\Consulter;

use Chamilo\Configuration\Service\DataLoader\RegistrationCacheDataLoader;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Cache\DataConsulterTrait;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Interfaces\DataConsulterInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Service\Consulter
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class RegistrationConsulter implements DataConsulterInterface
{
    use DataConsulterTrait;

    protected StringUtilities $stringUtilities;

    public function __construct(CacheDataLoaderInterface $dataLoader, StringUtilities $stringUtilities)
    {
        $this->dataLoader = $dataLoader;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @return string[][]
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
     */
    public function getIntegrationRegistrations(string $integration, ?string $root = null): array
    {
        $registrations = $this->getRegistrations();
        $integrationRegistrations = $registrations[RegistrationCacheDataLoader::REGISTRATION_INTEGRATION][$integration];

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

    /**
     * @return string[]
     */
    public function getRegistrationContexts(): array
    {
        $registrations = $this->getRegistrations();

        return array_keys($registrations[RegistrationCacheDataLoader::REGISTRATION_CONTEXT]);
    }

    /**
     * @return string[]
     */
    public function getRegistrationForContext(string $context): array
    {
        $registrations = $this->getRegistrations();

        return $registrations[RegistrationCacheDataLoader::REGISTRATION_CONTEXT][$context];
    }

    /**
     * @return string[][][][]
     */
    public function getRegistrations(): array
    {
        return $this->getDataLoader()->loadCacheData();
    }

    /**
     * @return string[][]
     */
    public function getRegistrationsByType(string $type): array
    {
        $registrations = $this->getRegistrations();

        return $registrations[RegistrationCacheDataLoader::REGISTRATION_TYPE][$type];
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function isContextRegistered(string $context): bool
    {
        $registration = $this->getRegistrationForContext($context);

        return !empty($registration);
    }

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
