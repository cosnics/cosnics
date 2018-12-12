<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectService
{
    /**
     * @var \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository
     */
    private $contentObjectRepository;

    /**
     * @var \Chamilo\Configuration\Service\RegistrationConsulter
     */
    private $registrationConsulter;

    /**
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     * @param \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(
        ContentObjectRepository $contentObjectRepository, RegistrationConsulter $registrationConsulter,
        StringUtilities $stringUtilities
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->registrationConsulter = $registrationConsulter;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository
     */
    public function getContentObjectRepository(): ContentObjectRepository
    {
        return $this->contentObjectRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function setContentObjectRepository(ContentObjectRepository $contentObjectRepository): void
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @return \Chamilo\Configuration\Service\RegistrationConsulter
     */
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\RegistrationConsulter $registrationConsulter
     */
    public function setRegistrationConsulter(RegistrationConsulter $registrationConsulter): void
    {
        $this->registrationConsulter = $registrationConsulter;
    }

    /**
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities): void
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @param boolean $alsoReturnInactiveTypes
     *
     * @return string[]
     */
    public function getContentObjectDataClassTypes(bool $alsoReturnInactiveTypes = true)
    {
        $contentObjectRegistrations =
            $this->getRegistrationConsulter()->getContentObjectRegistrations($alsoReturnInactiveTypes);
        $contentObjectDataClassTypes = array();

        foreach ($contentObjectRegistrations as $contentObjectRegistration)
        {
            $contentObjectContext = $contentObjectRegistration[Registration::PROPERTY_CONTEXT];
            $contentObjectName = $this->getStringUtilities()->createString(
                $contentObjectRegistration[Registration::PROPERTY_NAME]
            )->upperCamelize()->__toString();

            $types[] = $contentObjectContext . '\Storage\DataClass\\' . $contentObjectName;
        }

        return $contentObjectDataClassTypes;
    }

    /**
     * @return integer
     * @see \Chamilo\Core\Repository\Storage\DataManager::get_used_disk_space()
     * @todo Implement this
     */
    public function getUsedStorageSpace()
    {

    }

    /**
     * @return integer
     * @see \Chamilo\Core\Repository\Storage\DataManager::get_used_disk_space()
     * @todo Implement this
     */
    public function getUsedStorageSpaceForUser(User $user)
    {

    }

}