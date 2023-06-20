<?php
namespace Chamilo\Libraries\Rights\Service;

use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Rights\Domain\RightsLocation;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight;
use Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Libraries\Rights\Form\RightsForm;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Rights\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService
{
    public const TREE_TYPE_ROOT = 0;
    public const TYPE_ROOT = 0;

    /**
     * @var int[]
     */
    private array $entityRightsCache = [];

    /**
     * @var int[]
     */
    private array $locationIdentifiersCache = [];

    private string $rightsLocationClassName;

    private string $rightsLocationEntityRightClassName;

    private RightsRepository $rightsRepository;

    private Translator $translator;

    private UserService $userService;

    public function __construct(RightsRepository $rightsRepository, UserService $userService, Translator $translator)
    {
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int[] $rights
     * @param int[] $types
     *
     * @see RightsUtil::count_location_overview_with_rights_granted()
     */
    protected function countLocationOverviewWithGrantedRights(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities, array $rights = [], array $types = [], ?int $treeType = null, ?int $treeIdentifier = null
    ): int
    {
        return $this->getRightsRepository()->countLocationOverviewWithGrantedRights(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $userIdentifier, $entities, $rights, $types,
            $treeType, $treeIdentifier
        );
    }

    public function createRightsLocation(RightsLocation $location): bool
    {
        return $this->getRightsRepository()->createRightsLocation($location);
    }

    protected function createRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight): bool
    {
        return $this->getRightsRepository()->createRightsLocationEntityRight($rightsLocationEntityRight);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     *
     * @see RightsUtil::create_rights_location_entity_right()
     */
    protected function createRightsLocationEntityRightFromParameters(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): bool
    {
        $rightsLocationEntityRight = new $rightsLocationEntityRightClassName();

        $rightsLocationEntityRight->set_location_id($locationIdentifier);
        $rightsLocationEntityRight->set_right_id($right);
        $rightsLocationEntityRight->set_entity_id($entityIdentifier);
        $rightsLocationEntityRight->set_entity_type($entityType);

        return $this->createRightsLocationEntityRight($rightsLocationEntityRight);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocation> $rightsLocationClassName
     *
     * @see RightsUtil::create_location()
     */
    public function createRightsLocationFromParameters(
        string $rightsLocationClassName, int $type = self::TYPE_ROOT, string $identifier = '0', int $inherit = 0,
        string $parent = '0', int $locked = 0, string $treeIdentifier = '0', int $treeType = self::TREE_TYPE_ROOT
    ): bool
    {
        $location = new $rightsLocationClassName();

        $location->setParentId($parent);
        $location->setType($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($treeIdentifier);
        $location->set_tree_type($treeType);

        return $this->createRightsLocation($location);
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocation> $rightsLocationClassName
     *
     * @see RightsUtil::create_subtree_root_location()
     */
    public function createSubtreeRootLocation(string $rightsLocationClassName, string $treeIdentifier, int $treeType
    ): bool
    {
        return $this->createRightsLocationFromParameters(
            $rightsLocationClassName, self::TYPE_ROOT, '0', 0, '0', 0, $treeIdentifier, $treeType
        );
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     * @param int[][] $values
     */
    protected function deleteAndCreateRightsLocationEntityRightsForRightsLocationAndUserFromValues(
        string $rightsLocationEntityRightClassName, RightsLocation $location, User $user, array $values
    ): bool
    {
        if (!$this->deleteRightsLocationEntityRightsForLocation($rightsLocationEntityRightClassName, $location))
        {
            return false;
        }

        $success = true;

        foreach ($values[RightsForm::PROPERTY_RIGHT_OPTION] as $rightIdentifier => $rightsOption)
        {
            switch ($rightsOption)
            {
                case RightsForm::RIGHT_OPTION_ALL :
                    $success &= $this->createRightsLocationEntityRightFromParameters(
                        $rightsLocationEntityRightClassName, $rightIdentifier, '0', 0, $location->getId()
                    );
                    break;
                case RightsForm::RIGHT_OPTION_ME :
                    $success &= $this->createRightsLocationEntityRightFromParameters(
                        $rightsLocationEntityRightClassName, $rightIdentifier, $user->getId(),
                        UserEntityProvider::ENTITY_TYPE, $location->getId()
                    );
                    break;
                case RightsForm::RIGHT_OPTION_SELECT :

                    foreach (
                        $values[RightsForm::PROPERTY_TARGETS][$rightIdentifier] as $entityType => $entityIdentifiers
                    )
                    {
                        foreach ($entityIdentifiers as $entityIdentifier)
                        {
                            $success &= $this->createRightsLocationEntityRightFromParameters(
                                $rightsLocationEntityRightClassName, $rightIdentifier, $entityIdentifier, $entityType,
                                $location->getId()
                            );
                        }
                    }
            }
        }

        return $success;
    }

    public function deleteRightsLocation(string $rightsLocationEntityRightClassName, RightsLocation $rightsLocation
    ): bool
    {
        if (!$this->getRightsRepository()->deleteRightsLocation($rightsLocationEntityRightClassName, $rightsLocation))
        {
            return false;
        }

        return $this->deleteRightsLocationEntityRightsForLocation($rightsLocationEntityRightClassName, $rightsLocation);
    }

    public function deleteRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRight($rightsLocationEntityRight);
    }

    /**
     * @see RightsLocation::clear_rights()
     */
    protected function deleteRightsLocationEntityRightsForLocation(
        string $rightsLocationEntityRightClassName, RightsLocation $location
    ): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $rightsLocationEntityRightClassName, $location
        );
    }

    /**
     * Helper function to delete all the location entity right records for a given entity on a given location
     *
     * @see RightsUtil::delete_location_entity_right_for_entity()
     */
    protected function deleteRightsLocationEntityRightsForLocationAndEntityParameters(
        string $rightsLocationEntityRightClassName, RightsLocation $location, string $entityIdentifier, int $entityType
    ): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $rightsLocationEntityRightClassName, $location, $entityIdentifier, $entityType
        );
    }

    /**
     * @see RightsLocation::clear_right()
     */
    public function deleteRightsLocationEntityRightsForLocationAndRight(
        string $rightsLocationEntityRightClassName, RightsLocation $location, int $right
    ): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $rightsLocationEntityRightClassName, $location, null, null, $right
        );
    }

    /**
     * @see RightsUtil::is_allowed_for_rights_entity_item()
     */
    protected function doesEntityHaveRightForLocation(
        string $rightsLocationEntityRightClassName, string $entityIdentifier, int $entityType, int $right,
        RightsLocation $location
    ): bool
    {
        $grantedRights = $this->findGrantedRightsForEntityAndLocation(
            $rightsLocationEntityRightClassName, $entityIdentifier, $entityType, $location
        );

        return in_array($right, $grantedRights);
    }

    /**
     * @see RightsUtil::is_allowed_for_rights_entity_item_no_inherit()
     */
    protected function doesEntityHaveRightForLocationWithoutInheritance(
        string $rightsLocationEntityRightClassName, string $entityIdentifier, int $entityType, int $right,
        RightsLocation $location
    ): bool
    {
        $grantedRight = $this->findRightsLocationEntityRightByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $location->getId()
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
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @see RightsUtil::is_allowed()
     */
    public function doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier, int $right,
        array $entities, string $identifier = '0', int $type = self::TYPE_ROOT, string $treeIdentifier = '0',
        int $treeType = self::TREE_TYPE_ROOT
    ): bool
    {
        $user = $this->getUserService()->findUserByIdentifier($userIdentifier);

        if ($user->isPlatformAdmin())
        {
            return true;
        }

        $location = $this->getRightsRepository()->findRightsLocationByParameters(
            $rightsLocationClassName, $identifier, $type, $treeIdentifier, $treeType
        );

        if (!$location instanceof RightsLocation)
        {
            throw new RightsLocationNotFoundException(
                $this->getTranslator()->trans(
                    'NoLocationFound', [
                    '{TYPE}' => $type,
                    '{IDENTIFIER}' => $identifier,
                    '{TREE_IDENTIFIER}' => $treeIdentifier,
                    '{TREE_TYPE}' => $treeType
                ], 'Chamilo\Libraries\Rights'
                )
            );
        }

        return $this->doesUserIdentifierHaveRightForLocationAndEntities(
            $rightsLocationEntityRightClassName, $userIdentifier, $right, $location, $entities
        );
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @see RightsUtil::is_allowed_on_location()
     */
    protected function doesUserIdentifierHaveRightForLocationAndEntities(
        string $rightsLocationEntityRightClassName, string $userIdentifier, int $right, RightsLocation $location,
        array $entities
    ): bool
    {
        $grantedRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
            $rightsLocationEntityRightClassName, $userIdentifier, $location, $entities
        );

        return in_array($right, $grantedRights);
    }

    /**
     * Filters given identifiers and returns those which the given user has access rights to.
     * Why this function?: This function is an accelerated version of is_allowed(...) when called many times after each
     * other. The number of database queries is minimized by processing identifiers all at once.
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
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param string[] $identifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function filterLocationIdentifiersByGrantedRight(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, User $user, array $entities,
        int $right, array $identifiers, int $type
    ): array
    {
        if ($user->isPlatformAdmin())
        {
            return $identifiers;
        }

        $locationIdentifiers =
            $this->findRightsLocationIdentifiersByIdentifiersAndType($rightsLocationClassName, $identifiers, $type);
        $locationParentIdentifiers =
            $this->getLocationParentIdentifiersRecursive($rightsLocationClassName, $locationIdentifiers);

        $allLocationIdentifiers =
            array_merge(array_values($locationIdentifiers), array_values($locationParentIdentifiers));
        $allLocationIdentifiersWithGrantedRight = $this->getLocationIdentifiersByGrantedRight(
            $rightsLocationEntityRightClassName, $user->getId(), $entities, $right, $allLocationIdentifiers
        );

        $identifiersWithGrantedRight = [];

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
     * @return int[]
     * @see RightsUtil::get_granted_rights_for_rights_entity_item()
     */
    protected function findGrantedRightsForEntityAndLocation(
        string $rightsLocationEntityRightClassName, string $entityIdentifier, int $entityType, RightsLocation $location
    ): array
    {
        $cacheKey = md5(serialize([get_class($location), $entityIdentifier, $entityType, $location->getId()]));

        if (is_null($this->entityRightsCache[$cacheKey]))
        {
            $grantedRights = $this->getRightsRepository()->findGrantedRightsForEntityAndLocation(
                $rightsLocationEntityRightClassName, $entityIdentifier, $entityType, $location
            );

            if ($location->inherits())
            {
                $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier(
                    get_class($location), $location->getParentId()
                );

                if ($parentLocation instanceof RightsLocation)
                {
                    $parentRights = $this->findGrantedRightsForEntityAndLocation(
                        $rightsLocationEntityRightClassName, $entityIdentifier, $entityType, $parentLocation
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
     * @return int[]
     * @see DataManager::retrieve_granted_rights_array()
     */
    protected function findGrantedRightsForLocationAndCondition(
        string $rightsLocationEntityRightClassName, RightsLocation $location, Condition $condition
    ): array
    {
        $grantedRights = $this->getRightsRepository()->findGrantedRightsForLocationAndCondition(
            $rightsLocationEntityRightClassName, $location, $condition
        );

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier(
                get_class($location), $location->getParentId()
            );

            if ($parentLocation instanceof RightsLocation)
            {
                $parentRights = $this->findGrantedRightsForLocationAndCondition(
                    $rightsLocationEntityRightClassName, $parentLocation, $condition
                );
                $grantedRights = array_merge($grantedRights, $parentRights);
            }
        }

        return array_unique($grantedRights);
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return int[]
     * @see DataManager::retrieve_granted_rights_array()
     */
    protected function findGrantedRightsForUserIdentifierLocationAndEntities(
        string $rightsLocationEntityRightClassName, string $userIdentifier, RightsLocation $location, array $entities
    ): array
    {
        $grantedRights = $this->getRightsRepository()->findGrantedRightsForUserIdentifierLocationAndEntities(
            $rightsLocationEntityRightClassName, $userIdentifier, $location, $entities
        );

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier(
                get_class($location), $location->getParentId()
            );

            if ($parentLocation instanceof RightsLocation)
            {
                $parentRights = $this->findGrantedRightsForUserIdentifierLocationAndEntities(
                    $rightsLocationEntityRightClassName, $userIdentifier, $parentLocation, $entities
                );
                $grantedRights = array_merge($grantedRights, $parentRights);
            }
        }

        return array_unique($grantedRights);
    }

    /**
     * @param string $rightsLocationClassName
     * @param string[] $locationIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findLocationParentIdentifierRecordsForLocationIdentifiers(
        string $rightsLocationClassName, array $locationIdentifiers
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findLocationParentIdentifierRecordsForLocationIdentifiers(
            $rightsLocationClassName, $locationIdentifiers
        );
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int[] $rights
     * @param int[] $types
     * @param ?int $treeType
     * @param ?string $treeIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findLocationsWithGrantedRights(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities, array $rights = [], array $types = [], ?int $treeType = null, ?string $treeIdentifier = null
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findLocationsWithGrantedRights(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $userIdentifier, $entities, $rights, $types,
            $treeType, $treeIdentifier
        );
    }

    public function findRightsLocationByParameters(
        string $rightsLocationClassName, string $identifier = '0', int $type = RightsService::TYPE_ROOT,
        string $treeIdentifier = '0', int $treeType = RightsService::TREE_TYPE_ROOT
    ): ?RightsLocation
    {
        return $this->getRightsRepository()->findRightsLocationByParameters(
            $rightsLocationClassName, $identifier, $type, $treeIdentifier, $treeType
        );
    }

    public function findRightsLocationEntityRightByParameters(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): ?RightsLocationEntityRight
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param int $right
     * @param string[] $entityIdentifiers
     * @param int $entityType
     * @param string $locationIdentifier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightsByParameters(
        string $rightsLocationEntityRightClassName, int $right, array $entityIdentifiers, int $entityType,
        string $locationIdentifier
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightsByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifiers, $entityType, $locationIdentifier
        );
    }

    /**
     * @param string $rightsLocationClassName
     * @param string[] $identifiers
     * @param int $type
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see DataManager::retrieve_location_ids_by_identifiers()
     */
    protected function findRightsLocationIdentifiersByIdentifiersAndType(
        string $rightsLocationClassName, array $identifiers, int $type
    ): array
    {
        $locations =
            $this->findRightsLocationRecordsByIdentifiersAndType($rightsLocationClassName, $identifiers, $type);

        $locationIdentifiers = [];

        foreach ($locations as $location)
        {
            $locationIdentifiers[$location[RightsLocation::PROPERTY_IDENTIFIER]] = $location[DataClass::PROPERTY_ID];
        }

        return $locationIdentifiers;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @return string[]
     * @see RightsUtil::get_identifiers_with_right_granted()
     */
    public function findRightsLocationIdentifiersWithGrantedRight(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, int $right,
        RightsLocation $parentLocation, int $type, string $userIdentifier, array $entities
    ): array
    {
        $cacheKey = md5(serialize([get_class($parentLocation), $userIdentifier, $right, $parentLocation->getId()]));

        if (is_null($this->locationIdentifiersCache[$cacheKey]))
        {
            $parentHasRight = $this->doesUserIdentifierHaveRightForLocationAndEntities(
                $rightsLocationEntityRightClassName, $userIdentifier, $right, $parentLocation, $entities
            );

            $this->locationIdentifiersCache[$cacheKey] =
                $this->getRightsRepository()->findRightsLocationIdentifiersWithGrantedRight(
                    $rightsLocationClassName, $rightsLocationEntityRightClassName, $right, $parentLocation, $type,
                    $userIdentifier, $entities, $parentHasRight
                );
        }

        return $this->locationIdentifiersCache[$cacheKey];
    }

    /**
     * @param string $rightsLocationClassName
     * @param string[] $identifiers
     * @param int $type
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<string[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findRightsLocationRecordsByIdentifiersAndType(
        string $rightsLocationClassName, array $identifiers, int $type
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationRecordsByIdentifiersAndType(
            $rightsLocationClassName, $identifiers, $type
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param ?int $right
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findRightsLocationRightsEntitiesForLocationAndRight(
        string $rightsLocationEntityRightClassName, RightsLocation $location, ?int $right = null
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationRightsForLocationIdentifierAndRights(
            $rightsLocationEntityRightClassName, $location->getId(), [$right]
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param string $locationIdentifier
     * @param int[] $rights
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findRightsLocationRightsForLocationIdentifierAndRights(
        string $rightsLocationEntityRightClassName, string $locationIdentifier, array $rights
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationRightsForLocationIdentifierAndRights(
            $rightsLocationEntityRightClassName, $locationIdentifier, $rights
        );
    }

    /**
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int $right
     * @param string[] $locationIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see DataMananager::filter_location_identifiers_by_granted_right()
     */
    protected function getLocationIdentifiersByGrantedRight(
        string $rightsLocationEntityRightClassName, string $userIdentifier, array $entities, int $right,
        array $locationIdentifiers
    ): array
    {
        $locationEntityRights = $this->getRightsRepository()->findLocationEntityRightRecordsByGrantedRight(
            $rightsLocationEntityRightClassName, $userIdentifier, $entities, $right, $locationIdentifiers
        );

        $locationIdentifiers = [];

        foreach ($locationEntityRights as $locationEntityRight)
        {
            $locationIdentifiers[$locationEntityRight[RightsLocationEntityRight::PROPERTY_LOCATION_ID]] = '1';
        }

        return $locationIdentifiers;
    }

    /**
     * @param string $rightsLocationClassName
     * @param string $rightsLocationEntityRightClassName
     * @param string $userIdentifier
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     * @param int[] $rights
     * @param int[] $types
     * @param ?int $treeType
     * @param ?string $treeIdentifier
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see RightsUtil::get_location_overview_with_rights_granted()
     */
    protected function getLocationOverviewWithGrantedRights(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, string $userIdentifier,
        array $entities, array $rights = [], array $types = [], ?int $treeType = null, ?string $treeIdentifier = null
    ): array
    {
        $locations = $this->findLocationsWithGrantedRights(
            $rightsLocationClassName, $rightsLocationEntityRightClassName, $userIdentifier, $entities, $rights, $types,
            $treeType, $treeIdentifier
        );

        $overview = [];
        foreach ($locations as $location)
        {
            $overview[$location[RightsLocation::PROPERTY_TYPE]][] = $location[RightsLocation::PROPERTY_IDENTIFIER];
        }

        return $overview;
    }

    /**
     * @param string[] $locationIdentifiers
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see DataManager::retrieve_location_parent_ids()
     */
    protected function getLocationParentIdentifiersForLocationIdentifiers(
        string $rightsLocationClassName, array $locationIdentifiers
    ): array
    {
        $locations = $this->findLocationParentIdentifierRecordsForLocationIdentifiers(
            $rightsLocationClassName, $locationIdentifiers
        );

        $locationParentIdentifiers = [];

        foreach ($locations as $location)
        {
            $locationParentIdentifiers[$location[DataClass::PROPERTY_ID]] = $location[NestedSet::PROPERTY_PARENT_ID];
        }

        return $locationParentIdentifiers;
    }

    /**
     * Returns an array mapping child location ID's onto parent location ID's.
     * Idea:
     * Retrieve the child-parent relation of location with as few queries as possible and store them in the memory. The
     * function has_right_recursive(...) will loop over the child-parent tree, which is much faster than the recursive
     * function calls to DataManager::retrieve_granted_rights_array(...). This function actually retrieves the
     * location tree level-by-level starting with the leaf level, followed by parent level, then grandparents until an
     * empty level is found. Result is a flat array mapping each ID in $location_ids onto its parent ID and each parent
     * onto its grand parent D, etc. Result will only contain child location ID's if the 'inherit' property of the
     * location is true and the parent is not null.
     *
     * @param string[] $locationIdentifiers
     *
     * @return string[] Keys: child location ID's Values: parent location ID's.
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see RightsUtil::get_location_parent_ids_recursive()
     */
    protected function getLocationParentIdentifiersRecursive(string $rightsLocationClassName, array $locationIdentifiers
    ): array
    {
        $allLocationParentIdentifiers = [];

        $locationParentIdentifiers = $locationIdentifiers;

        while (true)
        {
            $locationParentIdentifiers = $this->getLocationParentIdentifiersForLocationIdentifiers(
                $rightsLocationClassName, $locationParentIdentifiers
            );

            if (count($locationParentIdentifiers) == 0)
            {
                break;
            }

            $allLocationParentIdentifiers = $allLocationParentIdentifiers + $locationParentIdentifiers;
        }

        return $allLocationParentIdentifiers;
    }

    protected function getRightsLocationIdentifierByParameters(
        string $rightsLocationClassName, string $identifier = '0', int $type = RightsService::TYPE_ROOT,
        string $treeIdentifier = '0', int $treeType = RightsService::TREE_TYPE_ROOT
    ): string
    {
        $location = $this->findRightsLocationByParameters(
            $rightsLocationClassName, $identifier, $type, $treeIdentifier, $treeType
        );

        if ($location instanceof RightsLocation)
        {
            return $location->getId();
        }
        else
        {
            return '0';
        }
    }

    protected function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @see RightsUtil::get_root()
     */
    public function getRootLocation(
        string $rightsLocationClassName, int $treeType = self::TREE_TYPE_ROOT, string $treeIdentifier = '0'
    ): ?RightsLocation
    {
        $rootLocation =
            $this->getRightsRepository()->findRootLocation($rightsLocationClassName, $treeType, $treeIdentifier);

        if (!$rootLocation instanceof RightsLocation)
        {
            return null;
        }

        return $rootLocation;
    }

    /**
     * @see RightsUtil::get_root_id()
     */
    public function getRootLocationIdentifier(
        string $rightsLocationClassName, int $treeType = self::TREE_TYPE_ROOT, string $treeIdentifier = '0'
    ): ?string
    {
        $rootLocation = $this->getRootLocation($rightsLocationClassName, $treeType, $treeIdentifier);

        if (!$rootLocation instanceof RightsLocation)
        {
            return $rootLocation->getId();
        }
        else
        {
            return null;
        }
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see RightsUtil::get_target_entities()
     */
    public function getTargetEntities(
        string $rightsLocationClassName, string $rightsLocationEntityRightClassName, int $right,
        string $identifier = '0', int $type = self::TYPE_ROOT, string $treeIdentifier = '0',
        int $treeType = self::TREE_TYPE_ROOT
    ): array
    {
        $location = $this->findRightsLocationByParameters(
            $rightsLocationClassName, $identifier, $type, $treeIdentifier, $treeType
        );

        if (!$location)
        {
            throw new RightsLocationNotFoundException(
                $this->getTranslator()->trans(
                    'NoLocationFound', [
                    '{TYPE}' => $type,
                    '{IDENTIFIER}' => $identifier,
                    '{TREE_IDENTIFIER}' => $treeIdentifier,
                    '{TREE_TYPE}' => $treeType
                ], 'Chamilo\Libraries\Rights'
                )
            );
        }

        return $this->getTargetEntitiesForLocation($rightsLocationEntityRightClassName, $right, $location);
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @see RightsUtil::get_target_entities_for_location()
     * @see DataManager:: retrieve_target_entities_array()
     */
    protected function getTargetEntitiesForLocation(
        string $rightsLocationEntityRightClassName, int $right, RightsLocation $location
    ): array
    {
        $entityRecords = $this->getRightsRepository()->findRightsEntityRecordsForRightAndLocation(
            $rightsLocationEntityRightClassName, $right, $location
        );

        $targetEntities = [];

        foreach ($entityRecords as $entityRecord)
        {
            $targetEntities[$entityRecord[RightsLocationEntityRight::PROPERTY_ENTITY_TYPE]][] =
                $entityRecord[RightsLocationEntityRight::PROPERTY_ENTITY_ID];
        }

        if ($location->inherits())
        {
            $parentLocation = $this->getRightsRepository()->findRightsLocationByIdentifier(
                get_class($location), $location->getParentId()
            );
            $parentEntities =
                $this->getTargetEntitiesForLocation($rightsLocationEntityRightClassName, $right, $parentLocation);

            foreach ($parentEntities as $type => $id_array)
            {
                if ($targetEntities[$type])
                {
                    $targetEntities[$type] = array_merge($id_array, $targetEntities[$type]);
                }
                else
                {
                    $targetEntities[$type] = $id_array;
                }
            }
        }

        return $targetEntities;
    }

    /**
     * @param int[] $rights
     *
     * @return string[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetEntitiesForRightsAndLocation(
        string $rightsLocationEntityRightClassName, array $rights, RightsLocation $location
    ): array
    {
        $rightsTargetEntities = [];

        foreach ($rights as $right)
        {
            $rightsTargetEntities[$right] =
                $this->getTargetEntitiesForLocation($rightsLocationEntityRightClassName, $right, $location);
        }

        return $rightsTargetEntities;
    }

    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * Returns whether given location or any of its ancestors is in array $location_ids_with_granted_right.
     *
     * @param string $locationIdentifier                     location we check whether user has access rigth to.
     * @param string[] $locationParentIdentifiers            mapping of child location ID's onto parent location ID's.
     * @param string[] $locationIdentifiersWithGrantedRight  All location ID's which user has access rigth to. Keys:
     *                                                       location ID's Values: True.
     *
     * @see RightsService::filterLocationIdentifiersByGrantedRight()
     * @see RightsUtil::has_right_recursive()
     * @see RightsService::getLocationParentIdentifiersRecursive()
     */
    private function hasRightRecursive(
        string $locationIdentifier, array $locationParentIdentifiers, array $locationIdentifiersWithGrantedRight
    ): bool
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
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocation> $rightsLocationClassName
     *
     * @see RightsUtil::create_location()
     */
    public function initializeRightsLocationFromParameters(
        string $rightsLocationClassName, int $type = self::TYPE_ROOT, string $identifier = '0', int $inherit = 0,
        string $parent = '0', int $locked = 0, string $treeIdentifier = '0', int $treeType = self::TREE_TYPE_ROOT
    ): RightsLocation
    {
        $location = new $rightsLocationClassName();

        $location->setParentId($parent);
        $location->setType($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($treeIdentifier);
        $location->set_tree_type($treeType);

        return $location;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     */
    public function invertLocationEntityRight(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): bool
    {
        $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
        );

        if ($locationEntityRight instanceof RightsLocationEntityRight)
        {
            return $this->deleteRightsLocationEntityRight($locationEntityRight);
        }
        else
        {
            return $this->createRightsLocationEntityRightFromParameters(
                $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
            );
        }
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     * @param int[][] $values
     */
    public function saveRightsConfigurationForRightsLocationAndUserFromValues(
        string $rightsLocationEntityRightClassName, RightsLocation $location, User $user, array $values
    ): bool
    {
        if (!$this->saveRightsLocationInheritanceForRightsLocationFromValues($location, $values))
        {
            return false;
        }

        if (!$this->deleteAndCreateRightsLocationEntityRightsForRightsLocationAndUserFromValues(
            $rightsLocationEntityRightClassName, $location, $user, $values
        ))
        {
            return false;
        }

        return true;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation[] $locations
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $values
     *
     * @return bool
     */
    protected function saveRightsConfigurationForRightsLocationsAndUserFromValues(
        string $rightsLocationEntityRightClassName, array $locations, User $user, array $values
    ): bool
    {
        foreach ($locations as $location)
        {
            if (!$this->saveRightsConfigurationForRightsLocationAndUserFromValues(
                $rightsLocationEntityRightClassName, $location, $user, $values
            ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocation $location
     * @param int[][] $values
     *
     * @return bool
     */
    protected function saveRightsLocationInheritanceForRightsLocationFromValues(RightsLocation $location, array $values
    ): bool
    {
        $inheritanceValue =
            array_key_exists(RightsForm::PROPERTY_INHERIT, $values) ? $values[RightsForm::PROPERTY_INHERIT] :
                RightsForm::INHERIT_FALSE;

        if ($inheritanceValue == RightsForm::INHERIT_TRUE && !$location->inherits())
        {
            $location->inherit();

            return $this->updateRightsLocation($location);
        }
        elseif ($location->inherits())
        {
            $location->disinherit();

            return $this->updateRightsLocation($location);
        }

        return true;
    }

    protected function setRightsLocationClassName(string $rightsLocationClassName): void
    {
        $this->rightsLocationClassName = $rightsLocationClassName;
    }

    /**
     * Enables a right for a specific entity on a specific location
     *
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     *
     * @see RightsUtil::set_location_entity_right()
     */
    public function setRightsLocationEntityRight(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): bool
    {
        $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
        );

        if ($locationEntityRight instanceof RightsLocationEntityRight)
        {
            return true;
        }
        else
        {
            return $this->createRightsLocationEntityRightFromParameters(
                $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
            );
        }
    }

    protected function setRightsLocationEntityRightClassName(string $rightsLocationEntityRightClassName): void
    {
        $this->rightsLocationEntityRightClassName = $rightsLocationEntityRightClassName;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight> $rightsLocationEntityRightClassName
     * @param int[][] $values
     */
    public function setRightsLocationEntityRightsForRightsLocationAndUserFromValues(
        string $rightsLocationEntityRightClassName, RightsLocation $location, User $user, array $values
    ): bool
    {
        $success = true;

        foreach ($values[RightsForm::PROPERTY_RIGHT_OPTION] as $rightIdentifier => $rightsOption)
        {
            switch ($rightsOption)
            {
                case RightsForm::RIGHT_OPTION_ALL :
                    $success &= $this->setRightsLocationEntityRight(
                        $rightsLocationEntityRightClassName, $rightIdentifier, '0', 0, $location->getId()
                    );
                    break;
                case RightsForm::RIGHT_OPTION_ME :
                    $success &= $this->setRightsLocationEntityRight(
                        $rightsLocationEntityRightClassName, $rightIdentifier, $user->getId(),
                        UserEntityProvider::ENTITY_TYPE, $location->getId()
                    );
                    break;
                case RightsForm::RIGHT_OPTION_SELECT :

                    foreach (
                        $values[RightsForm::PROPERTY_TARGETS][$rightIdentifier] as $entityType => $entityIdentifiers
                    )
                    {
                        foreach ($entityIdentifiers as $entityIdentifier)
                        {
                            $success &= $this->setRightsLocationEntityRight(
                                $rightsLocationEntityRightClassName, $rightIdentifier, $entityIdentifier, $entityType,
                                $location->getId()
                            );
                        }
                    }
            }
        }

        return $success;
    }

    /**
     * @see RightsUtil::unset_location_entity_right()
     */
    protected function unsetRightsLocationEntityRight(
        string $rightsLocationEntityRightClassName, int $right, string $entityIdentifier, int $entityType,
        string $locationIdentifier
    ): bool
    {
        $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
            $rightsLocationEntityRightClassName, $right, $entityIdentifier, $entityType, $locationIdentifier
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

    public function updateRightsLocation(RightsLocation $location): bool
    {
        return $this->getRightsRepository()->updateRightsLocation($location);
    }
}