<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationLoader extends DoctrineFilesystemCacheService implements DataLoaderInterface
{
    const REGISTRATION_DEFAULT = 2;
    const REGISTRATION_ID = 1;
    const REGISTRATION_USER_ID = 3;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository
     */
    private $templateRegistrationRepository;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository $templateRegistrationRepository
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(
        StringUtilities $stringUtilities, TemplateRegistrationRepository $templateRegistrationRepository,
        ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        parent::__construct($configurablePathBuilder);
        $this->stringUtilities = $stringUtilities;
        $this->templateRegistrationRepository = $templateRegistrationRepository;
    }

    /**
     * @return boolean
     */
    public function clearData()
    {
        $this->getTemplateRegistrationRepository()->clearTemplateRegistrationCache();

        return $this->getCacheProvider()->delete($this->getIdentifier());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Core\Repository';
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    public function getData()
    {
        return $this->getForIdentifier($this->getIdentifier());
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(__CLASS__);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array($this->getIdentifier());
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
     * @return \Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository
     */
    public function getTemplateRegistrationRepository()
    {
        return $this->templateRegistrationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository $templateRegistrationRepository
     */
    public function setTemplateRegistrationRepository($templateRegistrationRepository)
    {
        $this->templateRegistrationRepository = $templateRegistrationRepository;
    }

    /**
     * @return boolean
     * @throws \Exception
     */
    public function loadData()
    {
        $templateRegistrations = $this->getTemplateRegistrationRepository()->findTemplateRegistrations();
        $groupedRegistrations = [];

        foreach ($templateRegistrations as $templateRegistration)
        {
            $contentObjectType = $templateRegistration->get_content_object_type();
            $userIdentifier = $templateRegistration->get_user_id();

            $groupedRegistrations[self::REGISTRATION_ID][$templateRegistration->getId()] = $templateRegistration;
            $groupedRegistrations[self::REGISTRATION_USER_ID][$contentObjectType][$userIdentifier][] =
                $templateRegistration;

            if ($templateRegistration->get_default())
            {
                $groupedRegistrations[self::REGISTRATION_DEFAULT][$contentObjectType] = $templateRegistration;
            }
        }

        return $this->getCacheProvider()->save($this->getIdentifier(), $groupedRegistrations);
    }

    /**
     * @param string $identifier
     *
     * @return boolean
     * @throws \Exception
     */
    public function warmUpForIdentifier($identifier)
    {
        return $this->loadData();
    }
}