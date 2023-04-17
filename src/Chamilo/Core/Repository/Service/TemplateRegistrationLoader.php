<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository;
use Chamilo\Libraries\Cache\SymfonyCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationLoader extends SymfonyCacheService implements DataLoaderInterface
{
    public const REGISTRATION_DEFAULT = 2;
    public const REGISTRATION_ID = 1;
    public const REGISTRATION_USER_ID = 3;

    private StringUtilities $stringUtilities;

    private TemplateRegistrationRepository $templateRegistrationRepository;

    public function __construct(
        AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder,
        StringUtilities $stringUtilities, TemplateRegistrationRepository $templateRegistrationRepository
    )
    {
        parent::__construct($cacheAdapter, $configurablePathBuilder);

        $this->stringUtilities = $stringUtilities;
        $this->templateRegistrationRepository = $templateRegistrationRepository;
    }

    public function clearData(): bool
    {
        $this->getTemplateRegistrationRepository()->clearTemplateRegistrationCache();

        return $this->getCacheAdapter()->delete($this->getIdentifier());
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getData(): array
    {
        return $this->getForIdentifier($this->getIdentifier());
    }

    public function getIdentifier(): string
    {
        return md5(__CLASS__);
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return [$this->getIdentifier()];
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
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     */
    public function loadData(): bool
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

        $cacheItem = $this->getCacheAdapter()->getItem($this->getIdentifier());
        $cacheItem->set($groupedRegistrations);

        return $this->getCacheAdapter()->save($cacheItem);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        return $this->loadData();
    }
}