<?php
namespace Chamilo\Core\Rights\Service;

use Chamilo\Core\Rights\Domain\RightsLocation;
use Chamilo\Core\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Core\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Rights\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RightsService
{
    // Types
    const TREE_TYPE_ROOT = 0;
    const TYPE_ROOT = 0;

    /**
     * @var \Chamilo\Core\Rights\Storage\Repository\RightsRepository
     */
    private $rightsRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var integer[]
     */
    private $locationIdentifiersCache;

    /**
     * @param \Chamilo\Core\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator
    )
    {
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param integer $type
     * @param integer $identifier
     * @param integer $inherit
     * @param integer $parent
     * @param integer $locked
     * @param integer $treeIdentifier
     * @param integer $treeType
     * @param boolean $returnLocation
     * @param boolean $createInBatch
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     * @see RightsUtil::create_location()
     */
    public function createRightsLocationFromParameters(
        int $type = self::TYPE_ROOT, int $identifier = 0, int $inherit = 0, int $parent = 0, int $locked = 0,
        int $treeIdentifier = 0, int $treeType = self::TREE_TYPE_ROOT, bool $returnLocation = false,
        bool $createInBatch = false
    )
    {
        $location = $this->getRightsLocationInstance();
        $location->set_parent_id($parent);
        $location->set_type($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($treeIdentifier);
        $location->set_tree_type($treeType);

        $success = $this->createRightsLocation($location, $createInBatch);
        $succes = $location->create(null, $createInBatch);

        if ($returnLocation && $success)
        {
            return $location;
        }
        else
        {
            return $succes;
        }
    }

    /**
     * @param integer $userIdentifier
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return boolean
     * @see RightsUtil::is_allowed_on_location()
     */
    public function doesUserIdentifierHaveRightForLocationAndEntities(
        int $userIdentifier, int $right, RightsLocation $location, array $entities
    )
    {
        $grantedRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
            $userIdentifier, $location, $entities
        );

        return in_array($right, $grantedRights);
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return integer[]
     * @see DataManager::retrieve_granted_rights_array()
     */
    public function findGrantedRightsForUserIdentifierLocationAndEntities(
        int $userIdentifier, RightsLocation $location, array $entities
    )
    {
        $grantedRights = $this->getRightsRepository()->findGrantedRightsForUserIdentifierLocationAndEntities(
            $userIdentifier, $location, $entities
        );

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier($location->get_parent_id());

            if ($parentLocation instanceof RightsLocation)
            {
                $parentRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
                    $userIdentifier, $parentLocation, $entities
                );
                $grantedRights = array_merge($grantedRights, $parentRights);
            }
        }

        return array_unique($grantedRights);
    }

    /**
     * @param integer $userIdentifier
     * @param integer $right
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return boolean
     * @throws \Chamilo\Core\Rights\Exception\RightsLocationNotFoundException
     * @see RightsUtil::is_allowed()
     */
    public function doesUserIdentifierHaveRightForEntitiesAndLocationIdentifiers(
        int $userIdentifier, int $right, array $entities, int $identifier = 0, int $type = self::TYPE_ROOT,
        int $treeIdentifier = 0, int $treeType = self::TREE_TYPE_ROOT
    )
    {
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user->is_platform_admin())
        {
            return true;
        }

        $location = $this->getRightsRepository()->findRightsLocationByParameters(
            $identifier, $type, $treeIdentifier, $treeType
        );

        if (!$location instanceof RightsLocation)
        {
            // todo: refactor to translation
            throw new RightsLocationNotFoundException(
                $this->getTranslator()->trans('NoLocationFound') . ';type=' . $type . ';identifier=' . $identifier .
                ';tree_id=' . $treeIdentifier . ';tree_type=' . $treeType
            );
        }

        return $this->doesUserIdentifierHaveRightForLocationAndEntities(
            $userIdentifier, $right, $location, $entities
        );
    }

    /**
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $parentLocation
     * @param integer $type
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     *
     * @return integer[]
     * @see RightsUtil::get_identifiers_with_right_granted()
     */
    public function findRightsLocationIdentifiersWithGrantedRight(
        int $right, RightsLocation $parentLocation, int $type, int $userIdentifier, array $entities
    )
    {
        $cacheKey = md5(serialize([$userIdentifier, $right, $parentLocation->getId()]));

        if (is_null($this->locationIdentifiersCache[$cacheKey]))
        {
            $parentHasRight = $this->doesUserIdentifierHaveRightForLocationAndEntities(
                $userIdentifier, $right, $parentLocation, $entities
            );

            $this->locationIdentifiersCache[$cacheKey] =
                $this->getRightsRepository()->findRightsLocationIdentifiersWithGrantedRight(
                    $right, $parentLocation, $type, $userIdentifier, $entities, $parentHasRight
                );
        }

        return $this->locationIdentifiersCache[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param boolean $createInBatch
     *
     * @return boolean
     */
    public function createRightsLocation(RightsLocation $location, bool $createInBatch = false)
    {
        return $location->create(null, $createInBatch);
    }

    /**
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     */
    public function findRightsLocationByParameters(
        int $identifier = 0, int $type = RightsService::TYPE_ROOT, int $treeIdentifier = 0,
        int $treeType = RightsService::TREE_TYPE_ROOT
    )
    {
        return $this->getRightsRepository()->findRightsLocationByParameters(
            $identifier, $type, $treeIdentifier, $treeType
        );
    }

    /**
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight
     */
    abstract public function getRightsLocationEntityRightInstance();

    /**
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     */
    abstract public function getRightsLocationInstance();

    /**
     * @return \Chamilo\Core\Rights\Storage\Repository\RightsRepository
     */
    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @param \Chamilo\Core\Rights\Storage\Repository\RightsRepository $rightsRepository
     */
    public function setRightsRepository(RightsRepository $rightsRepository): void
    {
        $this->rightsRepository = $rightsRepository;
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation[]
     */
    public function findLocationsWithGrantedRights(
        int $userIdentifier, array $entities, array $rights = array(), array $types = array(), $treeType = null,
        $treeIdentifier = null
    )
    {
        return $this->getRightsRepository()->findLocationsWithGrantedRights(
            $userIdentifier, $entities, $rights, $types, $treeType, $treeIdentifier
        );
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return integer[][]
     * @see RightsUtil::get_location_overview_with_rights_granted()
     */
    public function getLocationOverviewWithGrantedRights(
        int $userIdentifier, array $entities, array $rights = array(), array $types = array(), $treeType = null,
        $treeIdentifier = null
    )
    {
        $locations = $this->findLocationsWithGrantedRights(
            $userIdentifier, $entities, $rights, $types, $treeType, $treeIdentifier
        );

        $overview = array();
        foreach ($locations as $location)
        {
            $overview[$location[RightsLocation::PROPERTY_TYPE]][] = $location[RightsLocation::PROPERTY_IDENTIFIER];
        }

        return $overview;
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer[] $rights
     * @param integer[] $types
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return integer
     * @see RightsUtil::count_location_overview_with_rights_granted()
     */
    public function countLocationOverviewWithGrantedRights(
        int $userIdentifier, array $entities, array $rights = array(), array $types = array(), $treeType = null,
        $treeIdentifier = null
    )
    {
        return $this->countLocationOverviewWithGrantedRights(
            $userIdentifier, $entities, $rights, $types, $treeType, $treeIdentifier
        );
    }

    /**
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return integer[]
     */
    public function findRightsLocationIdentifiersByIdentifiersAndType(array $identifiers, int $type)
    {
        $locations = $this->findRightsLocationRecordsByIdentifiersAndType($identifiers, $type);

        $locationIdentifiers = array();

        foreach ($locations as $location)
        {
            $locationIdentifiers[$location[RightsLocation::PROPERTY_IDENTIFIER]] =
                $location[RightsLocation::PROPERTY_ID];
        }

        return $locationIdentifiers;
    }

    /**
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return string[]
     */
    public function findRightsLocationRecordsByIdentifiersAndType(array $identifiers, int $type)
    {
        return $this->getRightsRepository()->findRightsLocationRecordsByIdentifiersAndType($identifiers, $type);
    }

    /**
     * Filters given identifiers and returns those which the given user has access rights to.
     *
     * Why this function?: This function is an accelerated version of is_allowed(...) when called many times after each
     * other. The number of database queries is minimized by processing identifiers all at once.
     *
     * Steps:
     * -# Retrieve all locations belonging to any of the identifiers.
     * -# Retrieve all parent location recursively of all locations found in step 1. Store them in a simple array
     *    mapping child onto parent location ID's.
     * -# Concatenate all locations ID's of step 1 and all parent ID's of step 2 into an array.
     * -# Remove those location ID's which user has not access right to.
     * -# Loop over all locations retrieved in step 1: recursively visit all parent locations using the array created
     *    in step 2, and check is user has access to any of them. If yes, add the corresponding identifier to the result
     *    array.
     * -# Return collected identifiers.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $right
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return array of identifiers.
     */
    public function filterLocationIdentifiersByGrantedRight(
        User $user, array $entities, int $right, array $identifiers, int $type
    )
    {
        if ($user->is_platform_admin())
        {
            return $identifiers;
        }

        $locationIdentifiers = $this->findRightsLocationIdentifiersByIdentifiersAndType($identifiers, $type);
        $locationParentIdentifiers = $this->getLocationParentIdentifiersRecursive($locationIdentifiers);

        $allLocationIdentifiers =
            array_merge(array_values($locationIdentifiers), array_values($locationParentIdentifiers));
        $allLocationIdentifiersWithGrantedRight =
            $this->getLocationIdentifiersByGrantedRight($user->getId(), $entities, $right, $allLocationIdentifiers);

        $identifiersWithGrantedRight = array();

        foreach ($identifiers as $identifier)
        {
            if ($this->hasRightRecursive(
                $locationIdentifiers[$identifier], $locationParentIdentifiers, $allLocationIdentifiersWithGrantedRight
            ))
            {
                $identifiersWithGrantedRight[] = $identifier;
            }
        }

        return $identifiersWithGrantedRight;
    }

    /**
     * Returns whether given location or any of its ancestors is in array $location_ids_with_granted_right.
     *
     * @param integer $locationIdentifier                    location we check whether user has access rigth to.
     * @param integer[] $locationParentIdentifiers           mapping of child location ID's onto parent location ID's.
     * @param integer[] $locationIdentifiersWithGrantedRight All location ID's which user has access rigth to. Keys:
     *                                                       location ID's Values: True.
     *
     * @see RightsService::getLocationParentIdentifiersRecursive()
     * @see RightsService::filterLocationIdentifiersByGrantedRight()
     *
     * @return boolean
     */
    private function hasRightRecursive(
        $locationIdentifier, $locationParentIdentifiers, $locationIdentifiersWithGrantedRight
    )
    {
        if (isset($locationIdentifiersWithGrantedRight[$locationIdentifier]))
        {
            return true;
        }

        if (!isset($locationParentIdentifiers[$locationIdentifier]))
        {
            return false;
        }

        return $this->hasRightRecursive(
            $locationParentIdentifiers[$locationIdentifier], $locationParentIdentifiers,
            $locationIdentifiersWithGrantedRight
        );
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $right
     * @param integer[] $locationIdentifiers
     *
     * @return integer[]
     */
    public function getLocationIdentifiersByGrantedRight(
        int $userIdentifier, array $entities, int $right, array $locationIdentifiers
    )
    {
        $locationEntityRights = $this->getRightsRepository()->findLocationEntityRightRecordsByGrantedRight(
            $userIdentifier, $entities, $right, $locationIdentifiers
        );

        $locationIdentifiers = array();

        foreach ($locationEntityRights as $locationEntityRight)
        {
            $locationIdentifiers[$locationEntityRight[RightsLocationEntityRight::PROPERTY_LOCATION_ID]] = 1;
        }

        return $locationIdentifiers;
    }

    /**
     * Returns an array mapping child location ID's onto parent location ID's.
     *
     * Idea:
     * Retrieve the child-parent relation of location with as few queries as possible and store them in the memory. The
     * function has_right_recursive(...) will loop over the child-parent tree, which is much faster than the recursive
     * function calls to DataManager :: retrieve_granted_rights_array(...). This function actually retrieves the
     * location tree level-by-level starting with the leaf level, followed by parent level, then grandparents until an
     * empty level is found. Result is a flat array mapping each ID in $location_ids onto its parent ID and each parent
     * onto its grand parent D, etc. Result will only contain child location ID's if the 'inherit' property of the
     * location is true and the parent is not null.
     *
     * @param integer[] $locationIdentifiers
     *
     * @return integer[] Keys: child location ID's Values: parent location ID's.
     */
    public function getLocationParentIdentifiersRecursive(array $locationIdentifiers)
    {
        $allLocationParentIdentifiers = array();

        $locationParentIdentifiers = $locationIdentifiers;

        while (true)
        {
            $locationParentIdentifiers =
                $this->getLocationParentIdentifiersForLocationIdentifiers($locationParentIdentifiers);

            if (count($locationParentIdentifiers) == 0)
            {
                break;
            }

            $allLocationParentIdentifiers = $allLocationParentIdentifiers + $locationParentIdentifiers;
        }

        return $allLocationParentIdentifiers;
    }

    /**
     * @param integer[] $locationIdentifiers
     *
     * @return integer[]
     */
    public function getLocationParentIdentifiersForLocationIdentifiers(array $locationIdentifiers)
    {
        $locations = $this->findLocationParentIdentifierRecordsForLocationIdentifiers($locationIdentifiers);

        $locationParentIdentifiers = array();

        foreach ($locations as $location)
        {
            $locationParentIdentifiers[$location[RightsLocation::PROPERTY_ID]] =
                $location[RightsLocation::PROPERTY_PARENT_ID];
        }

        return $locationParentIdentifiers;
    }

    /**
     * @param integer[] $locationIdentifiers
     *
     * @return string[]
     */
    public function findLocationParentIdentifierRecordsForLocationIdentifiers(array $locationIdentifiers)
    {
        return $this->getRightsRepository()->findLocationParentIdentifierRecordsForLocationIdentifiers(
            $locationIdentifiers
        );
    }

    /**
     *
     * @param integer $right
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @throws \Exception
     * @return integer[]
     */
    public function getTargetEntities(
        $right, $identifier = 0, $type = self::TYPE_ROOT, $treeIdentifier = 0, $treeType = self::TREE_TYPE_ROOT
    )
    {
        $location = $this->findRightsLocationByParameters($identifier, $type, $treeIdentifier, $treeType);

        if (!$location)
        {
            // todo: refactor to translation
            throw new RightsLocationNotFoundException(
                $this->getTranslator()->trans('NoLocationFound') . ';type=' . $type . ';identifier=' . $identifier .
                ';tree_id=' . $treeIdentifier . ';tree_type=' . $treeType
            );
        }

        return $this->getTargetEntitiesForLocation($right, $location);
    }

    /**
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return integer[]
     */
    public function getTargetEntitiesForLocation(int $right, RightsLocation $location)
    {
        $entityRecords = $this->getRightsRepository()->findRightsEntityRecordsForRightAndLocation($right, $location);

        $targetEntities = array();

        foreach ($entityRecords as $entityRecord)
        {
            $targetEntities[$entityRecord[RightsLocationEntityRight::PROPERTY_ENTITY_TYPE]][] =
                $entityRecord[RightsLocationEntityRight::PROPERTY_ENTITY_ID];
        }

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier($location->get_parent_id());
            $parentEntities = $this->getTargetEntitiesForLocation($right, $parentLocation);

            foreach ($parentEntities as $type => $id_array)
            {
                if ($targetEntities[$type])
                {
                    $targetEntities[$type] = array_merge($parentEntities[$type], $targetEntities[$type]);
                }
                else
                {
                    $targetEntities[$type] = $parentEntities[$type];
                }
            }
        }

        return $targetEntities;
    }

    /**
     * @param integer $identifier
     * @param integer $type
     * @param integer $treeIdentifier
     * @param integer $treeType
     *
     * @return integer
     */
    public function getRightsLocationIdentifierByParameters(
        int $identifier = 0, int $type = RightsService::TYPE_ROOT, int $treeIdentifier = 0,
        int $treeType = RightsService::TREE_TYPE_ROOT
    )
    {
        $location = $this->findRightsLocationByParameters($identifier, $type, $treeIdentifier, $treeType);

        if ($location instanceof RightsLocation)
        {
            return $location->getId();
        }
        else
        {
            return 0;
        }
    }
}