<?php
namespace Chamilo\Application\Weblcms\Course\Storage\DataManager\Implementation;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet\RecordResultSet;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Variable\ConditionVariableTranslator;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\CaseConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Doctrine implementation of the datamanager
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineExtension
{
    const PARAM_SUBSCRIPTION_STATUS = 'record_subscription_status';
    const PARAM_SUBSCRIPTION_TYPE = 'record_subscription_type';
    const PARAM_COUNT = 'count';

    /**
     *
     * @var DoctrineDatabase
     */
    private $database;

    /**
     *
     * @param DoctrineDatabase $database
     */
    public function __construct(\Chamilo\Libraries\Storage\DataManager\Doctrine\Database $database)
    {
        $this->database = $database;
    }

    /**
     * **************************************************************************************************************
     * Implemented Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves all course users of a given course with status and subscription type
     *
     * @param int $course_id
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @throws \libraries\storage\DataClassNoResultException
     *
     * @return RecordResultSet
     */
    public function retrieve_all_course_users($course_id, $condition = null, $offset = null, $count = null, $order_property = null)
    {
        $properties = $this->get_user_properties_for_select();

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: MIN,
                new StaticConditionVariable(self :: PARAM_SUBSCRIPTION_STATUS, false),
                'subscription_status'));

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: SUM,
                new StaticConditionVariable(self :: PARAM_SUBSCRIPTION_TYPE, false),
                'subscription_type'));

        $sql = $this->build_basic_sql_all_course_users($properties, $course_id, $condition);

        $query_builder = $this->database->get_connection()->createQueryBuilder();

        $group_by = new GroupBy();

        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL));
        $group_by->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_STATUS));

        $query_builder = $this->process_group_by($query_builder, $group_by);
        $query_builder = $this->process_order_by($query_builder, User :: class_name(), $order_property);
        $query_builder = $this->process_limit($query_builder, $count, $offset);

        $sql .= substr($query_builder->getSQL(), 14);

        try
        {
            return new RecordResultSet($this->database->get_connection()->query($sql));
        }
        catch (\PDOException $exception)
        {
            $this->database->error_handling($exception);
            throw new DataClassNoResultException(User :: class_name(), null);
        }
    }

    /**
     * Counts all course users of a given course with status and subscription type
     *
     * @param int $course_id
     * @param Condition $condition
     *
     * @throws \libraries\storage\DataClassNoResultException
     *
     * @return RecordResultSet
     */
    public function count_all_course_users($course_id, $condition = null)
    {
        $properties = new DataClassProperties();

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable :: DISTINCT,
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME)),
                self :: PARAM_COUNT));

        $sql = $this->build_basic_sql_all_course_users($properties, $course_id, $condition);

        try
        {
            $result = $this->database->get_connection()->query($sql);
            $row = $result->fetch(\PDO :: FETCH_ASSOC);

            return $row[self :: PARAM_COUNT];
        }
        catch (\PDOException $exception)
        {
            $this->database->error_handling($exception);
            throw new DataClassNoResultException(User :: class_name(), null);
        }
    }

    /**
     * **************************************************************************************************************
     * All Course Users Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Builds the basic sql for the "all course users" funtions
     *
     * @param DataClassProperties $properties
     * @param int $course_id
     * @param Condition $condition
     *
     * @return string
     */
    protected function build_basic_sql_all_course_users($properties, $course_id, $condition)
    {
        $query_builder = $this->database->get_connection()->createQueryBuilder();
        $query_builder = $this->process_data_class_properties($query_builder, User :: class_name(), $properties);

        $sql = $query_builder->getSQL();

        $sql_subscribed_users = $this->build_sql_for_subscribed_users($course_id, $condition);
        $sql_subscribed_groups = $this->build_sql_for_subscribed_group_users($course_id, $condition);

        $sql .= '(' .
             ($sql_subscribed_groups ? $sql_subscribed_users . ' UNION ' . $sql_subscribed_groups : $sql_subscribed_users) .
             ') AS ' . \Chamilo\Core\User\Storage\DataManager :: get_instance()->get_alias(User :: get_table_name()) .
             ' ';

        return $sql;
    }

    /**
     * Retrieves the direct subscribed groups joined with the group table to support the tree values
     *
     * @param $course_id
     * @return \libraries\storage\RecordResultSet
     */
    protected function retrieve_direct_subscribed_groups_with_tree_values($course_id)
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_LEFT_VALUE));
        $properties->add(new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_RIGHT_VALUE));

        $properties->add(
            new PropertyConditionVariable(CourseEntityRelation :: class_name(), CourseEntityRelation :: PROPERTY_STATUS));

        $joins = new Joins();

        $joins->add(
            new Join(
                Group :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                    new PropertyConditionVariable(
                        CourseEntityRelation :: class_name(),
                        CourseEntityRelation :: PROPERTY_ENTITY_ID))));

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_GROUP));
        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, null, $joins);

        return $this->database->records(CourseEntityRelation :: class_name(), $parameters);
    }

    /**
     * Returns the properties for the user that are used in the select statement
     *
     * @return \libraries\storage\DataClassProperties
     */
    protected function get_user_properties_for_select()
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL));
        $properties->add(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_STATUS));

        return $properties;
    }

    /**
     * Builds the sql for the directly subscribed users
     *
     * @param int $course_id
     * @param Condition $condition
     *
     * @return string
     */
    protected function build_sql_for_subscribed_users($course_id, $condition)
    {
        $properties = $this->get_user_properties_for_select();

        $properties->add(
            new FixedPropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_STATUS,
                self :: PARAM_SUBSCRIPTION_STATUS));

        $properties->add(new StaticConditionVariable('1 AS ' . self :: PARAM_SUBSCRIPTION_TYPE, false));

        $joins = new Joins();

        $joins->add(
            new Join(
                User :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                    new PropertyConditionVariable(
                        CourseEntityRelation :: class_name(),
                        CourseEntityRelation :: PROPERTY_ENTITY_ID))));

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_USER));

        if (isset($condition))
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, null, $joins);

        return $this->database->build_records_sql(CourseEntityRelation :: class_name(), $parameters);
    }

    /**
     * Builds the sql for the users subscribed through (sub) platform groups
     *
     * @param int $course_id
     * @param Condition $condition
     *
     * @return string
     */
    protected function build_sql_for_subscribed_group_users($course_id, $condition)
    {
        $direct_groups_with_tree_values = self :: retrieve_direct_subscribed_groups_with_tree_values($course_id);

        if ($direct_groups_with_tree_values->size() > 0)
        {
            $properties = $this->get_user_properties_for_select();

            $case_condition_variable = new CaseConditionVariable(array(), self :: PARAM_SUBSCRIPTION_STATUS);

            $direct_group_conditions = array();
            while ($group = $direct_groups_with_tree_values->next_result())
            {
                $and_conditions = array();

                $and_conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_LEFT_VALUE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group :: PROPERTY_LEFT_VALUE]));

                $and_conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_RIGHT_VALUE),
                    InequalityCondition :: LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable($group[Group :: PROPERTY_RIGHT_VALUE]));

                $direct_group_condition = new AndCondition($and_conditions);
                $direct_group_conditions[] = $direct_group_condition;

                $case_condition_variable->add(
                    new CaseElementConditionVariable(
                        $group[CourseEntityRelation :: PROPERTY_STATUS],
                        $direct_group_condition));
            }

            $properties->add($case_condition_variable);
            $properties->add(new StaticConditionVariable('2 AS ' . self :: PARAM_SUBSCRIPTION_TYPE, false));

            $joins = new Joins();

            $joins->add(
                new Join(
                    GroupRelUser :: class_name(),
                    new EqualityCondition(
                        new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_GROUP_ID))));

            $joins->add(
                new Join(
                    User :: class_name(),
                    new EqualityCondition(
                        new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                        new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_USER_ID))));

            $conditions = array();

            $conditions[] = new OrCondition($direct_group_conditions);

            if (isset($condition))
            {
                $conditions[] = $condition;
            }

            $condition = new AndCondition($conditions);

            $parameters = new RecordRetrievesParameters($properties, $condition, null, null, null, $joins);

            return \Chamilo\Core\Group\Storage\DataManager :: get_instance()->build_records_sql(
                Group :: class_name(),
                $parameters);
        }
    }

    /**
     * Processes the dataclass properties
     *
     * @param QueryBuilder $query_builder
     * @param string $class
     * @param DataClassProperties $properties
     *
     * @return QueryBuilder
     */
    public function process_data_class_properties($query_builder, $class, $properties)
    {
        if ($properties instanceof DataClassProperties)
        {
            foreach ($properties->get() as $condition_variable)
            {
                $query_builder->addSelect(ConditionVariableTranslator :: render($condition_variable));
            }
        }
        else
        {
            $query_builder->addSelect($this->database->get_alias($class :: get_table_name()) . '.*');
        }

        return $query_builder;
    }

    /**
     * Processes the group by
     *
     * @param QueryBuilder $query_builder
     * @param GroupBy $group_by
     *
     * @return QueryBuilder
     */
    public function process_group_by($query_builder, $group_by)
    {
        if ($group_by instanceof GroupBy)
        {
            foreach ($group_by->get() as $group_by_variable)
            {
                $query_builder->addGroupBy(ConditionVariableTranslator :: render($group_by_variable));
            }
        }

        return $query_builder;
    }

    /**
     * Processes the order by
     *
     * @param QueryBuilder $query_builder
     * @param string $class
     * @param ObjectTableOrder[] $order_by
     *
     * @return QueryBuilder
     */
    public function process_order_by($query_builder, $class, $order_by)
    {
        if (is_null($order_by))
        {
            $order_by = array();
        }
        elseif (! is_array($order_by))
        {
            $order_by = array($order_by);
        }

        foreach ($order_by as $order)
        {
            $query_builder->addOrderBy(
                ConditionVariableTranslator :: render($order->get_property()),
                ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC'));
        }

        return $query_builder;
    }

    /**
     * Processes query limit values
     *
     * @param QueryBuilder $query_builder
     * @param int $count
     * @param int $offset
     *
     * @return QueryBuilder
     */
    public function process_limit($query_builder, $count = null, $offset = null)
    {
        if (intval($count) > 0)
        {
            $query_builder->setMaxResults(intval($count));
        }

        if (intval($offset) > 0)
        {
            $query_builder->setFirstResult(intval($offset));
        }

        return $query_builder;
    }
}
