<?php
namespace Chamilo\Core\Admin\Announcement\Service;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocation;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;

/**
 * @package Chamilo\Core\Admin\Announcement\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Core\Rights\Service\RightsService
{
    const TYPE_PUBLICATION = 1;

    const VIEW_RIGHT = 1;

    /**
     * @param integer $userIdentifier
     * @param integer $publicationIdentifier
     *
     * @return boolean
     */
    public function canUserIdentifierViewPublication(int $userIdentifier, int $publicationIdentifier)
    {
        $entities = array();
        $entities[] = UserEntity::getInstance();
        $entities[] = PlatformGroupEntity::getInstance();

        try
        {
            return $this->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
                $userIdentifier, self::VIEW_RIGHT, $entities, $publicationIdentifier, self::TYPE_PUBLICATION
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
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
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
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $userIdentifier
     *
     * @return integer[]
     */
    public function findPublicationIdentifiersWithRightForEntitiesAndUserIdentifier(
        int $right, array $entities, int $userIdentifier
    )
    {
        return $this->findRightsLocationIdentifiersWithGrantedRight(
            $right, $this->getRootLocation(), self::TYPE_PUBLICATION, $userIdentifier, $entities
        );
    }

    /**
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $userIdentifier
     *
     * @return integer[]
     */
    public function findPublicationIdentifiersWithViewRightForEntitiesAndUserIdentifier(
        $entities, $userIdentifier
    )
    {
        return $this->findPublicationIdentifiersWithRightForEntitiesAndUserIdentifier(
            self::VIEW_RIGHT, $entities, $userIdentifier
        );
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
                    self::VIEW_RIGHT, $userIdentifier, UserEntity::ENTITY_TYPE, $locationIdentifier
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