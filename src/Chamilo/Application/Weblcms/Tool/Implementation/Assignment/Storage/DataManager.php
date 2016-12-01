<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the data manager for this package
 * 
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from MDB2
 * @package application.weblcms.tool.assignment
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * **************************************************************************************************************
     * Assignment Submissions functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves the submissions by a given submitter type
     * 
     * @param int $publication_id
     * @param mixed $submitter_type
     * @param string $class_name
     *
     * @return RecordResultSet
     */
    public static function retrieve_submissions_by_submitter_type($publication_id, $submitter_type, $class_name = null)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $date_submitted_variable = new PropertyConditionVariable(
            AssignmentSubmission::class_name(), 
            AssignmentSubmission::PROPERTY_DATE_SUBMITTED);
        
        $properties->add(
            new FunctionConditionVariable(FunctionConditionVariable::MIN, $date_submitted_variable, 'first_date'));
        
        $properties->add(
            new FunctionConditionVariable(FunctionConditionVariable::MAX, $date_submitted_variable, 'last_date'));
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT, 
                new PropertyConditionVariable(AssignmentSubmission::class_name(), AssignmentSubmission::PROPERTY_ID), 
                'count'));
        
        $properties->add(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_TYPE));
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        
        $condition = new AndCondition($conditions);
        
        $group_by = new GroupBy();
        $group_by->add(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), null, $group_by);
        
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::records(
            AssignmentSubmission::class_name(), 
            $parameters);
    }

    /**
     * Retrieves the submitter feedback from the tracker
     * 
     * @param $publication_id
     * @param $submitter_type
     * @return mixed
     */
    public static function retrieve_submitter_feedbacks($publication_id, $submitter_type)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT, 
                new PropertyConditionVariable(AssignmentSubmission::class_name(), AssignmentSubmission::PROPERTY_ID), 
                'count'));
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        
        $condition = new AndCondition($conditions);
        
        $group_by = new GroupBy();
        $group_by->add(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $joins = new Joins();
        $joins->add(
            new Join(
                SubmissionFeedback::class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        AssignmentSubmission::class_name(), 
                        AssignmentSubmission::PROPERTY_ID), 
                    new PropertyConditionVariable(
                        SubmissionFeedback::class_name(), 
                        SubmissionFeedback::PROPERTY_SUBMISSION_ID))));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins, $group_by);
        
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager::records(
            AssignmentSubmission::class_name(), 
            $parameters);
    }

    /**
     * **************************************************************************************************************
     * Assignment Publication Target Entities Functionality * TODO: Check if refactoring is possible because there is
     * some copy paste code *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves the assignment publication target users
     * 
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet<AssignmentSubmission>
     */
    public static function retrieve_assignment_publication_target_users($publication_id, $course_id, $offset = null, 
        $count = null, $order_by = null, $condition = null)
    {
        $users = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_users(
            $publication_id, 
            $course_id, 
            null, 
            null, 
            null, 
            $condition)->as_array();
        
        $user_ids = array();
        
        foreach ($users as $user)
        {
            $user_ids[$user->get_id()] = $user->get_id();
        }
        
        if (count($user_ids) < 1)
        {
            $user_ids[] = - 1;
        }
        
        $conditions = array();
        
        ! is_null($condition) ? $conditions[] = $condition : $condition;
        
        $conditions[] = new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $user_ids);
        
        $condition = new AndCondition($conditions);
        
        return self::retrieve_assignment_publication_targets(
            $publication_id, 
            AssignmentSubmission::SUBMITTER_TYPE_USER, 
            $condition, 
            $offset, 
            $count, 
            $order_by);
    }

    /**
     * Retrieves the assignment publication target course groups
     * 
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet<AssignmentSubmission>
     */
    public static function retrieve_assignment_publication_target_course_groups($publication_id, $course_id, 
        $offset = null, $count = null, $order_by = null, $condition = null)
    {
        $course_groups = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_course_groups(
            $publication_id, 
            $course_id, 
            null, 
            null, 
            null, 
            $condition)->as_array();
        
        $course_group_ids = array();
        
        foreach ($course_groups as $course_group)
        {
            $course_group_ids[$course_group->get_id()] = $course_group->get_id();
        }
        
        if (count($course_group_ids) < 1)
        {
            $course_group_ids[] = - 1;
        }
        
        $conditions = array();
        
        ! is_null($condition) ? $conditions[] = $condition : $condition;
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_ID), 
            $course_group_ids);
        
        $condition = new AndCondition($conditions);
        
        return self::retrieve_assignment_publication_targets(
            $publication_id, 
            AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP, 
            $condition, 
            $offset, 
            $count, 
            $order_by);
    }

    /**
     * Retrieves the assignment publication target platform groups
     * 
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet<AssignmentSubmission>
     */
    public static function retrieve_assignment_publication_target_platform_groups($publication_id, $course_id, 
        $offset = null, $count = null, $order_by = null, $condition = null)
    {
        $platform_groups = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_publication_target_platform_groups(
            $publication_id, 
            $course_id, 
            null, 
            null, 
            null, 
            $condition)->as_array();
        
        $platform_group_ids = array();
        
        foreach ($platform_groups as $platform_group)
        {
            $platform_group_ids[$platform_group->get_id()] = $platform_group->get_id();
        }
        
        if (count($platform_group_ids) < 1)
        {
            $platform_group_ids[] = - 1;
        }
        
        $conditions = array();
        
        ! is_null($condition) ? $conditions[] = $condition : $condition;
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), 
            $platform_group_ids);
        
        $condition = new AndCondition($conditions);
        
        return self::retrieve_assignment_publication_targets(
            $publication_id, 
            AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP, 
            $condition, 
            $offset, 
            $count, 
            $order_by);
    }

    /**
     * Retrieves the assignment publication targets
     * 
     * @param int $publication_id
     * @param int $submitter_type
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param int $order_by
     *
     * @return RecordResultSet
     */
    public static function retrieve_assignment_publication_targets($publication_id, $submitter_type, $condition, 
        $offset = null, $count = null, $order_by = null)
    {
        $properties = new DataClassProperties();
        
        switch ($submitter_type)
        {
            case AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $base_class = CourseGroup::class_name();
                
                $properties->add(
                    new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));
                
                $data_manager = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::class_name();
                
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $base_class = Group::class_name();
                
                $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME));
                
                $data_manager = \Chamilo\Core\Group\Storage\DataManager::class_name();
                
                break;
            case AssignmentSubmission::SUBMITTER_TYPE_USER :
                $base_class = User::class_name();
                
                $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
                $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
                
                $data_manager = \Chamilo\Core\User\Storage\DataManager::class_name();
                
                break;
        }
        
        $base_variable = new PropertyConditionVariable($base_class, $base_class::PROPERTY_ID);
        
        $properties->add(
            new FixedPropertyConditionVariable(
                $base_class, 
                $base_class::PROPERTY_ID, 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $date_submitted_variable = new PropertyConditionVariable(
            AssignmentSubmission::class_name(), 
            AssignmentSubmission::PROPERTY_DATE_SUBMITTED);
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MIN, 
                $date_submitted_variable, 
                Manager::PROPERTY_FIRST_SUBMISSION));
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX, 
                $date_submitted_variable, 
                Manager::PROPERTY_LAST_SUBMISSION));
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT, 
                $date_submitted_variable, 
                Manager::PROPERTY_NUMBER_OF_SUBMISSIONS));
        
        $joins = new Joins();
        
        $join_conditions = array();
        
        $join_conditions[] = new EqualityCondition(
            $base_variable, 
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_ID));
        
        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        
        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AssignmentSubmission::class_name(), 
                AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
            new StaticConditionVariable($submitter_type));
        
        $join_condition = new AndCondition($join_conditions);
        
        $joins->add(new Join(AssignmentSubmission::class_name(), $join_condition, Join::TYPE_LEFT));
        
        $group_by = new GroupBy();
        $group_by->add($base_variable);
        
        $parameters = new RecordRetrievesParameters(
            $properties, 
            $condition, 
            $count, 
            $offset, 
            $order_by, 
            $joins, 
            $group_by);
        
        return $data_manager::records($base_class, $parameters);
    }
}
