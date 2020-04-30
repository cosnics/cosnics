<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Core\Repository\Storage\Repository\TemplateRegistrationRepository;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TemplateRegistrationLoader implements CacheableDataLoaderInterface
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
     */
    public function __construct(
        StringUtilities $stringUtilities, TemplateRegistrationRepository $templateRegistrationRepository
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->templateRegistrationRepository = $templateRegistrationRepository;
    }

    /**
     * @return boolean
     */
    public function clearData()
    {
        return $this->getTemplateRegistrationRepository()->clearTemplateRegistrationCache();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration[][]
     * @throws \Exception
     */
    public function getData()
    {
        $templateRegistrations = $this->getTemplateRegistrationRepository()->findTemplateRegistrations();
        $groupedRegistrations = array();

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

        return $groupedRegistrations;
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
}
