<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Cache\DataConsulterTrait;
use Chamilo\Libraries\Cache\Interfaces\DataConsulterInterface;
use Chamilo\Libraries\Cache\Interfaces\DataLoaderInterface;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationConsulter implements DataConsulterInterface
{
    use DataConsulterTrait;

    protected StringUtilities $stringUtilities;

    public function __construct(DataLoaderInterface $dataLoader, StringUtilities $stringUtilities)
    {
        $this->$dataLoader = $dataLoader;
        $this->stringUtilities = $stringUtilities;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTemplateRegistrationByIdentifier(int $identifier): ?TemplateRegistration
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationCacheDataLoader::REGISTRATION_ID][$identifier];
    }

    public function getTemplateRegistrationDefaultByType(string $type): ?TemplateRegistration
    {
        return $this->getTemplateRegistrations()[TemplateRegistrationCacheDataLoader::REGISTRATION_DEFAULT][$type];
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     */
    public function getTemplateRegistrations(): array
    {
        return $this->getDataLoader()->readData();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[]
     */
    public function getTemplateRegistrationsByTypesAndUserIdentifier(array $types, ?int $user_id = null): array
    {
        $templateRegistrations = $this->getTemplateRegistrations();

        $filteredTemplateRegistrations = [];

        foreach ($types as $type)
        {
            $commonTemplateRegistrations =
                (array) $templateRegistrations[TemplateRegistrationCacheDataLoader::REGISTRATION_USER_ID][0][$type];

            if (count($commonTemplateRegistrations) > 0)
            {
                $filteredTemplateRegistrations =
                    array_merge($filteredTemplateRegistrations, $commonTemplateRegistrations);
            }

            if ($user_id)
            {
                $userTemplateRegistrations =
                    (array) $templateRegistrations[TemplateRegistrationCacheDataLoader::REGISTRATION_USER_ID][$user_id][$type];

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
