<?php
namespace Chamilo\Application\Survey\Repository;

use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Survey\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationRepository
{

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function findPublicationByIdentifier($identifier)
    {
        return DataManager::retrieve_by_id(Publication::class_name(), $identifier);
    }

    /**
     *
     * @param integer[] $identifiers
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findPublicationsByIdentifiers($identifiers, $limit = null, $offset = null, $orderProperty = array())
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
            $identifiers);
        
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters($condition, $limit, $offset, $orderProperty));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findPublicationsByCreator(User $user, $limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters(
                $this->getPublicationsByCreatorCondition($user), 
                $limit, 
                $offset, 
                $orderProperty));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countPublicationsByCreator(User $user)
    {
        return DataManager::count(
            Publication::class_name(), 
            new DataClassCountParameters($this->getPublicationsByCreatorCondition($user)));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    private function getPublicationsByCreatorCondition(User $user)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID), 
            new StaticConditionVariable($user->getId()));
    }

    /**
     *
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllPublications($limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters(null, $limit, $offset, $orderProperty));
    }

    /**
     *
     * @return integer
     */
    public function countAllPublications()
    {
        return DataManager::count(Publication::class_name());
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findSharedPublicationsForEntities($entities, $limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters(
                $this->getSharedPublicationsForEntitiesWithRightCondition($entities), 
                $limit, 
                $offset, 
                $orderProperty, 
                new Joins(array($this->getSharedPublicationsJoin()))));
    }

    /**
     *
     * @param integer[] $entities
     * @return integer
     */
    public function countSharedPublicationsForEntities($entities)
    {
        return DataManager::count(
            Publication::class_name(), 
            new DataClassCountParameters(
                $this->getSharedPublicationsForEntitiesWithRightCondition($entities), 
                new Joins(array($this->getSharedPublicationsJoin()))));
    }

    /**
     *
     * @param integer[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getSharedPublicationsForEntitiesWithRightCondition($entities, $right = RightsService :: RIGHT_VIEW)
    {
        $conditions = array();
        
        foreach ($entities as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityConditions = array();
                
                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        PublicationEntityRelation::class_name(), 
                        PublicationEntityRelation::PROPERTY_ENTITY_ID), 
                    new StaticConditionVariable($entityIdentifier));
                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        PublicationEntityRelation::class_name(), 
                        PublicationEntityRelation::PROPERTY_ENTITY_TYPE), 
                    new StaticConditionVariable($entityType));
                $entityConditions[] = new EqualityCondition(
                    new OperationConditionVariable(
                        new PropertyConditionVariable(
                            PublicationEntityRelation::class_name(), 
                            PublicationEntityRelation::PROPERTY_RIGHTS), 
                        OperationConditionVariable::BITWISE_AND, 
                        new StaticConditionVariable($right)), 
                    new StaticConditionVariable($right));
                
                $conditions[] = new AndCondition($entityConditions);
            }
        }
        
        return new OrCondition($conditions);
    }

    /**
     *
     * @param integer $joinType
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getSharedPublicationsJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            PublicationEntityRelation::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(
                    PublicationEntityRelation::class_name(), 
                    PublicationEntityRelation::PROPERTY_PUBLICATION_ID), 
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID)), 
            $joinType);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findPublicationFavouritesByUser(User $user, $entities, $limit = null, $offset = null, 
        $orderProperty = array())
    {
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters(
                $this->getPublicationFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW), 
                $limit, 
                $offset, 
                $orderProperty, 
                $this->getPublicationFavouritesByUserJoins(), 
                true));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @return integer
     */
    public function countPublicationFavouritesByUser(User $user, $entities)
    {
        return DataManager::count(
            Publication::class_name(), 
            new DataClassCountParameters(
                $this->getPublicationFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW), 
                $this->getPublicationFavouritesByUserJoins(), 
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, 
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID))));
    }

    /**
     *
     * @param integer $joinType
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getFavouritesJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            PublicationUserFavourite::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(
                    PublicationUserFavourite::class_name(), 
                    PublicationUserFavourite::PROPERTY_PUBLICATION_ID), 
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID)), 
            $joinType);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    private function getPublicationFavouritesByUserJoins()
    {
        $joins = new Joins();
        $joins->add($this->getSharedPublicationsJoin(Join::TYPE_LEFT));
        $joins->add($this->getFavouritesJoin());
        return $joins;
    }

    /**
     *
     * @param User $user
     * @param integer[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getPublicationFavouritesByUserCondition(User $user, $entities, $right)
    {
        $orConditions = array();
        $andConditions = array();
        
        $orConditions[] = $this->getPublicationsByCreatorCondition($user);
        $orConditions[] = $this->getSharedPublicationsForEntitiesWithRightCondition($entities, $right);
        
        $andConditions[] = new OrCondition($orConditions);
        $andConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PublicationUserFavourite::class_name(), 
                PublicationUserFavourite::PROPERTY_USER_ID), 
            new StaticConditionVariable($user->getId()));
        
        return new AndCondition($andConditions);
    }

    public function findPublicationsForUser(User $user, $entities, $right, $limit, $offset, $orderProperty = null)
    {
        return DataManager::retrieves(
            Publication::class_name(), 
            new DataClassRetrievesParameters(
                $this->getPublicationFavouritesByUserCondition($user, $entities, $right), 
                $limit, 
                $offset, 
                $orderProperty, 
                new Joins(array($this->getSharedPublicationsJoin(Join::TYPE_LEFT)))));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @return integer
     */
    public function countPublicationsForUser(User $user, $entities, $right)
    {
        return DataManager::count(
            Publication::class_name(), 
            new DataClassCountParameters(
                $this->getPublicationFavouritesByUserCondition($user, $entities, $right), 
                new Joins(array($this->getSharedPublicationsJoin(Join::TYPE_LEFT)))));
    }
}