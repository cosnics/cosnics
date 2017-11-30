<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Manages the entities for the learning path assignment submissions
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathAssignmentRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathAssignmentRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     *
     * @param integer $treeNodeAttemptIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeAttemptIdentifier($treeNodeAttemptIdentifier)
    {
        $condition = $this->getTreeNodeAttemptConditionByIdentifier($treeNodeAttemptIdentifier);
        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset, $count,
        $orderBy
    )
    {
        $users = $this->retrieveTreeNodeAttemptTargetUsers($treeNodeAttempt, $condition)->as_array();

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));

        $baseClass = User::class_name();

        return $this->findTargetsForEntityTypeAndTreeNodeAttempt(
            Entry::ENTITY_TYPE_USER,
            $treeNodeAttempt,
            $this->getTargetEntitiesCondition(User::class_name(), $users, $condition),
            $offset,
            $count,
            $orderBy,
            $properties,
            $baseClass,
            $this->getTargetBaseVariable($baseClass)
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetUsersForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition)
    {
        return $this->retrieveTreeNodeAttemptTargetUsers($treeNodeAttempt, $condition)->size();
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    private function retrieveTreeNodeAttemptTargetUsers(TreeNodeAttempt $treeNodeAttempt, Condition $condition = null)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_users(
            $treeNodeAttempt->getId(),
            $treeNodeAttempt->get_course_id(),
            null,
            null,
            null,
            $condition
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetCourseGroupsForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset,
        $count, $orderBy
    )
    {
        $courseGroups = $this->retrieveTreeNodeAttemptTargetCourseGroups($treeNodeAttempt, $condition)->as_array();

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));

        $baseClass = CourseGroup::class_name();

        return $this->findTargetsForEntityTypeAndTreeNodeAttempt(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $treeNodeAttempt,
            $this->getTargetEntitiesCondition(CourseGroup::class_name(), $courseGroups, $condition),
            $offset,
            $count,
            $orderBy,
            $properties,
            $baseClass,
            $this->getTargetBaseVariable($baseClass)
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetCourseGroupsForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition)
    {
        return $this->retrieveTreeNodeAttemptTargetCourseGroups($treeNodeAttempt, $condition)->size();
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    private function retrieveTreeNodeAttemptTargetCourseGroups(
        TreeNodeAttempt $treeNodeAttempt,
        Condition $condition = null
    )
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_course_groups(
            $treeNodeAttempt->getId(),
            $treeNodeAttempt->get_course_id(),
            null,
            null,
            null,
            $condition
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetGroupsForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset, $count,
        $orderBy
    )
    {
        $platformGroups = $this->retrieveTreeNodeAttemptTargetPlatformGroups($treeNodeAttempt, $condition)->as_array();

        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME));

        $baseClass = Group::class_name();

        return $this->findTargetsForEntityTypeAndTreeNodeAttempt(
            Entry::ENTITY_TYPE_GROUP,
            $treeNodeAttempt,
            $this->getTargetEntitiesCondition(Group::class_name(), $platformGroups, $condition),
            $offset,
            $count,
            $orderBy,
            $properties,
            $baseClass,
            $this->getTargetBaseVariable($baseClass)
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetGroupsForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition)
    {
        return $this->retrieveTreeNodeAttemptTargetPlatformGroups($treeNodeAttempt, $condition)->size();
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    private function retrieveTreeNodeAttemptTargetPlatformGroups(
        TreeNodeAttempt $treeNodeAttempt,
        Condition $condition = null
    )
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_platform_groups(
            $treeNodeAttempt->getId(),
            $treeNodeAttempt->get_course_id(),
            null,
            null,
            null,
            $condition
        );
    }

    /**
     *
     * @param string $entityClass
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $entities
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getTargetEntitiesCondition($entityClass, $entities, Condition $condition = null)
    {
        $entityIds = array();

        foreach ($entities as $entity)
        {
            $entityIds[$entity->getId()] = $entity->getId();
        }

        if (count($entityIds) < 1)
        {
            $entityIds[] = - 1;
        }

        $conditions = array();

        !is_null($condition) ? $conditions[] = $condition : null;

        $conditions[] =
            new InCondition(new PropertyConditionVariable($entityClass, DataClass::PROPERTY_ID), $entityIds);

        return new AndCondition($conditions);
    }

    /**
     *
     * @param string $baseClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    private function getTargetBaseVariable($baseClass)
    {
        return new PropertyConditionVariable($baseClass, $baseClass::PROPERTY_ID);
    }

    /**
     *
     * @param integer $entityType
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     * @param DataClassProperties $properties
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    private function findTargetsForEntityTypeAndTreeNodeAttempt(
        $entityType, TreeNodeAttempt $treeNodeAttempt,
        $condition, $offset, $count, $orderBy, DataClassProperties $properties, $baseClass, $baseVariable
    )
    {
        $properties->add(
            new FixedPropertyConditionVariable($baseClass, $baseClass::PROPERTY_ID, Entry::PROPERTY_ENTITY_ID)
        );
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE));

        $submittedVariable = new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED);

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MIN,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_ENTRY_COUNT
            )
        );

        $joins = new Joins();

        $joinConditions = array();

        $joinConditions[] = new EqualityCondition(
            $baseVariable,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $joinConditions[] = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $joinCondition = new AndCondition($joinConditions);

        $joins->add(new Join($this->getEntryClassName(), $joinCondition, Join::TYPE_LEFT));

        $group_by = new GroupBy();
        $group_by->add($baseVariable);

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $orderBy,
            $joins,
            $group_by
        );

        return $this->dataClassRepository->records($baseClass, $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeAttemptByEntityTypeAndEntityId(
        TreeNodeAttempt $treeNodeAttempt,
        $entityType, $entityId
    )
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();
        $joins->add(
            new Join(
                $this->getFeedbackClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID)
                )
            )
        );

        $parameters = new DataClassCountParameters($condition, $joins);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return AndCondition
     */
    private function getTreeNodeAttemptAndEntityTypeCondition(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        return new AndCondition($conditions);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeAttemptAndEntityType(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeAttemptAndEntityTypeCondition($treeNodeAttempt, $entityType),
            null,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeAttemptAndEntityType(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getFeedbackClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID)
                )
            )
        );

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeAttemptAndEntityTypeCondition($treeNodeAttempt, $entityType),
            $joins,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByTreeNodeAttemptAndEntityType(
        TreeNodeAttempt $treeNodeAttempt,
        $entityType
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                TreeNodeAttempt::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        TreeNodeAttempt::class_name(),
                        TreeNodeAttempt::PROPERTY_ID
                    ),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_TREE_NODE_ATTEMPT_ID)
                )
            )
        );

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $conditions = array();
        $conditions[] = $this->getTreeNodeAttemptAndEntityTypeCondition($treeNodeAttempt, $entityType);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($treeNodeAttempt->get_content_object()->get_end_time())
        );
        $condition = new AndCondition($conditions);

        $parameters = new DataClassCountParameters($condition, $joins, $property);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @param $entityId
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getTreeNodeAttemptEntityTypeAndIdCondition(
        TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId
    )
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        return new AndCondition($conditions);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeAttemptEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId, $condition
    )
    {
        $conditions = array();

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = $this->getTreeNodeAttemptEntityTypeAndIdCondition($treeNodeAttempt, $entityType, $entityId);

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getFeedbackClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID)
                )
            )
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeAttemptEntityTypeAndIdCondition($treeNodeAttempt, $entityType, $entityId),
            $joins
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId(TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeAttemptEntityTypeAndIdCondition($treeNodeAttempt, $entityType, $entityId),
            $joins
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return string[]
     */
    public function retrieveAverageScoreForEntityTypeAndId(TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $properties = new DataClassProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::AVERAGE,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE),
                AssignmentDataProvider::AVERAGE_SCORE
            )
        );

        $parameters = new RecordRetrieveParameters(
            $properties,
            $this->getTreeNodeAttemptEntityTypeAndIdCondition($treeNodeAttempt, $entityType, $entityId),
            array(),
            $joins
        );

        return $this->dataClassRepository->record($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntriesForTreeNodeAttemptEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId, $condition, $offset, $count, $orderProperty
    )
    {
        $conditions = array();

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = $this->getTreeNodeAttemptEntityTypeAndIdCondition($treeNodeAttempt, $entityType, $entityId);

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ContentObject::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
                ),
                Join::TYPE_LEFT
            )
        );

        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));

        $properties->add(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        $properties->add(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED));
        $properties->add(new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID));
        $properties->add(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderProperty, $joins);

        return $this->dataClassRepository->records($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entryIdentifier)
        );

        return $this->dataClassRepository->count(
            $this->getFeedbackClassName(), new DataClassCountParameters($condition)
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveEntryByIdentifier($entryIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getEntryClassName(), $entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers []
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveEntriesByIdentifiers($entryIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
            $entryIdentifiers
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function retrieveScoreByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        $score = $this->dataClassRepository->retrieve(
            $this->getScoreClassName(), new DataClassRetrieveParameters($condition)
        );

        if ($score instanceof Score)
        {
            return $score;
        }
        else
        {
            return null;
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function retrieveNoteByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getNoteClassName(), Note::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        $note = $this->dataClassRepository->retrieve(
            $this->getNoteClassName(), new DataClassRetrieveParameters($condition)
        );

        if ($note instanceof Note)
        {
            return $note;
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getFeedbackClassName(), $feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry Entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->count(
            $this->getFeedbackClassName(), new DataClassCountParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->getFeedbackClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeAttemptEntityTypeAndIdentifiers(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityIdentifiers
    )
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            $entityIdentifiers
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $condition = $this->getTreeNodeAttemptCondition($treeNodeAttempt);

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::class;
    }

    /**
     * @return string
     */
    protected function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback::class;
    }

    /**
     * @return string
     */
    protected function getNoteClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score::class;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getTreeNodeAttemptCondition(TreeNodeAttempt $treeNodeAttempt)
    {
        return $this->getTreeNodeAttemptConditionByIdentifier($treeNodeAttempt->getId());
    }

    protected function getTreeNodeAttemptConditionByIdentifier($treeNodeAttemptIdentifier)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::PROPERTY_TREE_NODE_ATTEMPT_ID
            ),
            new StaticConditionVariable($treeNodeAttemptIdentifier)
        );
    }
}