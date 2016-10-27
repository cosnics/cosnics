<?php
namespace Chamilo\Application\Survey\Service;

use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Publication\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationService
{
    const TYPE_PERSONAL = 1;
    const TYPE_PUBLICATION = 2;

    /**
     *
     * @var \Chamilo\Application\Survey\Repository\PublicationRepository
     */
    private $publicationRepository;

    /**
     *
     * @param \Chamilo\Application\Survey\Repository\PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Repository\PublicationRepository
     */
    public function getPublicationRepository()
    {
        return $this->publicationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Repository\PublicationRepository $publicationRepository
     */
    public function setPublicationRepository($publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getPublicationByIdentifier($identifier)
    {
        return $this->getPublicationRepository()->findPublicationByIdentifier($identifier);
    }

    /**
     *
     * @param integer[] $identifiers
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getPublicationsByIdentifiers($identifiers)
    {
        return $this->getPublicationRepository()->findPublicationsByIdentifiers($identifiers);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Architecture\PublicationInterface
     */
    public function determinePublicationForUserByIdentifier(User $user, $identifier = null)
    {
        if (! is_null($identifier))
        {
            if (! is_numeric($identifier))
            {
                throw new \InvalidArgumentException();
            }

            return $this->getPublicationByIdentifier($identifier);
        }
        else
        {
            return $this->getPersonalPublicationForUser($user);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getPublicationsByCreator(User $user, $limit, $offset, $orderProperty = null)
    {
        return $this->getPublicationRepository()->findPublicationsByCreator($user, $limit, $offset, $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getPublicationsForUser(User $user, $right = RightsService :: VIEW_RIGHT, $limit, $offset, $orderProperty = null)
    {
        return $this->getPublicationRepository()->findPublicationsForUser(
            $user,
            $this->getEntitiesForUser($user),
            $right,
            $limit,
            $offset,
            $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countPublicationsForUser(User $user, $right = RightsService :: VIEW_RIGHT)
    {
        return $this->getPublicationRepository()->countPublicationsForUser($user, $this->getEntitiesForUser($user), $right);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countPublicationsByCreator(User $user)
    {
        return $this->getPublicationRepository()->countPublicationsByCreator($user);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getAllPublications()
    {
        return $this->getPublicationRepository()->findAllPublications();
    }

    /**
     *
     * @return integer
     */
    public function countAllPublications()
    {
        return $this->getPublicationRepository()->countAllPublications();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getSharedPublicationsForUser(User $user, $limit, $offset, $orderProperty = null)
    {
        return $this->getPublicationRepository()->findSharedPublicationsForEntities(
            $this->getEntitiesForUser($user),
            $limit,
            $offset,
            $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countSharedPublicationsForUser(User $user)
    {
        return $this->getPublicationRepository()->countSharedPublicationsForEntities($this->getEntitiesForUser($user));
    }

    /**
     *
     * @param User $user
     * @return integer[]
     */
    public function getEntitiesForUser(User $user)
    {
        $entities = array();

        $entities[UserEntity :: ENTITY_TYPE] = array($user->get_id());
        $entities[PlatformGroupEntity :: ENTITY_TYPE] = array();

        $userGroupIdentifiers = $user->get_groups(true);

        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[PlatformGroupEntity :: ENTITY_TYPE][] = $userGroupIdentifier;
        }

        return $entities;
    }

    /**
     *
     * @param string[] $publicationProperties
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function createPublication($publicationProperties)
    {
        $publication = new Publication();
        $this->setPublicationProperties($publication, $publicationProperties);

        if (! $publication->create())
        {
            return false;
        }

        return $publication;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @param string[] $publicationProperties
     * @return boolean
     */
    public function updatePublication(Publication $publication, $publicationProperties)
    {
        $this->setPublicationProperties($publication, $publicationProperties);

        if (! $publication->update())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @return boolean
     */
    public function deletePublication(Publication $publication)
    {
        if (! $publication->delete())
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Application\Survey\Storage\DataClass\Publication $publication
     * @param string[] $publicationProperties
     */
    private function setPublicationProperties(Publication $publication, $publicationProperties)
    {
        $publication->setName($publicationProperties[Publication :: PROPERTY_TITLE]);
        $publication->setDescription($publicationProperties[Publication :: PROPERTY_DESCRIPTION]);
        $publication->setCreationDate($publicationProperties[Publication :: PROPERTY_PUBLISHED]);
        $publication->setCreatorId($publicationProperties[Publication :: PROPERTY_PUBLISHER_ID]);
    }

    /**
     *
     * @param EntityService $entityService
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getPublicationFavouritesByUser(EntityService $entityService, User $user, $limit, $offset,
        $orderProperty = null)
    {
        if (is_null($orderProperty))
        {
            $orderProperty = array(
                new OrderBy(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_TITLE),
                    SORT_ASC));
        }

        return $this->getPublicationRepository()->findPublicationFavouritesByUser(
            $user,
            $entityService->getEntitiesForUser($user),
            $limit,
            $offset,
            $orderProperty);
    }

    /**
     *
     * @param EntityService $entityService
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countPublicationFavouritesByUser(EntityService $entityService, User $user)
    {
        return $this->getPublicationRepository()->countPublicationFavouritesByUser(
            $user,
            $entityService->getEntitiesForUser($user));
    }

    /**
     *
     * @param integer $type
     * @param integer $typeIdentifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getPublicationByTypeAndTypeIdentifier($type, $typeIdentifier)
    {
        if ($type == self :: TYPE_PERSONAL)
        {
            $user = DataManager :: retrieve_by_id(User :: class_name(), $typeIdentifier);
            return $this->getPersonalPublicationForUser($user);
        }
        else
        {
            return $this->getPublicationByIdentifier($typeIdentifier);
        }
    }
}