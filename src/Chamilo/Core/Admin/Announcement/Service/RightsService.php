<?php
namespace Chamilo\Core\Admin\Announcement\Service;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocation;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Announcement\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService
{
    public const TYPE_PUBLICATION = 1;

    public const VIEW_RIGHT = 1;

    protected RightsRepository $rightsRepository;

    protected \Chamilo\Libraries\Rights\Service\RightsService $rightsService;

    protected Translator $translator;

    protected UserService $userService;

    private GroupEntityProvider $groupEntityProvider;

    private UserEntityProvider $userEntityProvider;

    public function __construct(
        \Chamilo\Libraries\Rights\Service\RightsService $rightsService, RightsRepository $rightsRepository,
        UserService $userService, Translator $translator, UserEntityProvider $userEntityProvider,
        GroupEntityProvider $groupEntityProvider
    )
    {
        $this->rightsService = $rightsService;
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
    }

    public function canUserIdentifierViewPublication(string $userIdentifier, string $publicationIdentifier): bool
    {
        try
        {
            return $this->getRightsService()->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
                RightsLocation::class, RightsLocationEntityRight::class, $userIdentifier, self::VIEW_RIGHT,
                $this->getEntities(), $publicationIdentifier, self::TYPE_PUBLICATION
            );
        }
        catch (RightsLocationNotFoundException)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return bool
     */
    public function createPublicationRightsLocation(Publication $publication): bool
    {
        return $this->getRightsService()->createRightsLocationFromParameters(
            RightsLocation::class, self::TYPE_PUBLICATION, $publication->getId(), 0,
            $this->getRightsService()->getRootLocationIdentifier(RightsLocation::class)
        );
    }

    public function createRoot(): bool
    {
        return $this->getRightsService()->createSubtreeRootLocation(
            RightsLocation::class, '0', \Chamilo\Libraries\Rights\Service\RightsService::TREE_TYPE_ROOT
        );
    }

    public function deletePublicationRightsLocation(Publication $publication): bool
    {
        $rightsLocation = $this->getRightsService()->findRightsLocationByParameters(
            RightsLocation::class, $publication->getId(), self::TYPE_PUBLICATION
        );

        return $this->getRightsService()->deleteRightsLocation(RightsLocationEntityRight::class, $rightsLocation);
    }

    /**
     * @return string[]
     */
    public function findPublicationIdentifiersWithRightForUserIdentifier(
        int $right, string $userIdentifier
    ): array
    {
        return $this->getRightsService()->findRightsLocationIdentifiersWithGrantedRight(
            RightsLocation::class, RightsLocationEntityRight::class, $right,
            $this->getRightsService()->getRootLocation(RightsLocation::class), self::TYPE_PUBLICATION, $userIdentifier,
            $this->getEntities()
        );
    }

    /**
     * @return string[]
     */
    public function findPublicationIdentifiersWithViewRightForUserIdentifier(string $userIdentifier): array
    {
        return $this->findPublicationIdentifiersWithRightForUserIdentifier(self::VIEW_RIGHT, $userIdentifier);
    }

    /**
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    public function getEntities(): array
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = $this->getUserEntityProvider();
        $entities[GroupEntityProvider::ENTITY_TYPE] = $this->getGroupEntityProvider();

        return $entities;
    }

    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    protected function getRightsLocationEntityRightInstance(
    ): \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
    {
        return new RightsLocationEntityRight();
    }

    protected function getRightsLocationInstance(): \Chamilo\Libraries\Rights\Domain\RightsLocation
    {
        return new RightsLocation();
    }

    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    public function getRightsService(): \Chamilo\Libraries\Rights\Service\RightsService
    {
        return $this->rightsService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param string $publicationIdentifier
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getViewTargetUsersAndGroupsIdentifiersForPublicationIdentifier(string $publicationIdentifier): array
    {
        return $this->getRightsService()->getTargetEntities(
            RightsLocation::class, RightsLocationEntityRight::class, self::VIEW_RIGHT, $publicationIdentifier,
            self::TYPE_PUBLICATION
        );
    }

    /**
     * @param string[][] $values
     */
    public function updatePublicationRights(Publication $publication, string $userIdentifier, array $values): bool
    {
        $rightsLocation = $this->getRightsService()->findRightsLocationByParameters(
            RightsLocation::class, $publication->getId(), self::TYPE_PUBLICATION
        );

        if (!$this->getRightsService()->deleteRightsLocationEntityRightsForLocationAndRight(
            RightsLocationEntityRight::class, $rightsLocation, self::VIEW_RIGHT
        ))
        {
            return false;
        }

        if ($rightsLocation->inherits())
        {
            $rightsLocation->disinherit();
            if (!$this->getRightsService()->updateRightsLocation($rightsLocation))
            {
                return false;
            }
        }

        $option = $values[PublicationForm::PROPERTY_RIGHT_OPTION];
        $locationIdentifier = $rightsLocation->getId();

        switch ($option)
        {
            case PublicationForm::RIGHT_OPTION_ALL :
                if (!$this->getRightsService()->invertLocationEntityRight(
                    RightsLocationEntityRight::class, self::VIEW_RIGHT, '0', 0, $locationIdentifier
                ))
                {
                    return false;
                }
                break;
            case PublicationForm::RIGHT_OPTION_ME :
                if (!$this->getRightsService()->invertLocationEntityRight(
                    RightsLocationEntityRight::class, self::VIEW_RIGHT, $userIdentifier,
                    UserEntityProvider::ENTITY_TYPE, $locationIdentifier
                ))
                {
                    return false;
                }
                break;
            case PublicationForm::RIGHT_OPTION_SELECT :
                foreach ($values[PublicationForm::PROPERTY_TARGETS] as $entityType => $entityIdentifiers)
                {
                    foreach ($entityIdentifiers as $entityIdentifier)
                    {
                        if (!$this->getRightsService()->invertLocationEntityRight(
                            RightsLocationEntityRight::class, self::VIEW_RIGHT, $entityIdentifier, $entityType,
                            $locationIdentifier
                        ))
                        {
                            return false;
                        }
                    }
                }
        }

        return true;
    }
}