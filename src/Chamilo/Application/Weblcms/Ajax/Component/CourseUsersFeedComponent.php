<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return users of this course
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class CourseUsersFeedComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_COURSE_ID = 'course_id';
    const PARAM_OFFSET = 'offset';
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    private $user_count = 0;

    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_COURSE_ID);
    }

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $elements = $this->get_elements();
        $elements = $elements->as_array();

        $result->set_property(self :: PROPERTY_ELEMENTS, $elements);
        $result->set_property(self :: PROPERTY_TOTAL_ELEMENTS, $this->user_count);

        $result->display();
    }

    /**
     * Returns all the elements for this feed
     *
     * @return Array
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();

        // Add user category
        $user_category = new AdvancedElementFinderElement('users', 'category', 'Users', 'Users');
        $elements->add_element($user_category);

        $users = $this->retrieve_users();
        if ($users)
        {
            while ($user = $users->next_result())
            {
                $user_category->add_child(
                    new AdvancedElementFinderElement(
                        CourseUserEntity :: ENTITY_TYPE . '_' . $user->get_id(),
                        'type type_user',
                        $user->get_fullname(),
                        $user->get_official_code()));
            }
        }

        return $elements;
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     *
     * @return ResultSet
     */
    private function retrieve_users()
    {
        $course_id = $this->getPostDataValue(self :: PARAM_COURSE_ID);

        // Retrieve the users directly subscribed to the course
        $userConditions = array();
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $userConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_USER));

        $parameters = new DataClassDistinctParameters(
            new AndCondition($userConditions),
            CourseEntityRelation :: PROPERTY_ENTITY_ID);

        $user_ids = DataManager :: distinct(
            \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation :: class_name(),
            $parameters);

        // Retrieve the users subscribed through platform groups
        $groupConditions = array();
        $groupConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $groupConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseEntityRelation :: class_name(),
                CourseEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(CourseEntityRelation :: ENTITY_TYPE_GROUP));

        $groups = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            Group :: class_name(),
            new DataClassRetrievesParameters(
                new AndCondition($groupConditions),
                null,
                null,
                array(),
                new Joins(
                    new Join(
                        CourseEntityRelation :: class_name(),
                        new EqualityCondition(
                            new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                            new PropertyConditionVariable(
                                CourseEntityRelation :: class_name(),
                                CourseEntityRelation :: PROPERTY_ENTITY_ID))))));

        $parameters = new DataClassDistinctParameters(
            new AndCondition($userConditions),
            CourseEntityRelation :: PROPERTY_ENTITY_ID);

        $groupIdentifiers = DataManager :: distinct(
            \Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation :: class_name(),
            $parameters);

        $group_users = array();

        while ($group = $groups->next_result())
        {
            $group_user_ids = $group->get_users(true, true);

            $group_users = array_merge($group_users, $group_user_ids);
        }

        $user_ids = array_unique(array_merge($user_ids, $group_users));

        if (count($user_ids) == 0)
        {
            return;
        }

        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities :: query_to_condition(
                $search_query,
                array(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME)));
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
            $user_ids);

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }
        $this->user_count = \Chamilo\Core\User\Storage\DataManager :: count(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $condition);

        $parameters = new DataClassRetrievesParameters(
            $condition,
            100,
            $this->get_offset(),
            array(
                new OrderBy(new PropertyConditionVariable(User:: class_name(), User :: PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User:: class_name(), User :: PROPERTY_FIRSTNAME))
            )
        );

        return \Chamilo\Core\User\Storage\DataManager :: retrieves(User :: class_name(), $parameters);
    }

    protected function get_offset()
    {
        $offset = Request :: post(self :: PARAM_OFFSET);
        if (! isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }
}
