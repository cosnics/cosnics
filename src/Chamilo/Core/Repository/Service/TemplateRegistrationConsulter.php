<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationConsulter
{
    protected StringUtilities $stringUtilities;

    protected TemplateRegistrationCacheDataPreLoader $templateRegistrationCacheDataPreLoader;

    public function __construct(
        TemplateRegistrationCacheDataPreLoader $templateRegistrationCacheDataPreLoader, StringUtilities $stringUtilities
    )
    {
        $this->templateRegistrationCacheDataPreLoader = $templateRegistrationCacheDataPreLoader;
        $this->stringUtilities = $stringUtilities;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getTemplateRegistrationByIdentifier(int $identifier): ?TemplateRegistration
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationCacheDataPreLoader::REGISTRATION_ID][$identifier];
    }

    public function getTemplateRegistrationCacheDataPreLoader(): TemplateRegistrationCacheDataPreLoader
    {
        return $this->templateRegistrationCacheDataPreLoader;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getTemplateRegistrationDefaultByType(string $type): ?TemplateRegistration
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationCacheDataPreLoader::REGISTRATION_DEFAULT][$type];
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getTemplateRegistrations(): array
    {
        return $this->getTemplateRegistrationCacheDataPreLoader()->getTemplateRegistrations();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getTemplateRegistrationsByTypesAndUserIdentifier(array $types, ?int $user_id = null): array
    {
        $templateRegistrations = $this->getTemplateRegistrations();

        $filteredTemplateRegistrations = [];

        foreach ($types as $type)
        {
            $commonTemplateRegistrations =
                (array) $templateRegistrations[TemplateRegistrationCacheDataPreLoader::REGISTRATION_USER_ID][0][$type];

            if (count($commonTemplateRegistrations) > 0)
            {
                $filteredTemplateRegistrations =
                    array_merge($filteredTemplateRegistrations, $commonTemplateRegistrations);
            }

            if ($user_id)
            {
                $userTemplateRegistrations =
                    (array) $templateRegistrations[TemplateRegistrationCacheDataPreLoader::REGISTRATION_USER_ID][$user_id][$type];

                if (count($userTemplateRegistrations) > 0)
                {
                    $filteredTemplateRegistrations =
                        array_merge($filteredTemplateRegistrations, $userTemplateRegistrations);
                }
            }
        }

        return $filteredTemplateRegistrations;
    }
}
