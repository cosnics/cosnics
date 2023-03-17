<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectService
{
    private ContentObjectRepository $contentObjectRepository;

    private RegistrationConsulter $registrationConsulter;

    private StringUtilities $stringUtilities;

    public function __construct(
        ContentObjectRepository $contentObjectRepository, RegistrationConsulter $registrationConsulter,
        StringUtilities $stringUtilities
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->registrationConsulter = $registrationConsulter;
        $this->stringUtilities = $stringUtilities;
    }

    public function getContentObjectRepository(): ContentObjectRepository
    {
        return $this->contentObjectRepository;
    }

    /**
     * @return string[]
     */
    public function getContentObjectTypes(bool $alsoReturnInactiveTypes = true): array
    {
        $contentObjectRegistrations =
            $this->getRegistrationConsulter()->getContentObjectRegistrations($alsoReturnInactiveTypes);
        $contentObjectTypes = [];

        foreach ($contentObjectRegistrations as $contentObjectRegistration)
        {
            $contentObjectContext = $contentObjectRegistration[Registration::PROPERTY_CONTEXT];
            $contentObjectName = $this->getStringUtilities()->createString(
                $contentObjectRegistration[Registration::PROPERTY_NAME]
            )->upperCamelize()->__toString();

            $contentObjectTypes[] = $contentObjectContext . '\Storage\DataClass\\' . $contentObjectName;
        }

        return $contentObjectTypes;
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \Exception
     */
    public function getUsedStorageSpace(): int
    {
        $contentObjectTypes = $this->getContentObjectTypes();
        $usedStorageSpace = 0;

        foreach ($contentObjectTypes as $contentObjectType)
        {
            $usedStorageSpace += $this->getUsedStorageSpaceForContentObjectType($contentObjectType);
        }

        return $usedStorageSpace;
    }

    /**
     * @throws \Exception
     */
    public function getUsedStorageSpaceForContentObjectType(string $contentObjectType): int
    {
        return $this->getContentObjectRepository()->getUsedStorageSpaceForContentObjectType(
            $contentObjectType
        );
    }

    /**
     * @throws \Exception
     */
    public function getUsedStorageSpaceForContentObjectTypeAndUser(string $contentObjectType, User $user): int
    {
        return $this->getContentObjectRepository()->getUsedStorageSpaceForContentObjectTypeAndUser(
            $contentObjectType, $user
        );
    }

    /**
     * @throws \Exception
     */
    public function getUsedStorageSpaceForUser(User $user): int
    {
        $contentObjectTypes = $this->getContentObjectTypes();
        $usedStorageSpace = 0;

        foreach ($contentObjectTypes as $contentObjectType)
        {
            $usedStorageSpace += $this->getUsedStorageSpaceForContentObjectTypeAndUser($contentObjectType, $user);
        }

        return $usedStorageSpace;
    }

    public function retrieveContentObjectByIdentifier(string $identifier): ?ContentObject
    {
        return $this->getContentObjectRepository()->retrieveContentObjectByIdentifier($identifier);
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function retrieveContentObjectsByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->getContentObjectRepository()->retrieveContentObjectsByIdentifiers($identifiers);
    }

}