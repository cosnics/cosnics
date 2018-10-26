<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Portfolio\Storage\Repository\RightsRepository;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService
{
    const VIEW_RIGHT = 1;
    const VIEW_FEEDBACK_RIGHT = 2;
    const GIVE_FEEDBACK_RIGHT = 3;
    const EDIT_RIGHT = 4;

    /**
     *
     * @var Rights
     */
    private static $instance;

    /**
     *
     * @var boolean[]
     */
    private $entities_condition_cache;

    /**
     *
     * @var int[]
     */
    private $granted_rights_cache;

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\RightsRepository
     */
    private $rightsRepository;

    /**
     *
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(RightsRepository $rightsRepository, UserService $userService, Translator $translator)
    {
        $this->userService = $userService;
        $this->rightsRepository = $rightsRepository;
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\RightsRepository
     */
    public function getRightsRepository()
    {
        return $this->rightsRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\RightsRepository $rightsRepository
     */
    public function setRightsRepository(RightsRepository $rightsRepository)
    {
        $this->rightsRepository = $rightsRepository;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return string[]
     */
    public function getAvailableRights()
    {
        $translator = $this->getTranslator();

        return array(
            $translator->trans('ViewRight', [], 'Chamilo\Application\Portfolio') => self::VIEW_RIGHT,
            $translator->trans('ViewFeedbackRight', [], 'Chamilo\Application\Portfolio') => self::VIEW_FEEDBACK_RIGHT,
            $translator->trans('GiveFeedbackRight', [], 'Chamilo\Application\Portfolio') => self::GIVE_FEEDBACK_RIGHT,
            $translator->trans('EditRight', [], 'Chamilo\Application\Portfolio') => self::EDIT_RIGHT);
    }

    /**
     *
     * @param integer $right
     * @param integer $entityId
     * @param integer $entityType
     * @param string $locationId
     * @param int $publicationId
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight
     */
    public function findRightsLocationEntityRight($right, $entityId, $entityType, $locationId, $publicationId)
    {
        return $this->getRightsRepository()->findRightsLocationEntityRight(
            $right,
            $entityId,
            $entityType,
            $locationId,
            $publicationId);
    }

    /**
     *
     * @todo DataClass executes additional business logic when deleting an instance, needs to be move or reimplemented
     *       before using the non-dataclass-based delete method(s)
     * @param RightsLocationEntityRight $rightsLocationEntityRight
     * @return boolean
     */
    public function deleteRightsLocationEntityRight(RightsLocationEntityRight $rightsLocationEntityRight)
    {
        return $rightsLocationEntityRight->delete();
    }

    /**
     *
     * @param int $right
     * @param int $entityId
     * @param int $entityType
     * @param string $locationId
     * @param int $publicationId
     * @return boolean
     */
    public function invertLocationEntityRight($right, $entityId, $entityType, $locationId, $publicationId)
    {
        if (! is_null($entityId) && ! is_null($entityType) && ! empty($right) && ! empty($locationId) &&
             ! empty($publicationId))
        {
            $locationEntityRight = $this->findRightsLocationEntityRight(
                $right,
                $entityId,
                $entityType,
                $locationId,
                $publicationId);

            if ($locationEntityRight)
            {
                return $this->deleteRightsLocationEntityRight($locationEntityRight);
            }
            else
            {
                DataClassCache::truncate(RightsLocationEntityRight::class_name());
                return $this->createRightsLocationEntityRightFromParameters(
                    $right,
                    $entityId,
                    $entityType,
                    $locationId,
                    $publicationId);
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Helper function to create a rights location entity right
     *
     * @param int $right
     * @param int $entityId
     * @param int $entityType
     * @param int $locationId
     * @return boolean
     */
    private function createRightsLocationEntityRightFromParameters($right, $entityId, $entityType, $locationId,
        $publicationId)
    {
        $locationEntityRight = new RightsLocationEntityRight();

        $locationEntityRight->set_location_id($locationId);
        $locationEntityRight->set_publication_id($publicationId);
        $locationEntityRight->set_right_id($right);
        $locationEntityRight->set_entity_id($entityId);
        $locationEntityRight->set_entity_type($entityType);

        return $this->createRightsLocationEntityRight($locationEntityRight);
    }

    /**
     *
     * @param int $right
     * @param RightsLocation $location
     * @param int $user_id
     * @return boolean
     */
    public function is_allowed($right, $location, $userIdentifier)
    {
        $userIdentifier = $userIdentifier ? $userIdentifier : Session::get_user_id();

        $user = $this->getUserService()->findUserByIdentifier((int) $userIdentifier);

        if ($user->is_platform_admin())
        {
            return true;
        }

        if (! $location instanceof RightsLocation)
        {
            return false;
        }

        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        return $this->is_allowed_on_location($right, $userIdentifier, $entities, $location);
    }

    /**
     *
     * @param int $right
     * @param int $user_id
     * @param \rights\RightsEntity[] $entities
     * @param RightsLocation $location
     * @return boolean
     */
    public function is_allowed_on_location($right, $user_id, $entities, $location)
    {
        $rights_array = $this->retrieve_granted_rights_array(
            $location,
            $this->get_entities_condition($user_id, $entities));

        return in_array($right, $rights_array);
    }

    /**
     *
     * @param int $user_id
     * @param \rights\RightsEntity[] $entities
     * @param boolean $to_string
     * @return \libraries\storage\Condition
     */
    private function get_entities_condition($user_id, $entities)
    {
        if (! empty($entities))
        {
            $entitiesHash = md5(serialize($entities));

            if (is_null($this->entities_condition_cache[$user_id][$entitiesHash]))
            {
                $or_conditions = array();
                foreach ($entities as $entity)
                {
                    $entity_type_condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRight::class_name(),
                            RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($entity->get_entity_type()));

                    foreach ($entity->retrieve_entity_item_ids_linked_to_user($user_id) as $entity_item_id)
                    {
                        $and_conditions = array();
                        $and_conditions[] = $entity_type_condition;

                        $and_conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                RightsLocationEntityRight::class_name(),
                                RightsLocationEntityRight::PROPERTY_ENTITY_ID),
                            new StaticConditionVariable($entity_item_id));

                        $or_conditions[] = new AndCondition($and_conditions);
                    }
                }

                // add everyone 'entity'

                $and_conditions = array();

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class_name(),
                        RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(0));

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class_name(),
                        RightsLocationEntityRight::PROPERTY_ENTITY_ID),
                    new StaticConditionVariable(0));

                $or_conditions[] = new AndCondition($and_conditions);

                $condition = new OrCondition($or_conditions);

                $this->entities_condition_cache[$user_id][$entitiesHash] = $condition;
            }

            return $this->entities_condition_cache[$user_id][$entitiesHash];
        }
    }

    /**
     * Retrieves the granted rights for a location
     *
     * @param RightsLocation $location
     * @param \libraries\storage\Condition $entities_condition
     * @return int[]
     */
    public function retrieve_granted_rights_array($location, $entities_condition)
    {
        $hash = (is_object($entities_condition) ? spl_object_hash($entities_condition) : md5($entities_condition));

        if (is_null($this->granted_rights_cache[$location->get_node_id()][$hash]))
        {
            $records = $this->findRightsLocationEntityRightsRecordsForLocation($location, $entities_condition);

            $granted_rights = array();

            foreach ($records as $record)
            {
                $granted_rights[] = $record[RightsLocationEntityRight::PROPERTY_RIGHT_ID];
            }

            if ($location->inherits())
            {
                $parent_location = $this->get_location(
                    $location->get_node()->get_parent(),
                    $location->get_publication_id());

                if ($parent_location)
                {
                    $parent_rights = $this->retrieve_granted_rights_array($parent_location, $entities_condition);
                    $granted_rights = array_merge($granted_rights, $parent_rights);
                }
            }

            $this->granted_rights_cache[$location->get_node_id()][$hash] = $granted_rights;
        }
        return $this->granted_rights_cache[$location->get_node_id()][$hash];
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation $location
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $entitiesCondition
     * @return string[]
     */
    public function findRightsLocationEntityRightsRecordsForLocation(RightsLocation $location,
        Condition $entitiesCondition)
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightsRecordsForLocation(
            $location,
            $entitiesCondition);
    }

    /**
     *
     * @param ComplexContentObjectPath $node
     * @param integer $publication_id
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation
     */
    public function get_location(ComplexContentObjectPathNode $node = null, $publication_id)
    {
        if (! $node)
        {
            return null;
        }

        $node_id = $node->get_hash();

        $location = new RightsLocation();

        $location->set_node_id($node_id);
        $location->set_publication_id($publication_id);

        // Check inherit

        if ($node->is_root())
        {
            $location->set_inherit(0);
        }

        else
        {
            try
            {
                $record = $this->findRightsLocationForPublicationIdentifierAndNodeIdentifier(
                    $location->get_publication_id(),
                    $location->get_node_id());

                if ($record === false)
                {
                    $location->set_inherit(1);
                }
                else
                {
                    $location->set_inherit($record[RightsLocation::PROPERTY_INHERIT]);
                }
            }
            catch (\Exception $exception)
            {
                $location->set_inherit(1);
            }
        }

        $location->set_node($node);

        if ($node->is_root())
        {
            $location->set_parent_id(null);
        }
        else
        {
            $location->set_parent_id($node->get_parent()->get_hash());
        }

        return $location;
    }

    /**
     *
     * @param integer $publicationIdentifier
     * @param integer $nodeIdentifier
     * @return string[]
     */
    public function findRightsLocationForPublicationIdentifierAndNodeIdentifier($publicationIdentifier, $nodeIdentifier)
    {
        return $this->getRightsRepository()->findRightsLocationForPublicationIdentifierAndNodeIdentifier(
            $publicationIdentifier,
            $nodeIdentifier);
    }

    /**
     *
     * @param integer $publicationId
     * @param \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio $portfolio
     * @return boolean
     */
    public function createRightsForEveryUserAtPortfolioRoot($publicationId, Portfolio $portfolio)
    {
        $contentObjectPath = $portfolio->get_complex_content_object_path();
        $rootNode = $contentObjectPath->get_root();
        $rootNodeHash = $rootNode->get_hash();

        return $this->createRightsForEveryUserOnLocation($publicationId, $rootNodeHash);
    }

    /**
     * Creates rights for the "everyone" entity on a given location
     *
     * @param integer $publicationId
     * @param integer $nodeId
     * @return boolean
     */
    public function createRightsForEveryUserOnLocation($publicationId, $nodeId)
    {
        return $this->createRightsLocationEntityRightFromParameters(
            RightsService::VIEW_RIGHT,
            0,
            0,
            $nodeId,
            $publicationId);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight $locationEntityRight
     * @return boolean
     */
    public function createRightsLocationEntityRight(RightsLocationEntityRight $locationEntityRight)
    {
        return $this->getRightsRepository()->createRightsLocationEntityRight($locationEntityRight);
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation $location
     * @param integer[] $rights
     * @return \Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRightsForLocationAndRights(RightsLocation $location, $rights)
    {
        if (! is_array($rights))
        {
            $rights = array($rights);
        }

        return $this->getRightsRepository()->findRightsLocationEntityRightsForLocationAndRights($location, $rights);
    }
}
