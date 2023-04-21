<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataLoaderTrait;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationCacheDataLoader implements CacheDataLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataLoaderTrait;

    public const REGISTRATION_DEFAULT = 2;
    public const REGISTRATION_ID = 1;
    public const REGISTRATION_USER_ID = 3;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected StringUtilities $stringUtilities;

    protected TemplateRegistrationRepository $templateRegistrationRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder,
        StringUtilities $stringUtilities, TemplateRegistrationRepository $templateRegistrationRepository
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->stringUtilities = $stringUtilities;
        $this->templateRegistrationRepository = $templateRegistrationRepository;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     * @throws \Exception
     */
    public function getDataForCache(): array
    {
        $templateRegistrations = $this->getTemplateRegistrationRepository()->findTemplateRegistrations();
        $groupedRegistrations = [];

        foreach ($templateRegistrations as $templateRegistration)
        {
            $contentObjectType = $templateRegistration->get_content_object_type();
            $userIdentifier = $templateRegistration->get_user_id();

            $groupedRegistrations[self::REGISTRATION_ID][$templateRegistration->getId()] = $templateRegistration;
            $groupedRegistrations[self::REGISTRATION_USER_ID][$userIdentifier][$contentObjectType][] =
                $templateRegistration;

            if ($templateRegistration->get_default())
            {
                $groupedRegistrations[self::REGISTRATION_DEFAULT][$contentObjectType] = $templateRegistration;
            }
        }

        return $groupedRegistrations;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTemplateRegistrationRepository(): TemplateRegistrationRepository
    {
        return $this->templateRegistrationRepository;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getTemplateRegistrations(): array
    {
        return $this->loadCacheData();
    }
}