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
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Libraries\Rights\Service\RightsService
{
    const TYPE_PUBLICATION = 1;

    const VIEW_RIGHT = 1;

    /**
     * @var \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    private $userEntityProvider;

    /**
     * @var \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    private $groupEntityProvider;

    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
    }

    /**
     * @param integer $userIdentifier
     * @param integer $publicationIdentifier
     *
     * @return boolean
     */
    public function canUserIdentifierViewPublication(int $userIdentifier, int $publicationIdentifier)
    {
        try
        {
            return $this->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
                $userIdentifier, self::VIEW_RIGHT, $this->getEntities(), $publicationIdentifier, self::TYPE_PUBLICATION
            );
        }
        catch (RightsLocationNotFoundException $exception)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function createPublicationRightsLocation(Publication $publication)
    {
        return $this->createRightsLocationFromParameters(
            self::TYPE_PUBLICATION, $publication->getId(), false, $this->getRootLocationIdentifier()
        );
    }

    /**
     * @param bool $returnLocation
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     * @throws \Exception
     */
    public function createRoot(bool $returnLocation = true)
    {
        return $this->createSubtreeRootLocation(0, self::TREE_TYPE_ROOT, $returnLocation);
    }

    public function deletePublicationRightsLocation(Publication $publication)
    {
        $rightsLocation = $this->findRightsLocationByParameters($publication->getId(), self::TYPE_PUBLICATION);

        return $this->deleteRightsLocation($rightsLocation);
    }

    /**
     * @param integer $right
     * @param integer $userIdentifier
     *
     * @return integer[]
     * @throws \Exception
     *
     */
    public function findPublicationIdentifiersWithRightForUserIdentifier(
        int $right, int $userIdentifier
    )
    {
        return $this->findRightsLocationIdentifiersWithGrantedRight(
            $right, $this->getRootLocation(), self::TYPE_PUBLICATION, $userIdentifier, $this->getEntities()
        );
    }

    /**
     * @param integer $userIdentifier
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findPublicationIdentifiersWithViewRightForUserIdentifier(
        $userIdentifier
    )
    {
        return $this->findPublicationIdentifiersWithRightForUserIdentifier(
            self::VIEW_RIGHT, $userIdentifier
        );
    }

    /**
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    public function getEntities()
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = $this->getUserEntityProvider();
        $entities[GroupEntityProvider::ENTITY_TYPE] = $this->getGroupEntityProvider();

        return $entities;
    }

    /**
     * @return \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    /**
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     */
    public function setGroupEntityProvider(GroupEntityProvider $groupEntityProvider): void
    {
        $this->groupEntityProvider = $groupEntityProvider;
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocationEntityRight
     */
    protected function getRightsLocationEntityRightInstance()
    {
        return new RightsLocationEntityRight();
    }

    /**
     * @return \Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocation
     */
    protected function getRightsLocationInstance()
    {
        return new RightsLocation();
    }

    /**
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    /**
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     */
    public function setUserEntityProvider(UserEntityProvider $userEntityProvider): void
    {
        $this->userEntityProvider = $userEntityProvider;
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return integer[][]
     * @throws \Exception
     */
    public function getViewTargetUsersAndGroupsIdentifiersForPublicationIdentifier(int $publicationIdentifier)
    {
        return $this->getTargetEntities(self::VIEW_RIGHT, $publicationIdentifier, self::TYPE_PUBLICATION);
    }

    /**
     * @param \Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication $publication
     * @param integer $userIdentifier
     * @param string[] $values
     *
     * @return boolean
     * @throws \Exception
     */
    public function updatePublicationRights(Publication $publication, int $userIdentifier, $values)
    {
        $rightsLocation = $this->findRightsLocationByParameters($publication->getId(), self::TYPE_PUBLICATION);

        if (!$this->deleteRightsLocationEntityRightsForLocationAndRight($rightsLocation, self::VIEW_RIGHT))
        {
            return false;
        }

        if ($rightsLocation->inherits())
        {
            $rightsLocation->disinherit();
            if (!$this->updateRightsLocation($rightsLocation))
            {
                return false;
            }
        }

        $option = $values[PublicationForm::PROPERTY_RIGHT_OPTION];
        $locationIdentifier = $rightsLocation->getId();

        switch ($option)
        {
            case PublicationForm::RIGHT_OPTION_ALL :
                if (!$this->invertLocationEntityRight(self::VIEW_RIGHT, 0, 0, $locationIdentifier))
                {
                    return false;
                }
                break;
            case PublicationForm::RIGHT_OPTION_ME :
                if (!$this->invertLocationEntityRight(
                    self::VIEW_RIGHT, $userIdentifier, UserEntityProvider::ENTITY_TYPE, $locationIdentifier
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
                        if (!$this->invertLocationEntityRight(
                            self::VIEW_RIGHT, $entityIdentifier, $entityType, $locationIdentifier
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