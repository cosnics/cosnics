<?php
namespace Chamilo\Application\Weblcms\Storage;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeRelCourseSetting;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeRelCourseSettingValue;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSettingValue;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseModuleLastAccess;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingDefaultValue;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocationLockedRight;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;
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
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\EmptyResultSet;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;
use Exception;

/**
 * This class represents the data manager for this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from MDB2
 * @package application.weblcms
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
     * ContentObjectPublication Functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks if a content object is published
     *
     * @param int $object_id
     *
     * @return bool
     */
    public static function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($object_id));

        return self :: count(ContentObjectPublication :: class_name(), $condition) >= 1;
    }

    /**
     * Checks if any of the content objects are published
     *
     * @param int[] $object_ids
     *
     * @return bool
     */
    public static function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            $object_ids);

        return self :: count(ContentObjectPublication :: class_name(), $condition) >= 1;
    }

    /**
     * Counts the number of content object publications
     *
     * @param int $attributes_type
     * @param int $identifier
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public static function count_publication_attributes(
        $attributes_type = PublicationInterface :: ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null)
    {
        switch ($attributes_type)
        {
            case PublicationInterface :: ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
                    new StaticConditionVariable($identifier));
                break;
            case PublicationInterface :: ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($identifier));
                break;
            default :
                return 0;
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publication_condition));
        }
        else
        {
            $condition = $publication_condition;
        }

        $parameters = new DataClassCountParameters($condition, self :: get_content_object_publication_joins());

        return self :: count(ContentObjectPublication :: class_name(), $parameters);
    }

    /**
     * Updates a content object id for a given publication with the given attributes
     *
     * @param \core\repository\publication\storage\data_class\Attributes $publication_attr
     *
     * @return bool
     */
    public static function update_content_object_publication_id($publication_attr)
    {
        $publication = self :: retrieve_by_id(ContentObjectPublication :: class_name(), $publication_attr->get_id());
        $publication->set_content_object_id($publication_attr->get_content_object_id());

        return $publication->update();
    }

    /**
     * Delete the content object publications for given content object id
     *
     * @param int $object_id
     *
     * @return bool
     */
    public static function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($object_id));
        $publications = self :: retrieves(ContentObjectPublication :: class_name(), $condition);

        while ($publication = $publications->next_result())
        {
            if (! $publication->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves content object publications joined with the repository content object table
     *
     * @param \libraries\storage\Condition $condition
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_content_object_publications($condition = null, $order_by = array(), $offset = 0,
        $max_objects = -1)
    {
        $data_class_properties = array();

        $data_class_properties[] = new PropertiesConditionVariable(ContentObjectPublication :: class_name());

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject :: class_name(),
            ContentObject :: PROPERTY_TITLE);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject :: class_name(),
            ContentObject :: PROPERTY_DESCRIPTION);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject :: class_name(),
            ContentObject :: PROPERTY_TYPE);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject :: class_name(),
            ContentObject :: PROPERTY_CURRENT);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject :: class_name(),
            ContentObject :: PROPERTY_OWNER_ID);

        $properties = new DataClassProperties($data_class_properties);

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $max_objects,
            $offset,
            $order_by,
            self :: get_content_object_publication_joins());

        return self :: records(ContentObjectPublication :: class_name(), $parameters);
    }

    /**
     * Retrieves a content object publication with content object
     *
     * @static Static method
     * @param int $publication_id
     *
     * @return mixed
     */
    public static function retrieve_content_object_publication_with_content_object($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_ID),
            new StaticConditionVariable($publication_id));

        $publications = self :: retrieve_content_object_publications($condition, array(), 0, 1);

        return $publications->next_result();
    }

    /**
     * Counts content object publications
     *
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public static function count_content_object_publications($condition)
    {
        $parameters = new DataClassCountParameters($condition, self :: get_content_object_publication_joins());

        return self :: count(ContentObjectPublication :: class_name(), $parameters);
    }

    /**
     * Returns attributes for content object publications
     *
     * @param int $type
     * @param int $identifier
     * @param Condition $condition
     *
     * @return multitype:\core\repository\publication\Attributes
     */
    public static function get_content_object_publication_attributes($identifier,
        $type = PublicationInterface :: ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, $offset = null, $order_properties = null)
    {
        switch ($type)
        {
            case PublicationInterface :: ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
                    new StaticConditionVariable($identifier));
                break;
            case PublicationInterface :: ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($identifier));
                break;
            default :
                return array();
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publication_condition));
        }
        else
        {
            $condition = $publication_condition;
        }

        $result = self :: retrieve_content_object_publications($condition);

        $publication_attributes = array();

        while ($record = $result->next_result())
        {
            $publication_attributes[] = self :: create_publication_attributes_from_record($record);
        }

        return $publication_attributes;
    }

    /**
     * Connector method with the repository, retrieves the content object publication attributes for a given content
     * object publication
     *
     * @param $publication_id
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_ID),
            new StaticConditionVariable($publication_id));
        $record = self :: record(ContentObjectPublication :: class_name(), $condition);

        return self :: create_publication_attributes_from_record($record);
    }

    /**
     * Retrieves the ids for new publications
     *
     * @param int $course_id
     * @param int $user_id
     * @param bool $is_teacher
     * @param string $tool
     * @param int $category_id
     *
     * @return mixed
     */
    public static function retrieve_new_publication_icon_ids($course_id, $user_id, $is_teacher = false, $tool = null,
        $category_id = null)
    {
        $join_conditions = array();

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_COURSE_CODE),
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID));

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_MODULE_NAME),
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL));

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_CATEGORY_ID),
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CATEGORY_ID));

        $join = new Join(CourseModuleLastAccess :: class_name(), new AndCondition($join_conditions), Join :: TYPE_LEFT);

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        if (! is_null($tool))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_TOOL),
                new StaticConditionVariable($tool));
        }

        if (! is_null($category_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($category_id));
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_HIDDEN),
            new StaticConditionVariable(0));

        $conditions_publication_period = array();

        $conditions_publication_period[] = new InequalityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_FROM_DATE),
            InequalityCondition :: LESS_THAN_OR_EQUAL,
            new StaticConditionVariable(time()));

        $conditions_publication_period[] = new InequalityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TO_DATE),
            InequalityCondition :: GREATER_THAN_OR_EQUAL,
            new StaticConditionVariable(time()));

        $condition_publication_period = new AndCondition($conditions_publication_period);

        $condition_publication_forever = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_FROM_DATE),
            new StaticConditionVariable(0));

        $conditions[] = new OrCondition($condition_publication_forever, $condition_publication_period);

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_TOOL),
                new StaticConditionVariable('home')));

        $modified_conditions = array();

        $modified_conditions[] = new InequalityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_MODIFIED_DATE),
            InequalityCondition :: GREATER_THAN_OR_EQUAL,
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_ACCESS_DATE));

        $modified_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_ACCESS_DATE),
            null);

        $conditions[] = new OrCondition($modified_conditions);

        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();

        if ($is_teacher)
        {
            $properties->add(
                new FunctionConditionVariable(
                    FunctionConditionVariable :: DISTINCT,
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_TOOL)));
        }
        else
        {
            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_ID));

            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_TOOL));

            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_CATEGORY_ID));
        }

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), new Joins(array($join)));

        return self :: records(ContentObjectPublication :: class_name(), $parameters);
    }

    /**
     * Returns the user_id with the most publications in a given course
     *
     * @static Static method
     * @param int $course_id
     * @param bool $only_teachers
     *
     * @return int
     */
    public static function get_user_with_most_publications_in_course($course_id, $only_teachers = true)
    {
        $joins = array();

        $join_conditions = array();

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID),
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID));

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID),
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_PUBLISHER_ID));

        $joins[] = new Join(
            ContentObjectPublication :: class_name(),
            new AndCondition($join_conditions),
            Join :: TYPE_LEFT);

        $conditions = array();

        if ($only_teachers)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_STATUS),
                new StaticConditionVariable(CourseUserRelation :: STATUS_TEACHER));
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();

        $properties->add(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID));

        $count_alias = 'count';

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: COUNT,
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_ID),
                $count_alias));

        $order_by = array(new OrderBy(new StaticConditionVariable($count_alias)));

        $group_by = new GroupBy();
        $group_by->add(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID));

        $parameters = new RecordRetrieveParameters($properties, $condition, $order_by, new Joins($joins), $group_by);

        $record = self :: record(CourseUserRelation :: class_name(), $parameters);

        if ($record[$count_alias] >= 0)
        {
            return $record[CourseUserRelation :: PROPERTY_USER_ID];
        }
    }

    /**
     * Retrieves your publications AND the publications you have the edit right for Otherwise there is no way to view
     * 'invisible' publications if you're not the owner, but you do have the edit right
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     * @param int $user_id
     *
     * @return RecordResultSet
     */
    public static function retrieve_my_publications($parent_location, $entities, $condition = null, $order_by = array(),
        $offset = 0, $max_objects = -1, $user_id = null)
    {
        $condition = self :: get_my_publications_condition($parent_location, $entities, $user_id, $condition);

        return self :: retrieve_content_object_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Counts your publications AND the publications you have the edit right for Otherwise there is no way to view
     * 'invisible' publications if you're not the owner, but you do have the edit right
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     *
     * @return int
     */
    public static function count_my_publications($parent_location, $entities, $condition = null, $user_id = null)
    {
        $condition = self :: get_my_publications_condition($parent_location, $entities, $user_id, $condition);

        return self :: count_content_object_publications($condition);
    }

    /**
     * Retrieves the content object publications with view right granted in category location
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     * @param int $user_id
     *
     * @return RecordResultSet
     */
    public static function retrieve_content_object_publications_with_view_right_granted_in_category_location(
        $parent_location, $entities, $condition, $order_by = array(), $offset = 0, $max_objects = -1, $user_id = null)
    {
        $condition = self :: get_content_object_publications_with_view_right_granted_in_category_location_condition(
            $parent_location,
            $entities,
            $condition,
            $user_id);

        return self :: retrieve_content_object_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Counts the content object publications with view right granted in category location
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     *
     * @return RecordResultSet
     */
    public static function count_content_object_publications_with_view_right_granted_in_category_location(
        $parent_location, $entities, $condition, $user_id = null)
    {
        $condition = self :: get_content_object_publications_with_view_right_granted_in_category_location_condition(
            $parent_location,
            $entities,
            $condition,
            $user_id);

        return self :: count_content_object_publications($condition);
    }

    /**
     * **************************************************************************************************************
     * ContentObjectPublication Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the joins for the content object publication with the content object table
     *
     * @return \libraries\storage\Joins
     */
    protected static function get_content_object_publication_joins()
    {
        $joins = array();

        $joins[] = new Join(
            ContentObject :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID)));

        return new Joins($joins);
    }

    /**
     * Creates a publication attributes object from a given record
     *
     * @param $record
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    protected static function create_publication_attributes_from_record($record)
    {
        $attributes = new \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes();

        $attributes->set_id($record[ContentObjectPublication :: PROPERTY_ID]);
        $attributes->set_publisher_id($record[ContentObjectPublication :: PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[ContentObjectPublication :: PROPERTY_PUBLICATION_DATE]);
        $attributes->set_application(__NAMESPACE__);

        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course :: class_name(),
            $record[ContentObjectPublication :: PROPERTY_COURSE_ID]);

        $location = $course->get_title() . ' (' . $course->get_visual_code() . ') > ' . Translation :: get(
            'TypeName',
            null,
            'Chamilo\Application\Weblcms\Tool\\' . $record[ContentObjectPublication :: PROPERTY_TOOL]);

        $attributes->set_location($location);

        $url = 'index.php?application=weblcms&amp;go=' . Manager :: ACTION_VIEW_COURSE . '&course=' .
             $record[ContentObjectPublication :: PROPERTY_COURSE_ID] . '&amp;tool=' .
             $record[ContentObjectPublication :: PROPERTY_TOOL] . '&amp;tool_action=' .
             \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW . '&amp;' .
             \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID . '=' . $attributes->get_id();

        $attributes->set_url($url);
        $attributes->set_title($record[ContentObject :: PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

        return $attributes;
    }

    /**
     * Returns the condition for my publications
     *
     * @static Static method
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param int $user_id
     * @param Condition $condition
     *
     * @return Condition
     */
    protected static function get_my_publications_condition($parent_location, $entities, $user_id = null, $condition = null)
    {
        $conditions = self :: get_publication_conditions_with_right(
            WeblcmsRights :: EDIT_RIGHT,
            $parent_location,
            $entities,
            $user_id,
            $condition);

        if (is_array($conditions) && count($conditions) > 0)
        {
            return new OrCondition($conditions);
        }
    }

    /**
     * Retrieves the conditions for the content object publications with view right granted
     *
     * @static Static method
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     *
     * @return Condition
     */
    protected static function get_content_object_publications_with_view_right_granted_in_category_location_condition(
        $parent_location, $entities, $condition, $user_id = null)
    {
        $conditions = self :: get_publication_conditions_with_right(
            WeblcmsRights :: VIEW_RIGHT,
            $parent_location,
            $entities,
            $user_id,
            $condition);

        if (is_array($conditions) && count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /**
     * Returns the conditions for the content object publications for a given right
     *
     * @static Static method
     * @param int $right
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param int $user_id
     * @param Condition $condition
     *
     * @return Condition
     */
    protected static function get_publication_conditions_with_right($right, $parent_location, $entities, $user_id,
        $condition)
    {
        $conditions = array();

        $granted_location_ids = WeblcmsRights :: get_instance()->get_identifiers_with_right_granted(
            $right,
            Manager :: context(),
            $parent_location,
            WeblcmsRights :: TYPE_PUBLICATION,
            $user_id,
            $entities);

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_ID),
            $granted_location_ids);

        if ($condition)
        {
            $conditions[] = $condition;
        }

        return $conditions;
    }

    /**
     * Builds some conditions based on a few given parameters
     *
     * @static Static method
     * @param Course $course
     * @param \core\user\storage\data_class\User $user
     * @param string $tool
     * @param string $content_object_type
     *
     * @return \libraries\storage\AndCondition
     */
    public static function get_publications_condition(Course $course, User $user, $tool = null, $content_object_type = null)
    {
        $conditions = array();

        if ((! $course->is_course_admin($user)))
        {
            $time_conditions = array();

            $time_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_HIDDEN),
                new StaticConditionVariable(0));

            $from_date_variable = new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_FROM_DATE);

            $to_date_variable = new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TO_DATE);

            $forever_conditions = array();

            $forever_conditions[] = new EqualityCondition($from_date_variable, new StaticConditionVariable(0));
            $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

            $between_conditions = array();

            $between_conditions[] = new InequalityCondition(
                $from_date_variable,
                InEqualityCondition :: LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(time()));

            $between_conditions[] = new InequalityCondition(
                $to_date_variable,
                InEqualityCondition :: GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable(time()));

            $date_conditions = array();
            $date_conditions[] = new AndCondition($forever_conditions);
            $date_conditions[] = new AndCondition($between_conditions);

            $time_conditions[] = new OrCondition($date_conditions);

            $conditions[] = new AndCondition($time_conditions);
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    /**
     * **************************************************************************************************************
     * New publications functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks the caching
     *
     * @var bool
     */
    private static $is_cached;

    /**
     * The new publications cache
     *
     * @var bool[string]
     */
    private static $new_publications_cache;

    /**
     * Determines if a tool has new publications since the last time the current user visited the tool.
     *
     * @param string $tool string
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    public static function tool_has_new_publications($tool, User $user, Course $course = null)
    {
        $key = $user->get_id() . $course->get_id();

        if (! isset(self :: $is_cached[$key]))
        {
            self :: $is_cached[$key] = true;

            if (! $course || $course->get_id() == 0)
            {
                return false;
            }

            $weblcms_rights = WeblcmsRights :: get_instance();

            if ($course->is_course_admin($user))
            {
                $tools_with_new_publications = DataManager :: retrieve_new_publication_icon_ids(
                    $course->get_id(),
                    $user->get_id(),
                    true);

                while ($publication = $tools_with_new_publications->next_result(false))
                {
                    self :: $new_publications_cache[$key][$publication[ContentObjectPublication :: PROPERTY_TOOL]] = true;
                }
            }
            else
            {
                $publications = DataManager :: retrieve_new_publication_icon_ids($course->get_id(), $user->get_id());

                while ($publication = $publications->next_result(false))
                {
                    if (! isset(
                        self :: $new_publications_cache[$key][$publication[ContentObjectPublication :: PROPERTY_TOOL]]) && $weblcms_rights->is_allowed_in_courses_subtree(
                        WeblcmsRights :: VIEW_RIGHT,
                        $publication[ContentObjectPublication :: PROPERTY_ID],
                        WeblcmsRights :: TYPE_PUBLICATION,
                        $course->get_id()))
                    {
                        // check if the publication is visible
                        $visible = true;
                        if ($publication[ContentObjectPublication :: PROPERTY_CATEGORY_ID] != 0)
                        {
                            // categories can be made invisible
                            $category = DataManager :: retrieve_by_id(
                                ContentObjectPublicationCategory :: class_name(),
                                $publication[ContentObjectPublication :: PROPERTY_CATEGORY_ID]);

                            $visible = $category->is_recursive_visible();
                        }
                        if ($visible)
                        {
                            self :: $new_publications_cache[$key][$publication[ContentObjectPublication :: PROPERTY_TOOL]] = true;
                        }
                    }
                }
            }
        }

        return self :: $new_publications_cache[$key][$tool];
    }

    /**
     * Returns if a category inside a tool has new publications
     *
     * @param string $tool type
     * @param User $user User
     * @param Course $course Course
     * @param string $category
     *
     * @return bool
     */
    public static function tool_category_has_new_publications($tool, User $user, Course $course, $category)
    {
        if ($course->is_course_admin($user))
        {
            $tools_with_new_publications = DataManager :: retrieve_new_publication_icon_ids(
                $course->get_id(),
                $user->get_id(),
                true,
                $tool,
                $category);
            if ($tools_with_new_publications->size() > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $weblcms_rights = WeblcmsRights :: get_instance();

            $publications = DataManager :: retrieve_new_publication_icon_ids(
                $course->get_id(),
                $user->get_id(),
                false,
                $tool,
                $category);

            while ($publication = $publications->next_result(false))
            {
                if ($weblcms_rights->is_allowed_in_courses_subtree(
                    WeblcmsRights :: VIEW_RIGHT,
                    $publication[ContentObjectPublication :: PROPERTY_ID],
                    WeblcmsRights :: TYPE_PUBLICATION,
                    $course->get_id()))
                {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * **************************************************************************************************************
     * CourseModuleLastAccess Functionality *
     * **************************************************************************************************************
     */

    /**
     * Logs a course module last access record
     *
     * @param int $course_id
     * @param int $user_id
     * @param string $module_name
     * @param int $category_id
     *
     * @return bool
     */
    public static function log_course_module_access($course_id, $user_id, $module_name = null, $category_id = 0)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_MODULE_NAME),
            new StaticConditionVariable($module_name));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($category_id));

        $condition = new AndCondition($conditions);

        $course_module_last_access = self :: retrieve(
            CourseModuleLastAccess :: class_name(),
            new DataClassRetrieveParameters($condition));

        if (! $course_module_last_access)
        {
            $course_module_last_access = new CourseModuleLastAccess();

            $course_module_last_access->set_course_code($course_id);
            $course_module_last_access->set_user_id($user_id);
            $course_module_last_access->set_module_name($module_name);
            $course_module_last_access->set_category_id($category_id);
        }

        $course_module_last_access->set_access_date(time());

        return $course_module_last_access->save();
    }

    /**
     * Returns the last visit date per course (and optional for a module and / or a user)
     *
     * @param int $course_id
     * @param string $module_name
     * @param int $user_id
     *
     * @return int
     */
    public static function get_last_visit_date_per_course($course_id, $module_name = null, $user_id = null)
    {
        return self :: get_last_visit_date($course_id, $user_id, $module_name, null);
    }

    /**
     * Returns the last visit date
     *
     * @param int $course_id
     * @param int|null $user_id
     * @param string|null $module_name
     * @param int|null $category_id
     *
     * @return int
     */
    public static function get_last_visit_date($course_id, $user_id, $module_name = null, $category_id = 0)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_id));

        if (! is_null($user_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess :: class_name(),
                    CourseModuleLastAccess :: PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
        }

        if (! is_null($category_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess :: class_name(),
                    CourseModuleLastAccess :: PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($category_id));
        }

        if (! is_null($module_name))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess :: class_name(),
                    CourseModuleLastAccess :: PROPERTY_MODULE_NAME),
                new StaticConditionVariable($module_name));
        }

        $condition = new AndCondition($conditions);

        $order_by = new OrderBy(
            new PropertyConditionVariable(
                CourseModuleLastAccess :: class_name(),
                CourseModuleLastAccess :: PROPERTY_ACCESS_DATE));

        $course_module_access = self :: retrieve(
            CourseModuleLastAccess :: class_name(),
            new DataClassRetrieveParameters($condition, $order_by));

        if (! $course_module_access)
        {
            return 0;
        }
        else
        {
            return $course_module_access->get_access_date();
        }
    }

    /**
     * **************************************************************************************************************
     * CourseTypeUserCategory Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves a course type user category at sort
     *
     * @param int $user_id
     * @param int $course_type_id
     * @param int $sort
     * @param string $direction
     *
     * @return CourseTypeUserCategory
     */
    public static function retrieve_course_type_user_category_at_sort($user_id, $course_type_id, $sort, $direction)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID),
            new StaticConditionVariable($course_type_id));

        if ($direction == 'up')
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory :: class_name(),
                    CourseTypeUserCategory :: PROPERTY_SORT),
                ComparisonCondition :: LESS_THAN,
                new StaticConditionVariable($sort));

            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory :: class_name(),
                    CourseTypeUserCategory :: PROPERTY_SORT),
                ComparisonCondition :: GREATER_THAN,
                new StaticConditionVariable($sort));

            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);
        $object_table_order = new OrderBy(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_SORT),
            $order_direction);

        return self :: retrieve(
            CourseTypeUserCategory :: class_name(),
            new DataClassRetrieveParameters($condition, $object_table_order));
    }

    /**
     * Retrieves the course user categories from a given course type
     *
     * @static Static method
     * @param int $course_type_id
     * @param int $user_id
     *
     * @return \libraries\storage\RecordResultSet
     */
    public static function retrieve_course_user_categories_from_course_type($course_type_id, $user_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID),
            new StaticConditionVariable($course_type_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(CourseTypeUserCategory :: class_name()));

        $properties->add(
            new PropertyConditionVariable(CourseUserCategory :: class_name(), CourseUserCategory :: PROPERTY_TITLE));

        $join_condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory :: class_name(),
                CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID),
            new PropertyConditionVariable(CourseUserCategory :: class_name(), CourseUserCategory :: PROPERTY_ID));

        $joins = new Joins();
        $joins->add(new Join(CourseUserCategory :: class_name(), $join_condition));

        $order_by = array(
            new OrderBy(
                new PropertyConditionVariable(
                    CourseTypeUserCategory :: class_name(),
                    CourseTypeUserCategory :: PROPERTY_SORT)));

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, $order_by, $joins);

        return self :: records(CourseTypeUserCategory :: class_name(), $parameters);
    }

    /**
     * Fixes the course type user category rel course display orders for deleted courses for a given user Retrieves all
     * the coures type user category rel course objects for which the course does not exist anymore and removes them and
     * fixes the display orders
     *
     * @param $user_id int
     *
     * @return boolean
     */
    public static function fix_course_type_user_category_rel_course_for_user($user_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse :: class_name(),
                CourseTypeUserCategoryRelCourse :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID),
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID),
                Course :: get_table_name()));

        $condition = new AndCondition($conditions);

        $result_set = DataManager :: retrieves(CourseTypeUserCategoryRelCourse :: class_name(), $condition);

        while ($course_type_user_category_rel_course = $result_set->next_result())
        {
            if (! $course_type_user_category_rel_course->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * **************************************************************************************************************
     * CourseCategory Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the course categories ordered by name
     *
     * @param \libraries\storage\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     *
     * @return \libraries\storage\ResultSet<CourseCategory>
     */
    public static function retrieve_course_categories_ordered_by_name($condition = null, $offset = null, $count = null,
        $order_by = array())
    {
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_NAME));

        return DataManager :: retrieves(
            CourseCategory :: class_name(),
            new DataClassRetrievesParameters($condition, $offset, $count, $order_by));
    }

    public static function retrieve_course_category_by_code($category_code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_CODE),
            new StaticConditionVariable($category_code));

        return self :: retrieve(CourseCategory :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * **************************************************************************************************************
     * CourseSettings Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the course_settings joined with the course_tool (name property)
     *
     * @param $condition Condition
     * @param $offset int
     * @param $count int
     * @param $order_by int
     *
     * @return ResultSet
     */
    public static function retrieve_course_settings_with_tools($condition = null, $offset = null, $count = null, $order_by = null)
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable(CourseSetting :: class_name()));

        $properties->add(
            new FixedPropertyConditionVariable(
                CourseTool :: class_name(),
                CourseTool :: PROPERTY_NAME,
                CourseSetting :: PROPERTY_COURSE_TOOL_NAME));

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseTool :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(CourseSetting :: class_name(), CourseSetting :: PROPERTY_TOOL_ID),
                    new PropertyConditionVariable(CourseTool :: class_name(), CourseTool :: PROPERTY_ID)),
                Join :: TYPE_LEFT));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $order_by, $joins);

        return self :: records(CourseSetting :: class_name(), $parameters);
    }

    /**
     * Retrieves the course settings joined with the course setting default values
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_by
     *
     * @return RecordResultSet
     */
    public static function retrieve_course_settings_with_default_values($condition = null, $offset = null, $count = null,
        $order_by = array())
    {
        $data_class_properties = array();

        $data_class_properties[] = new PropertiesConditionVariable(CourseSetting :: class_name());

        $data_class_properties[] = new PropertyConditionVariable(
            CourseSettingDefaultValue :: class_name(),
            CourseSettingDefaultValue :: PROPERTY_VALUE);

        $joins = array();

        $joins[] = new Join(
            CourseSettingDefaultValue :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(CourseSetting :: class_name(), CourseSetting :: PROPERTY_ID),
                new PropertyConditionVariable(
                    CourseSettingDefaultValue :: class_name(),
                    CourseSettingDefaultValue :: PROPERTY_COURSE_SETTING_ID)));

        $parameters = new RecordRetrievesParameters(
            new DataClassProperties($data_class_properties),
            $condition,
            $count,
            $offset,
            $order_by,
            new Joins($joins));

        return self :: records(CourseSetting :: class_name(), $parameters);
    }

    /**
     * Retrieves the course settings joined with the course type setting values
     *
     * @param int $course_type_id
     *
     * @return RecordResultSet
     */
    public static function retrieve_course_settings_with_course_type_values($course_type_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeRelCourseSetting :: class_name(),
                CourseTypeRelCourseSetting :: PROPERTY_COURSE_TYPE_ID),
            new StaticConditionVariable($course_type_id));

        $parameters = self :: get_course_settings_with_values_parameters(
            CourseTypeRelCourseSetting :: class_name(),
            CourseTypeRelCourseSettingValue :: class_name(),
            CourseTypeRelCourseSetting :: PROPERTY_COURSE_SETTING_ID,
            CourseTypeRelCourseSettingValue :: PROPERTY_COURSE_TYPE_REL_COURSE_SETTING_ID,
            $condition);

        return self :: records(CourseSetting :: class_name(), $parameters);
    }

    /**
     * Retrieves the course settings joined with the course setting values
     *
     * @param int $course_id
     *
     * @return RecordResultSet
     */
    public static function retrieve_course_settings_with_course_values($course_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseRelCourseSetting :: class_name(),
                CourseRelCourseSetting :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $parameters = self :: get_course_settings_with_values_parameters(
            CourseRelCourseSetting :: class_name(),
            CourseRelCourseSettingValue :: class_name(),
            CourseRelCourseSetting :: PROPERTY_COURSE_SETTING_ID,
            CourseRelCourseSettingValue :: PROPERTY_COURSE_REL_COURSE_SETTING_ID,
            $condition);

        return self :: records(CourseSetting :: class_name(), $parameters);
    }

    /**
     * **************************************************************************************************************
     * Course Settings Helper Functions *
     * **************************************************************************************************************
     */

    /**
     * Builds the parameters to retrieve course settings with a course setting relation table.
     * Returns course settings
     * with their compliant values.
     *
     * @param $course_setting_relation_class String - The class name for the course setting relation table
     * @param $course_setting_relation_value_class String - The class name for the course setting relation value table
     * @param $course_setting_foreign_property String - The foreign property for the course setting id
     * @param $course_setting_relation_foreign_property String - The foreign property for the course setting relation id
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_by
     *
     * @return RecordRetrievesParameters
     */
    private static function get_course_settings_with_values_parameters($course_setting_relation_class,
        $course_setting_relation_value_class, $course_setting_foreign_property,
        $course_setting_relation_foreign_property, $condition = null, $offset = null, $count = null, $order_by = array())
    {
        $data_class_properties = array();

        $data_class_properties[] = new PropertiesConditionVariable(CourseSetting :: class_name());

        $data_class_properties[] = new PropertyConditionVariable(
            $course_setting_relation_value_class,
            $course_setting_relation_value_class :: PROPERTY_VALUE);

        $joins = array();

        $joins[] = new Join(
            $course_setting_relation_class :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(CourseSetting :: class_name(), CourseSetting :: PROPERTY_ID),
                new PropertyConditionVariable($course_setting_relation_class, $course_setting_foreign_property)));

        $joins[] = new Join(
            $course_setting_relation_value_class :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    $course_setting_relation_class,
                    $course_setting_relation_class :: PROPERTY_ID),
                new PropertyConditionVariable(
                    $course_setting_relation_value_class,
                    $course_setting_relation_foreign_property)));

        return new RecordRetrievesParameters(
            new DataClassProperties($data_class_properties),
            $condition,
            $offset,
            $count,
            $order_by,
            new Joins($joins));
    }

    /**
     * **************************************************************************************************************
     * CourseTool Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves a course tool by a given tool name
     *
     * @param $course_tool_name String
     *
     * @return CourseTool
     */
    public static function retrieve_course_tool_by_name($course_tool_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseTool :: class_name(), CourseTool :: PROPERTY_NAME),
            new StaticConditionVariable($course_tool_name));

        return self :: retrieve(CourseTool :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * **************************************************************************************************************
     * Content Object Publication Target Entities Functionality * TODO: Check if refactoring is possible because there
     * is some copy paste code *
     * **************************************************************************************************************
     */

    /**
     * Retrieves a course id from a given publication
     *
     * @param int $publication_id
     *
     * @return int
     *
     * @throws \libraries\architecture\ObjectNotExistException
     */
    public static function get_course_id_from_publication($publication_id)
    {
        $publication = self :: retrieve_by_id(ContentObjectPublication :: class_name(), $publication_id);

        if (! $publication)
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException(
                Translation :: get('ContentObjectPublication'),
                $publication_id);
        }

        return $publication->get_course_id();
    }

    /**
     * Retrieves the target user ids of a publication
     *
     * @param int $publication_id
     *
     * @return int[]
     */
    public static function retrieve_publication_target_user_ids($publication_id)
    {
        $course_id = self :: get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity :: ENTITY_TYPE] = false;
        }

        return $target_entities[CourseUserEntity :: ENTITY_TYPE];
    }

    /**
     * Retrieves the target course group ids of a publication
     *
     * @param int $publication_id
     *
     * @return int[]
     */
    public static function retrieve_publication_target_course_group_ids($publication_id)
    {
        $course_id = self :: get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity :: ENTITY_TYPE] = false;
        }

        return $target_entities[CourseGroupEntity :: ENTITY_TYPE];
    }

    /**
     * Retrieves the target platform group ids of a publication
     *
     * @param int $publication_id
     *
     * @return int[]
     */
    public static function retrieve_publication_target_platform_group_ids($publication_id)
    {
        $course_id = self :: get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity :: ENTITY_TYPE] = false;
        }

        return $target_entities[CoursePlatformGroupEntity :: ENTITY_TYPE];
    }

    /**
     * Retrieves the target users of a publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\ObjectTableOrder[] <\user\User>
     */
    public static function retrieve_publication_target_users($publication_id, $course_id, $offset = null, $count = null,
        $order_by = null, $condition = null)
    {
        if (is_null($course_id))
        {
            $course_id = self :: get_course_id_from_publication($publication_id);
        }

        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }

        $users_ids = array();

        foreach ($target_entities as $entity_type => $entity_ids)
        {
            switch ($entity_type)
            {
                case CourseUserEntity :: ENTITY_TYPE :
                    foreach ($entity_ids as $user_id)
                    {
                        $users_ids[$user_id] = $user_id;
                    }
                    break;

                case CourseGroupEntity :: ENTITY_TYPE :
                    $course_groups = CourseGroupDataManager :: retrieve_course_groups_and_subgroups(
                        $target_entities[CourseGroupEntity :: ENTITY_TYPE]);

                    while ($course_group = $course_groups->next_result())
                    {
                        $course_group_users = CourseGroupDataManager :: retrieve_course_group_user_ids(
                            $course_group->get_id());

                        foreach ($course_group_users as $id)
                        {
                            $users_ids[$id] = $id;
                        }
                    }
                    break;

                case CoursePlatformGroupEntity :: ENTITY_TYPE :

                    $group_condition = new InCondition(
                        new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                        $entity_ids);
                    $groups_resultset = DataManager :: retrieves(Group :: class_name(), $group_condition);

                    while ($group = $groups_resultset->next_result())
                    {
                        $user_ids_from_group = $group->get_users(true, true);
                        foreach ($user_ids_from_group as $user)
                        {
                            $users_ids[$user] = $user;
                        }
                    }
                    break;

                case 0 :

                    $course_users = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_all_course_users(
                        $course_id,
                        $condition,
                        $offset,
                        $count,
                        $order_by);

                    while ($course_user = $course_users->next_result())
                    {
                        $users_ids[$course_user[User :: PROPERTY_ID]] = $course_user[USER :: PROPERTY_ID];
                    }
            }
        }

        if (count($users_ids) == 0)
        {
            $users_ids[] = - 1;
        }

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
            $users_ids);

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $user_condition = new AndCondition($conditions);

        return \Chamilo\Core\User\Storage\DataManager :: retrieves(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            new DataClassRetrievesParameters($user_condition, $count, $offset, $order_by));
    }

    /**
     * Checks if the given user is a target user of the publication
     *
     * @param int $user_id
     * @param int $publication_id
     * @param int $course_id
     *
     * @return bool
     */
    public static function is_publication_target_user($user_id, $publication_id, $course_id = null)
    {
        if (is_null($course_id))
        {
            $course_id = self :: get_course_id_from_publication($publication_id);
        }

        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }

        if (count($target_entities) == 0)
        {
            return true;
        }

        foreach ($target_entities as $entity_type => $entity_ids)
        {
            switch ($entity_type)
            {
                case CourseUserEntity :: ENTITY_TYPE :
                    foreach ($entity_ids as $uid)
                    {
                        if ($user_id == $uid)
                        {
                            return true;
                        }
                    }
                    break;

                case CourseGroupEntity :: ENTITY_TYPE :
                    foreach ($entity_ids as $course_group_id)
                    {
                        $course_group = self :: retrieve_by_id(CourseGroup :: class_name(), $course_group_id);

                        $course_group_users = CourseGroupDataManager :: retrieve_course_group_user_ids(
                            $course_group->get_id());

                        foreach ($course_group_users as $id)
                        {
                            if ($user_id == $id)
                            {
                                return true;
                            }
                        }
                    }
                    break;

                case CoursePlatformGroupEntity :: ENTITY_TYPE :
                    $groups_resultset = \Chamilo\Core\Group\Storage\DataManager :: retrieve_groups_and_subgroups(
                        $entity_ids);

                    while ($group = $groups_resultset->next_result())
                    {
                        $condition = new EqualityCondition(
                            new PropertyConditionVariable(
                                GroupRelUser :: class_name(),
                                GroupRelUser :: PROPERTY_GROUP_ID),
                            new StaticConditionVariable($group->get_id()));

                        $group_user_rels = \Chamilo\Core\Group\Storage\DataManager :: retrieves(
                            GroupRelUser :: class_name(),
                            $condition);
                        while ($group_user_rel = $group_user_rels->next_result())
                        {
                            if ($user_id == $group_user_rel->get_user_id())
                            {
                                return true;
                            }
                        }
                    }
                    break;

                case 0 :
                    return true;
            }
        }
    }

    /**
     * Retrieves the target course groups for a given publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet<CourseGroup>
     */
    public static function retrieve_publication_target_course_groups($publication_id, $course_id, $offset = null,
        $count = null, $order_by = null, $condition = null)
    {
        if (is_null($course_id))
        {
            $course_id = self :: get_course_id_from_publication($publication_id);
        }
        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }

        if ($target_entities[0])
        {
            $conditions = array();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
                new StaticConditionVariable($course_id));
            $conditions[] = new InequalityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                InequalityCondition :: GREATER_THAN,
                new StaticConditionVariable(0));

            if ($condition)
            {
                $conditions[] = $condition;
            }

            $group_condition = new AndCondition($conditions);
        }
        else
        {
            return CourseGroupDataManager :: retrieve_course_groups_and_subgroups(
                $target_entities[CourseGroupEntity :: ENTITY_TYPE],
                $condition,
                $offset,
                $count,
                $order_by);
        }

        return self :: retrieves(
            CourseGroup :: class_name(),
            new DataClassRetrievesParameters($group_condition, $count, $offset, $order_by));
    }

    /**
     * Retrieves the target platform groups for a given publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet<\group\Group>
     */
    public static function retrieve_publication_target_platform_groups($publication_id, $course_id, $offset = null,
        $count = null, $order_by = null, $condition = null)
    {
        if (is_null($course_id))
        {
            $course_id = self :: get_course_id_from_publication($publication_id);
        }
        try
        {
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }

        if ($target_entities[0])
        {
            $cgr_condition = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupRelation :: class_name(),
                    CourseGroupRelation :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($course_id));

            $cgr_resultset = $course_group_relations = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
                CourseGroupRelation :: class_name(),
                $cgr_condition);

            $group_ids = array();

            while ($course_group_rel = $cgr_resultset->next_result())
            {
                $group_ids[] = $course_group_rel->get_group_id();
            }

            return \Chamilo\Core\Group\Storage\DataManager :: retrieve_groups_and_subgroups(
                $group_ids,
                $condition,
                $count,
                $offset,
                $order_by);
        }

        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_groups_and_subgroups(
            $target_entities[CoursePlatformGroupEntity :: ENTITY_TYPE],
            $condition,
            $count,
            $offset,
            $order_by);
    }

    /**
     * Returns an array of users that are the targets of the given publication based on the rights structure
     *
     * @param $publication ContentObjectPublication
     *
     * @return array
     */
    public static function get_publication_target_users($publication)
    {
        $target_users = array();
        $publication_id = $publication->get_id();
        $course_id = $publication->get_course_id();

        try
        {
            // get the entities
            $target_entities = WeblcmsRights :: get_instance()->get_target_entities(
                WeblcmsRights :: VIEW_RIGHT,
                Manager :: context(),
                $publication_id,
                WeblcmsRights :: TYPE_PUBLICATION,
                $course_id,
                WeblcmsRights :: TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }

        // check for everybody
        if (array_key_exists(0, $target_entities[0]))
        {
            // get all course users
            $target_users = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_all_course_users(
                $course_id)->as_array();
        }
        else
        {
            $user_ids = array();

            // get all users for all entities
            foreach ($target_entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case CoursePlatformGroupEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                                Group :: class_name(),
                                $group_id);
                            if ($group)
                            {
                                $user_ids = array_merge($user_ids, $group->get_users(true, true));
                            }
                        }
                        break;
                    case CourseUserEntity :: ENTITY_TYPE :
                        $user_ids = array_merge($user_ids, $entity_ids);
                        break;
                    case CourseGroupEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager :: retrieve_by_id(
                                CourseGroup :: class_name(),
                                $course_group_id);

                            if ($course_group)
                            {
                                $user_ids = array_merge($user_ids, $course_group->get_members(true, true, false));
                            }
                        }
                        break;
                }
            }
        }

        if (count($user_ids) > 0)
        {
            $users = \Chamilo\Core\User\Storage\DataManager :: records(
                User :: class_name(),
                new InCondition(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), $user_ids))->as_array();

            foreach ($users as $user)
            {
                if (! array_key_exists($user[User :: PROPERTY_ID], $target_users))
                {
                    $target_users[$user[User :: PROPERTY_ID]] = $user;
                }
            }
        }

        return $target_users;
    }

    /**
     * Returns an array of users that are the targets of the publication identified by the given ID based on the rights
     * structure
     *
     * @param $publication_id int
     *
     * @return array
     */
    public static function get_publication_target_users_by_publication_id($publication_id)
    {
        $publication = DataManager :: retrieve_by_id(ContentObjectPublication :: class_name(), $publication_id);
        if (! $publication)
        {
            return array();
        }

        return self :: get_publication_target_users($publication);
    }

    /**
     * **************************************************************************************************************
     * Introduction Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves an introduction from a given condition
     *
     * @static Static method
     * @param Condition $condition
     *
     * @return mixed[string]
     */
    public static function retrieve_introduction_publication($condition)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                ContentObject :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID))));

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            new StaticConditionVariable(Introduction :: class_name()));

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition, array(), $joins);

        return self :: retrieve(ContentObjectPublication :: class_name(), $parameters);
    }

    /**
     * **************************************************************************************************************
     * RightsLocationLockedRight Functionality *
     * **************************************************************************************************************
     */

    /**
     * Clears the locked rights for a given location id (an optionally a given right id)
     *
     * @param $location_id int
     * @param $right_id int - [OPTIONAL] default null
     * @return boolean
     */
    public static function clear_locked_rights_for_location($location_id, $right_id = null)
    {
        return self :: deletes(
            RightsLocationLockedRight :: class_name(),
            self :: get_rights_location_locked_right_condition($location_id, $right_id));
    }

    /**
     * Is the given right locked for the given location
     *
     * @param $location_id int
     * @param $right_id int
     *
     * @return boolean
     */
    public static function is_right_locked_for_location($location_id, $right_id)
    {
        return self :: count(
            RightsLocationLockedRight :: class_name(),
            self :: get_rights_location_locked_right_condition($location_id, $right_id)) > 0;
    }

    /**
     * **************************************************************************************************************
     * RightsLocationLockedRight Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Creates a condition for the rights location locked right class with a location and right id
     *
     * @static Static method
     * @param int $location_id
     * @param int $right_id
     *
     * @return \libraries\storage\AndCondition
     */
    protected static function get_rights_location_locked_right_condition($location_id, $right_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationLockedRight :: class_name(),
                RightsLocationLockedRight :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id));

        if (! is_null($right_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationLockedRight :: class_name(),
                    RightsLocationLockedRight :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));
        }

        return new AndCondition($conditions);
    }

    /**
     * **************************************************************************************************************
     * CourseRequest Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns whether or not there is a pending request for a given user and course
     *
     * @param $user_id int
     * @param $course_id int
     *
     * @return boolean
     */
    public static function is_user_requested_for_course($user_id, $course_id)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest :: class_name(), CourseRequest :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest :: class_name(), CourseRequest :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest :: class_name(), CourseRequest :: PROPERTY_DECISION),
            new StaticConditionVariable(CourseRequest :: NO_DECISION));

        $condition = new AndCondition($conditions);

        return (self :: count(CourseRequest :: class_name(), $condition) > 0);
    }

    public static function retrieve_all_courses_with_course_categories($user_id, $group_ids)
    {
        // First: retrieve the ids of courses the user is subscribed to individually.
        $properties = new DataClassProperties();

        $properties->add(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $directly_subscribed_course_ids = self :: records(CourseUserRelation :: class_name(), $parameters);

        $course_ids = array();

        foreach ($directly_subscribed_course_ids->as_array() as $course_id)
        {
            $course_ids[] = $course_id[CourseUserRelation :: PROPERTY_COURSE_ID];
        }

        // Second, if any groups have been specified, retrieve any *additional* courses these groups are subscribed to.
        if (! $group_ids instanceof EmptyResultSet && count($group_ids) > 0)
        {
            $properties = new DataClassProperties();

            $properties->add(
                new PropertyConditionVariable(
                    CourseGroupRelation :: class_name(),
                    CourseGroupRelation :: PROPERTY_COURSE_ID));

            $conditions = array();

            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    CourseGroupRelation :: class_name(),
                    CourseGroupRelation :: PROPERTY_GROUP_ID),
                $group_ids);

            if (count($course_ids) > 0) // Exclude the courses we already know about
            {
                $conditions[] = new NotCondition(
                    new InCondition(
                        new PropertyConditionVariable(
                            CourseGroupRelation :: class_name(),
                            CourseGroupRelation :: PROPERTY_COURSE_ID),
                        $course_ids));
            }

            $condition = new AndCondition($conditions);

            $parameters = new RecordRetrievesParameters($properties, $condition);

            $indirectly_subscribed_course_ids = self :: records(CourseGroupRelation :: class_name(), $parameters);

            foreach ($indirectly_subscribed_course_ids->as_array() as $course_id)
            {
                $course_ids[] = $course_id[CourseUserRelation :: PROPERTY_COURSE_ID];
            }
        }

        // Finally, retrieve information about the course, as well as labels the user applied to allow sorting the
        // courses
        if (count($course_ids) > 0)
        {
            $properties = new DataClassProperties();

            $properties->add(new PropertiesConditionVariable(Course :: class_name()));
            $properties->add(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID));
            $properties->add(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_SORT));

            $join_conditions = array();

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));

            $join_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_ID),
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID));

            $join = new Join(
                CourseTypeUserCategoryRelCourse :: class_name(),
                new AndCondition($join_conditions),
                Join :: TYPE_LEFT);

            $conditions = array();

            $conditions[] = new InCondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID),
                $course_ids);

            $order_by = array();

            $order_by[] = new OrderBy(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_SORT),
                SORT_ASC,
                self :: get_alias(CourseTypeUserCategoryRelCourse :: get_table_name()));

            $order_by[] = new OrderBy(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_TITLE),
                SORT_ASC,
                self :: get_alias(Course :: get_table_name()));

            $parameters = new RecordRetrievesParameters($properties, new AndCondition($conditions), /* $count = */
                null, /* $offset = */
                null, $order_by, new Joins(array($join)));

            $courses = self :: records(Course :: class_name(), $parameters);

            return $courses;
        }

        return new RecordResultSet(array());
    }
}
