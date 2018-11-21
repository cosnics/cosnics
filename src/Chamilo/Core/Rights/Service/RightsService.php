<?php
namespace Chamilo\Core\Rights\Service;

use Chamilo\Core\Rights\Domain\RightsLocation;
use Chamilo\Core\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Core\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
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
     * @var integer[][]
     */
    private $locationIdentifiersCache;

    /**
     * @var integer[][]
     */
    private $entityRightsCache;

    /**
     * @var string
     */
    private $rightsLocationClassName;

    /**
     * @var string
     */
    private $rightsLocationEntityRightClassName;

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
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param boolean $createInBatch
     *
     * @return boolean
     */
    public function createRightsLocation(RightsLocation $location, bool $createInBatch = false)
    {
        return $this->getRightsRepository()->createRightsLocation($location, $createInBatch);
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     */
    public function createRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight)
    {
        return $this->getRightsRepository()->createRightsLocationEntityRight($rightsLocationEntityRight);
    }

    /**
     * @param integer $right
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return boolean
     * @see RightsUtil::create_rights_location_entity_right()
     */
    public function createRightsLocationEntityRightFromParameters(
        int $right, int $entityIdentifier, int $entityType, int $locationIdentifier
    )
    {
        $rightsLocationEntityRight = $this->getRightsLocationEntityRightInstance();

        $rightsLocationEntityRight->set_location_id($locationIdentifier);
        $rightsLocationEntityRight->set_right_id($right);
        $rightsLocationEntityRight->set_entity_id($entityIdentifier);
        $rightsLocationEntityRight->set_entity_type($entityType);

        return $this->createRightsLocationEntityRight($rightsLocationEntityRight);
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
     * @return \Chamilo\Core\Rights\Domain\RightsLocation|boolean
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

        if ($returnLocation && $success)
        {
            return $location;
        }
        else
        {
            return $success;
        }
    }

    /**
     * @param integer $treeIdentifier
     * @param integer $treeType
     * @param boolean $returnLocation
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation
     * @see RightsUtil::create_subtree_root_location()
     */
    public function createSubtreeRootLocation(int $treeIdentifier, int $treeType, bool $returnLocation = false)
    {
        return $this->createRightsLocationFromParameters(
            self::TYPE_ROOT, 0, 0, 0, 0, $treeIdentifier, $treeType, $returnLocation
        );
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     */
    public function deleteRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight)
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRight($rightsLocationEntityRight);
    }

    /**
     *
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return boolean
     * @see RightsLocation::clear_rights()
     */
    public function deleteRightsLocationEntityRightsForLocation(RightsLocation $location)
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $location
        );
    }

    /**
     * Helper function to delete all the location entity right records for a given entity on a given location
     *
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param integer $entityIdentifier
     * @param integer $entityType
     *
     * @return boolean
     * @see RightsUtil::delete_location_entity_right_for_entity()
     */
    public function deleteRightsLocationEntityRightsForLocationAndEntityParameters(
        RightsLocation $location, int $entityIdentifier, int $entityType
    )
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $location, $entityIdentifier, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param integer $right
     *
     * @return boolean
     * @see RightsLocation::clear_right()
     */
    public function deleteRightsLocationEntityRightsForLocationAndRight(RightsLocation $location, int $right)
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $location, null, null, $right
        );
    }

    /**
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return boolean
     * @see RightsUtil::is_allowed_for_rights_entity_item()
     */
    public function doesEntityHaveRightForLocation(
        int $entityIdentifier, int $entityType, int $right, RightsLocation $location
    )
    {
        $grantedRights = $this->findGrantedRightsForEntityAndLocation(
            $entityIdentifier, $entityType, $location
        );

        return in_array($right, $grantedRights);
    }

    /**
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $right
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return boolean
     * @see RightsUtil::is_allowed_for_rights_entity_item_no_inherit()
     */
    public function doesEntityHaveRightForLocationWithoutInheritance(
        int $entityIdentifier, int $entityType, int $right, RightsLocation $location
    )
    {
        $grantedRight = $this->findRightsLocationEntityRightByParameters(
            $right, $entityIdentifier, $entityType, $location->getId()
        );

        if ($grantedRight instanceof RightsLocationEntityRight)
        {
            return true;
        }
        else
        {
            return false;
        }
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
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     *
     * @return integer[]
     * @see RightsUtil::get_granted_rights_for_rights_entity_item()
     */
    public function findGrantedRightsForEntityAndLocation(
        int $entityIdentifier, int $entityType, RightsLocation $location
    )
    {
        $cacheKey = md5(serialize([get_class($location), $entityIdentifier, $entityType, $location->getId()]));

        if (is_null($this->entityRightsCache[$cacheKey]))
        {
            $grantedRights = $this->getRightsRepository()->findGrantedRightsForEntityAndLocation(
                $entityIdentifier, $entityType, $location
            );

            if ($location->inherits())
            {
                $parentLocation =
                    $this->getRightsRepository()->findRightsLocationByIdentifier($location->get_parent_id());

                if ($parentLocation instanceof RightsLocation)
                {
                    $parentRights = $this->findGrantedRightsForEntityAndLocation(
                        $entityIdentifier, $entityType, $parentLocation
                    );
                    $grantedRights = array_merge($grantedRights, $parentRights);
                }
            }

            $grantedRights = array_unique($grantedRights);

            $this->entityRightsCache[$cacheKey] = $grantedRights;
        }

        return $this->entityRightsCache[$cacheKey];
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer[]
     * @see DataManager::retrieve_granted_rights_array()
     */
    public function findGrantedRightsForLocationAndCondition(
        RightsLocation $location, Condition $condition
    )
    {
        $grantedRights = $this->getRightsRepository()->findGrantedRightsForLocationAndCondition(
            $location, $condition
        );

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier($location->get_parent_id());

            if ($parentLocation instanceof RightsLocation)
            {
                $parentRights = $this->findGrantedRightsForLocationAndCondition($parentLocation, $condition);
                $grantedRights = array_merge($grantedRights, $parentRights);
            }
        }

        return array_unique($grantedRights);
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
     * @param integer[] $locationIdentifiers
     *
     * @return string[][]
     */
    public function findLocationParentIdentifierRecordsForLocationIdentifiers(array $locationIdentifiers)
    {
        return $this->getRightsRepository()->findLocationParentIdentifierRecordsForLocationIdentifiers(
            $locationIdentifiers
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
     * @return string[][]
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
     * @param integer $right
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight
     */
    public function findRightsLocationEntityRightByParameters(
        int $right, int $entityIdentifier, int $entityType, int $locationIdentifier
    )
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightByParameters(
            $right, $entityIdentifier, $entityType, $locationIdentifier
        );
    }

    /**
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return integer[]
     * @see DataManager::retrieve_location_ids_by_identifiers()
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
        $cacheKey = md5(serialize([get_class($parentLocation), $userIdentifier, $right, $parentLocation->getId()]));

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
     * @param integer[] $identifiers
     * @param integer $type
     *
     * @return string[][]
     */
    public function findRightsLocationRecordsByIdentifiersAndType(array $identifiers, int $type)
    {
        return $this->getRightsRepository()->findRightsLocationRecordsByIdentifiersAndType($identifiers, $type);
    }

    /**
     * @param \Chamilo\Core\Rights\Domain\RightsLocation $location
     * @param integer $right
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight[]
     */
    public function findRightsLocationRightsEntitiesForLocationAndRight(RightsLocation $location, int $right = null)
    {
        return $this->getRightsRepository()->findRightsLocationRightsForLocationIdentifierAndRights(
            $location->getId(), array($right)
        );
    }

    /**
     * @param integer $locationIdentifier
     * @param integer[] $rights
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight[]
     */
    public function findRightsLocationRightsForLocationIdentifierAndRights(int $locationIdentifier, array $rights)
    {
        return $this->getRightsRepository()->findRightsLocationRightsForLocationIdentifierAndRights(
            $locationIdentifier, $rights
        );
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\Rights\Entity\RightsEntity[] $entities
     * @param integer $right
     * @param integer[] $locationIdentifiers
     *
     * @return integer[]
     * @see DataMananager::filter_location_identifiers_by_granted_right()
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
     * @param integer[] $locationIdentifiers
     *
     * @return integer[]
     * @see DataManager::retrieve_location_parent_ids()
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
     * @see RightsUtil::get_location_parent_ids_recursive()
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
     * @return string
     */
    public function getRightsLocationClassName(): string
    {
        return $this->rightsLocationClassName;
    }

    /**
     * @param string $rightsLocationClassName
     */
    public function setRightsLocationClassName(string $rightsLocationClassName): void
    {
        $this->rightsLocationClassName = $rightsLocationClassName;
    }

    /**
     * @return string
     */
    public function getRightsLocationEntityRightClassName(): string
    {
        return $this->rightsLocationEntityRightClassName;
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     */
    public function setRightsLocationEntityRightClassName(string $rightsLocationEntityRightClassName): void
    {
        $this->rightsLocationEntityRightClassName = $rightsLocationEntityRightClassName;
    }

    /**
     * @return \Chamilo\Core\Rights\Domain\RightsLocationEntityRight
     */
    abstract public function getRightsLocationEntityRightInstance();

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
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return \Chamilo\Core\Rights\Domain\RightsLocation|boolean
     * @see RightsUtil::get_root()
     */
    public function getRootLocation(int $treeType = self::TREE_TYPE_ROOT, int $treeIdentifier = 0)
    {
        $rootLocation = $this->getRightsRepository()->findRootLocation($treeType, $treeIdentifier);

        if (!$rootLocation instanceof RightsLocation)
        {
            return false;
        }

        return $rootLocation;
    }

    /**
     * @param integer $treeType
     * @param integer $treeIdentifier
     *
     * @return integer
     * @see RightsUtil::get_root_id()
     */
    public function getRootLocationIdentifier($treeType = self::TREE_TYPE_ROOT, $treeIdentifier = 0)
    {
        $rootLocation = $this->getRootLocation($treeType, $treeIdentifier);

        if ($rootLocation instanceof RightsLocation)
        {
            return $rootLocation->getId();
        }
        else
        {
            return false;
        }
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
     * @return integer[][]
     * @see RightsUtil::get_target_entities()
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
     * @return integer[][]
     * @see RightsUtil::get_target_entities_for_location()
     * @see DataManager:: retrieve_target_entities_array()
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
     * Returns whether given location or any of its ancestors is in array $location_ids_with_granted_right.
     *
     * @param integer $locationIdentifier location we check whether user has access rigth to.
     * @param integer[] $locationParentIdentifiers mapping of child location ID's onto parent location ID's.
     * @param integer[] $locationIdentifiersWithGrantedRight All location ID's which user has access rigth to. Keys:
     *                                                       location ID's Values: True.
     *
     * @see RightsService::getLocationParentIdentifiersRecursive()
     * @see RightsService::filterLocationIdentifiersByGrantedRight()
     * @see RightsUtil::has_right_recursive()
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

    public function invertLocationEntityRight($right, $entityIdentifier, $entityType, $locationIdentifier)
    {
        if (!is_null($entityIdentifier) && !is_null($entityType) && !empty($right) && !empty($locationIdentifier))
        {
            $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                $right, $entityIdentifier, $entityType, $locationIdentifier
            );

            if ($locationEntityRight instanceof RightsLocationEntityRight)
            {
                return $this->deleteRightsLocationEntityRight($locationEntityRight);
            }
            else
            {
                return $this->createRightsLocationEntityRightFromParameters(
                    $right, $entityIdentifier, $entityType, $locationIdentifier
                );
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Enables a right for a specific entity on a specific location
     *
     * @param integer $right
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return boolean
     * @see RightsUtil::set_location_entity_right()
     */
    public function setRightsLocationEntityRight($right, $entityIdentifier, $entityType, $locationIdentifier)
    {
        if (!is_null($entityIdentifier) && !is_null($entityType) && !empty($right) && !empty($locationIdentifier))
        {
            $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                $right, $entityIdentifier, $entityType, $locationIdentifier
            );

            if ($locationEntityRight instanceof RightsLocationEntityRight)
            {
                return true;
            }
            else
            {
                return $this->createRightsLocationEntityRightFromParameters(
                    $right, $entityIdentifier, $entityType, $locationIdentifier
                );
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * @param integer $right
     * @param integer $entityIdentifier
     * @param integer $entityType
     * @param integer $locationIdentifier
     *
     * @return boolean
     * @see RightsUtil::unset_location_entity_right()
     */
    public function unsetRightsLocationEntityRight(
        int $right, int $entityIdentifier, int $entityType, int $locationIdentifier
    )
    {
        if (!is_null($entityIdentifier) && !is_null($entityType) && !empty($right) && !empty($locationIdentifier))
        {
            $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                $right, $entityIdentifier, $entityType, $locationIdentifier
            );

            if ($locationEntityRight instanceof RightsLocationEntityRight)
            {
                return $this->deleteRightsLocationEntityRight($locationEntityRight);
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }
}