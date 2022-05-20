<?php

namespace Chamilo\Application\Weblcms\Storage;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeRelCourseSetting;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service\PublicationModifier;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
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
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
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
     * Checks if any of the content objects are published
     *
     * @param int[] $object_ids
     *
     * @return bool
     */
    public static function areContentObjectsPublished($object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ), $object_ids
        );

        return self::count(ContentObjectPublication::class, new DataClassCountParameters($condition)) >= 1;
    }

    /**
     * Clears the locked rights for a given location id (an optionally a given right id)
     *
     * @param $location_id int
     * @param $right_id int - [OPTIONAL] default null
     *
     * @return boolean
     */
    public static function clear_locked_rights_for_location($location_id, $right_id = null)
    {
        return self::deletes(
            RightsLocationLockedRight::class, self::get_rights_location_locked_right_condition($location_id, $right_id)
        );
    }

    /**
     * Counts the number of content object publications
     *
     * @param int $attributes_type
     * @param int $identifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public static function countPublicationAttributes(
        $attributes_type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
        switch ($attributes_type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                    ), new StaticConditionVariable($identifier)
                );
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
                    ), new StaticConditionVariable($identifier)
                );
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

        $parameters = new DataClassCountParameters($condition, self::get_content_object_publication_joins());

        return self::count(ContentObjectPublication::class, $parameters);
    }

    /**
     * Counts content object publications
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public static function count_content_object_publications($condition)
    {
        $parameters = new DataClassCountParameters($condition, self::get_content_object_publication_joins());

        return self::count(ContentObjectPublication::class, $parameters);
    }

    /**
     * Counts the content object publications with view right granted in category location
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     *
     * @return integer
     */
    public static function count_content_object_publications_with_view_right_granted_in_category_location(
        $parent_location, $entities, $condition, $user_id = null
    )
    {
        $condition = self::get_content_object_publications_with_view_right_granted_in_category_location_condition(
            $parent_location, $entities, $condition, $user_id
        );

        return self::count_content_object_publications($condition);
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
        $condition = self::get_my_publications_condition($parent_location, $entities, $user_id, $condition);

        return self::count_content_object_publications($condition);
    }

    /**
     * \brief Creates an array of courses where the keys are course ID's and the values are course instances.
     *
     * @param $courses Course instance or an array of course instances.
     */
    public static function create_courses_array($courses)
    {
        $courses_with_ids = [];

        foreach ($courses as $course)
        {
            $courses_with_ids[$course->get_id()] = $course;
        }

        return $courses_with_ids;
    }

    /**
     * Creates the key for the cache new_publications_cache.
     */
    private static function create_new_publications_cache_key($user_id, $course_id)
    {
        return $user_id . '_' . $course_id;
    }

    /**
     * Creates a publication attributes object from a given record
     *
     * @param $record
     *
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    protected static function create_publication_attributes_from_record($record)
    {
        $attributes = new Attributes();

        $attributes->set_id($record[ContentObjectPublication::PROPERTY_ID]);
        $attributes->set_publisher_id($record[ContentObjectPublication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[ContentObjectPublication::PROPERTY_PUBLICATION_DATE]);
        $attributes->set_application('Chamilo\Application\Weblcms');

        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
            Course::class, $record[ContentObjectPublication::PROPERTY_COURSE_ID]
        );

        $location = $course->get_title() . ' (' . $course->get_visual_code() . ') > ' . Translation::get(
                'TypeName', null,
                'Chamilo\Application\Weblcms\Tool\Implementation\\' . $record[ContentObjectPublication::PROPERTY_TOOL]
            );

        $attributes->set_location($location);

        $url = 'index.php?application=Chamilo\Application\Weblcms&amp;go=' . Manager::ACTION_VIEW_COURSE . '&course=' .
            $record[ContentObjectPublication::PROPERTY_COURSE_ID] . '&amp;tool=' .
            $record[ContentObjectPublication::PROPERTY_TOOL] . '&amp;tool_action=' .
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW . '&amp;' .
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID . '=' . $attributes->get_id();

        $attributes->set_url($url);
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
        $attributes->setModifierServiceIdentifier(
            PublicationModifier::class
        );

        return $attributes;
    }

    /**
     * Delete the content object publications for given content object id
     *
     * @param int $object_id
     *
     * @return bool
     */
    public static function deleteContentObjectPublications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($object_id)
        );
        $publications = self::retrieves(
            ContentObjectPublication::class, new DataClassRetrievesParameters($condition)
        );

        foreach ($publications as $publication)
        {
            if (!$publication->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes all the course type user category rel course objects for a given course id
     *
     * @param int $course_id
     *
     * @return bool
     */
    public static function delete_course_type_user_category_rel_courses_by_course_id($course_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class, CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );

        $result_set = DataManager::retrieves(
            CourseTypeUserCategoryRelCourse::class, new DataClassRetrievesParameters($condition)
        );

        foreach ($result_set as $course_type_user_category_rel_course)
        {
            if (!$course_type_user_category_rel_course->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Fills the $new_publications_cache cache with given courses for given user.
     * Ideas:
     * - In order to speed multiple calls to tool_has_new_publications(...) we use the cache $new_publications_cache.
     * If
     * the cache is not
     * filled for a user and course pair yet, this function is activated automatically.
     * - Further acceleration of the code can be achieved if this function is called with a list of courses before
     * looping over the courses
     * starts. Why? It is more efficient to execute large queries including several 100 courses than executing small
     * queries for each
     * course separately. This function fills the cache for all given courses, resulting in fast execution of
     * subsequent
     * calls to
     * tool_has_new_publications.
     * Steps:
     * -# Retrieve all tools with new publications for all courses. @param array $courses mapping of course ID's onto
     * course objects @see create_courses_array($courses).
     * @see RighsUtils::
     * filter_location_identifiers_by_granted_right(...)
     * -# Filter out all publication whose category is not visible. @see
     * retrieve_publication_category_parent_ids_recursive(...),
     * retrieve_publication_category_visibility(...), and ContentObjectPublicationCategory::
     * is_recursive_visible_on_arrays(...).
     * -# Fill cache $new_publications_cache with the remaining publications.
     *
     * @see DataManager::
     * retrieve_new_publication_icon_ids
     * -# Filter out all publications which user has no access right to.
     */
    public static function fill_new_publications_cache($user, $courses)
    {
        $weblcms_rights = WeblcmsRights::getInstance();

        foreach (array_keys($courses) as $course_id)
        {
            self::$is_cached[self::create_new_publications_cache_key($user->get_id(), $course_id)] = true;
        }

        $tools_with_new_publications = DataManager::retrieve_new_publication_icon_ids(
            array_keys($courses), $user->get_id()
        );

        $identifiers = [];
        $publications = [];

        foreach ($tools_with_new_publications as $publication)
        {
            $course = $courses[$publication[ContentObjectPublication::PROPERTY_COURSE_ID]];

            if ($course->is_course_admin($user))
            {
                $key = self::create_new_publications_cache_key(
                    $user->get_id(), $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
                );

                self::$new_publications_cache[$key][$publication[ContentObjectPublication::PROPERTY_TOOL]] = true;
            }
            else
            {
                $identifiers[] = $publication[ContentObjectPublication::PROPERTY_ID];
                $publications[$publication[ContentObjectPublication::PROPERTY_ID]] = $publication;
            }
        }

        $entities = [];

        $entities[] = CourseUserEntity::getInstance();
        $entities[] = CourseGroupEntity::getInstance(null);
        $entities[] = CoursePlatformGroupEntity::getInstance(null);

        $publication_ids_with_right_view = $weblcms_rights->filter_location_identifiers_by_granted_right(
            Manager::context(), $user, $entities, WeblcmsRights::VIEW_RIGHT, $identifiers,
            WeblcmsRights::TYPE_PUBLICATION
        );

        $category_ids = [];

        foreach ($publication_ids_with_right_view as $publication_id)
        {
            $category_ids[] = $publications[$publication_id][ContentObjectPublication::PROPERTY_CATEGORY_ID];
        }

        $category_parent_ids = self::retrieve_publication_category_parent_ids_recursive($category_ids);
        $all_category_ids = array_merge($category_ids, array_values($category_parent_ids));
        $category_visibility = self::retrieve_publication_category_visibility($all_category_ids);

        foreach ($publication_ids_with_right_view as $publication_id)
        {
            $publication = $publications[$publication_id];
            if (ContentObjectPublicationCategory::is_recursive_visible_on_arrays(
                $publication[ContentObjectPublication::PROPERTY_CATEGORY_ID], $category_parent_ids, $category_visibility
            ))
            {
                $key = self::create_new_publications_cache_key(
                    $user->get_id(), $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
                );

                self::$new_publications_cache[$key][$publication[ContentObjectPublication::PROPERTY_TOOL]] = true;
            }
        }
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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class, CourseTypeUserCategoryRelCourse::PROPERTY_USER_ID
            ), new StaticConditionVariable($user_id)
        );

        $conditions[] = new NotCondition(
            new SubselectCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse::class, CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID
                ), new PropertyConditionVariable(Course::class, Course::PROPERTY_ID), Course::getTableName()
            )
        );

        $condition = new AndCondition($conditions);

        $result_set = DataManager::retrieves(
            CourseTypeUserCategoryRelCourse::class, new DataClassRetrievesParameters($condition)
        );

        foreach ($result_set as $course_type_user_category_rel_course)
        {
            if (!$course_type_user_category_rel_course->delete())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * **************************************************************************************************************
     * ContentObjectPublication Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns attributes for content object publications
     *
     * @param int $type
     * @param int $identifier
     * @param Condition $condition
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     */
    public static function getContentObjectPublicationsAttributes(
        $identifier, $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
        $offset = null, $order_properties = null
    )
    {
        switch ($type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                    ), new StaticConditionVariable($identifier)
                );
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
                    ), new StaticConditionVariable($identifier)
                );
                break;
            default :
                return [];
        }

        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publication_condition));
        }
        else
        {
            $condition = $publication_condition;
        }

        $result = self::retrieve_content_object_publications($condition);

        $publication_attributes = [];

        foreach ($result as $record)
        {
            $publication_attributes[] = self::create_publication_attributes_from_record($record);
        }

        return $publication_attributes;
    }

    /**
     * Returns the user identifiers for whom a publication was targetted
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param array $order_by
     * @param Condition $condition
     *
     * @return array
     */
    public static function getPublicationTargetUserIds(
        $publication_id, $course_id, $offset = null, $count = null, $order_by = null, $condition = null
    )
    {
        if (is_null($course_id))
        {
            $course_id = self::get_course_id_from_publication($publication_id);
        }

        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        $users_ids = [];

        foreach ($target_entities as $entity_type => $entity_ids)
        {
            switch ($entity_type)
            {
                case CourseUserEntity::ENTITY_TYPE :
                    foreach ($entity_ids as $user_id)
                    {
                        $users_ids[$user_id] = $user_id;
                    }
                    break;

                case CourseGroupEntity::ENTITY_TYPE :
                    $course_groups = CourseGroupDataManager::retrieve_course_groups_and_subgroups(
                        $target_entities[CourseGroupEntity::ENTITY_TYPE]
                    );

                    foreach ($course_groups as $course_group)
                    {
                        $course_group_users = CourseGroupDataManager::retrieve_course_group_user_ids(
                            $course_group->get_id()
                        );

                        foreach ($course_group_users as $id)
                        {
                            $users_ids[$id] = $id;
                        }
                    }
                    break;

                case CoursePlatformGroupEntity::ENTITY_TYPE :

                    $group_condition = new InCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $entity_ids
                    );
                    $groups_resultset = DataManager::retrieves(
                        Group::class, new DataClassRetrievesParameters($group_condition)
                    );

                    foreach ($groups_resultset as $group)
                    {
                        $user_ids_from_group = $group->get_users(true, true);
                        foreach ($user_ids_from_group as $user)
                        {
                            $users_ids[$user] = $user;
                        }
                    }
                    break;

                case 0 :

                    $course_users = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_course_users(
                        $course_id, $condition, $offset, $count, $order_by
                    );

                    foreach ($course_users as $course_user)
                    {
                        $users_ids[$course_user[User::PROPERTY_ID]] = $course_user[USER::PROPERTY_ID];
                    }
            }
        }

        return $users_ids;
    }

    /**
     * Connector method with the repository, retrieves the content object publication attributes for a given content
     * object publication
     *
     * @param $publication_id
     *
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID
            ), new StaticConditionVariable($publication_id)
        );
        $record = self::record(
            ContentObjectPublication::class, new RecordRetrieveParameters(
                new DataClassProperties(new PropertiesConditionVariable(ContentObjectPublication::class)), $condition
            )
        );

        return self::create_publication_attributes_from_record($record);
    }

    /**
     * Returns the joins for the content object publication with the content object table
     *
     * @return \libraries\storage\Joins
     */
    protected static function get_content_object_publication_joins()
    {
        $joins = [];

        $joins[] = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        return new Joins($joins);
    }

    /**
     * Retrieves the conditions for the content object publications with view right granted
     *
     * @static Static method
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     *
     * @return Condition
     */
    protected static function get_content_object_publications_with_view_right_granted_in_category_location_condition(
        $parent_location, $entities, $condition, $user_id = null
    )
    {
        $conditions = self::get_publication_conditions_with_right(
            WeblcmsRights::VIEW_RIGHT, $parent_location, $entities, $user_id, $condition
        );

        if (is_array($conditions) && count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

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
        $publication = self::retrieve_by_id(ContentObjectPublication::class, $publication_id);

        if (!$publication)
        {
            throw new ObjectNotExistException(
                Translation::get('ContentObjectPublication'), $publication_id
            );
        }

        return $publication->get_course_id();
    }

    /**
     * **************************************************************************************************************
     * New publications functionality *
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
    private static function get_course_settings_with_values_parameters(
        $course_setting_relation_class, $course_setting_foreign_property, $condition = null, $offset = null,
        $count = null, $order_by = []
    )
    {
        $data_class_properties = [];

        $data_class_properties[] = new PropertiesConditionVariable(CourseSetting::class);

        $data_class_properties[] = new PropertyConditionVariable(
            $course_setting_relation_class, $course_setting_relation_class::PROPERTY_OBJECT_ID, 'object_id'
        );

        $data_class_properties[] = new PropertyConditionVariable(
            $course_setting_relation_class, $course_setting_relation_class::PROPERTY_VALUE
        );

        $joins = [];

        $joins[] = new Join(
            $course_setting_relation_class::class_name(), new EqualityCondition(
                new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_ID),
                new PropertyConditionVariable($course_setting_relation_class, $course_setting_foreign_property)
            )
        );

        return new RecordRetrievesParameters(
            new DataClassProperties($data_class_properties), $condition, $offset, $count, $order_by, new Joins($joins)
        );
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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_COURSE_CODE
            ), new StaticConditionVariable($course_id)
        );

        if (!is_null($user_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_USER_ID
                ), new StaticConditionVariable($user_id)
            );
        }

        if (!is_null($category_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_CATEGORY_ID
                ), new StaticConditionVariable($category_id)
            );
        }

        if (!is_null($module_name))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_MODULE_NAME
                ), new StaticConditionVariable($module_name)
            );
        }

        $condition = new AndCondition($conditions);

        $order_by = array(
            new OrderBy(
                new PropertyConditionVariable(
                    CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_ACCESS_DATE
                )
            )
        );

        $course_module_access = self::retrieve(
            CourseModuleLastAccess::class, new DataClassRetrieveParameters($condition, $order_by)
        );

        if (!$course_module_access)
        {
            return 0;
        }
        else
        {
            return $course_module_access->get_access_date();
        }
    }

    // PERFORMANCE-TWEAKS-START

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
        return self::get_last_visit_date($course_id, $user_id, $module_name, null);
    }

    /**
     * Returns the condition for my publications
     *
     * @static Static method
     *
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param int $user_id
     * @param Condition $condition
     *
     * @return Condition
     */
    protected static function get_my_publications_condition(
        $parent_location, $entities, $user_id = null, $condition = null
    )
    {
        $conditions = self::get_publication_conditions_with_right(
            WeblcmsRights::EDIT_RIGHT, $parent_location, $entities, $user_id, $condition
        );

        if (is_array($conditions) && count($conditions) > 0)
        {
            return new OrCondition($conditions);
        }
    }

    /**
     * Returns the conditions for the content object publications for a given right
     *
     * @static Static method
     *
     * @param int $right
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param int $user_id
     * @param Condition $condition
     *
     * @return Condition
     */
    protected static function get_publication_conditions_with_right(
        $right, $parent_location, $entities, $user_id, $condition
    )
    {
        $conditions = [];

        $granted_location_ids = WeblcmsRights::getInstance()->get_identifiers_with_right_granted(
            $right, Manager::context(), $parent_location, WeblcmsRights::TYPE_PUBLICATION, $user_id, $entities
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID
            ), $granted_location_ids
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        return $conditions;
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
        $target_users = [];
        $publication_id = $publication->get_id();
        $course_id = $publication->get_course_id();

        try
        {
            // get the entities
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        // check for everybody
        if (array_key_exists(0, $target_entities[0]))
        {
            // get all course users
            $target_users = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_course_users(
                $course_id
            );
        }
        else
        {
            $user_ids = [];

            // get all users for all entities
            foreach ($target_entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case CoursePlatformGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                                Group::class, $group_id
                            );
                            if ($group)
                            {
                                $user_ids = array_merge($user_ids, $group->get_users(true, true));
                            }
                        }
                        break;
                    case CourseUserEntity::ENTITY_TYPE :
                        $user_ids = array_merge($user_ids, $entity_ids);
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class, $course_group_id
                            );

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
            $users = \Chamilo\Core\User\Storage\DataManager::records(
                User::class, new RecordRetrievesParameters(
                    new DataClassProperties(array(new PropertiesConditionVariable(User::class))),
                    new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $user_ids)
                )
            );

            foreach ($users as $user)
            {
                if (!array_key_exists($user[User::PROPERTY_ID], $target_users))
                {
                    $target_users[$user[User::PROPERTY_ID]] = $user;
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
        $publication = DataManager::retrieve_by_id(ContentObjectPublication::class, $publication_id);
        if (!$publication)
        {
            return [];
        }

        return self::get_publication_target_users($publication);
    }

    /**
     * Builds some conditions based on a few given parameters
     *
     * @static Static method
     *
     * @param Course $course
     * @param \core\user\storage\data_class\User $user
     * @param string $tool
     * @param string $content_object_type
     *
     * @return \libraries\storage\AndCondition
     */
    public static function get_publications_condition(
        Course $course, User $user, $tool = null, $content_object_type = null
    )
    {
        $conditions = [];

        if ((!$course->is_course_admin($user)))
        {
            $time_conditions = [];

            $time_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_HIDDEN
                ), new StaticConditionVariable(0)
            );

            $from_date_variable = new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
            );

            $to_date_variable = new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TO_DATE
            );

            $forever_conditions = [];

            $forever_conditions[] = new EqualityCondition($from_date_variable, new StaticConditionVariable(0));
            $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

            $between_conditions = [];

            $between_conditions[] = new ComparisonCondition(
                $from_date_variable, InEqualityCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
            );

            $between_conditions[] = new ComparisonCondition(
                $to_date_variable, InEqualityCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
            );

            $date_conditions = [];
            $date_conditions[] = new AndCondition($forever_conditions);
            $date_conditions[] = new AndCondition($between_conditions);

            $time_conditions[] = new OrCondition($date_conditions);

            $conditions[] = new AndCondition($time_conditions);
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    // PERFORMANCE-TWEAKS-END

    /**
     * Creates a condition for the rights location locked right class with a location and right id
     *
     * @static Static method
     *
     * @param int $location_id
     * @param int $right_id
     *
     * @return \libraries\storage\AndCondition
     */
    protected static function get_rights_location_locked_right_condition($location_id, $right_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationLockedRight::class, RightsLocationLockedRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($location_id)
        );

        if (!is_null($right_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationLockedRight::class, RightsLocationLockedRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * **************************************************************************************************************
     * CourseModuleLastAccess Functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks if a content object is published
     *
     * @param int $object_id
     *
     * @return bool
     */
    public static function isContentObjectPublished($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($object_id)
        );

        return self::count(ContentObjectPublication::class, new DataClassCountParameters($condition)) >= 1;
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
            $course_id = self::get_course_id_from_publication($publication_id);
        }

        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        if (count($target_entities) == 0)
        {
            return true;
        }

        foreach ($target_entities as $entity_type => $entity_ids)
        {
            switch ($entity_type)
            {
                case CourseUserEntity::ENTITY_TYPE :
                    foreach ($entity_ids as $uid)
                    {
                        if ($user_id == $uid)
                        {
                            return true;
                        }
                    }
                    break;

                case CourseGroupEntity::ENTITY_TYPE :
                    foreach ($entity_ids as $course_group_id)
                    {
                        $course_group = self::retrieve_by_id(CourseGroup::class, $course_group_id);

                        if (empty($course_group))
                        { // course group no longer exists
                            continue;
                        }

                        $course_group_users = CourseGroupDataManager::retrieve_course_group_user_ids(
                            $course_group->get_id()
                        );

                        foreach ($course_group_users as $id)
                        {
                            if ($user_id == $id)
                            {
                                return true;
                            }
                        }
                    }
                    break;

                case CoursePlatformGroupEntity::ENTITY_TYPE :
                    $groups_resultset = \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups(
                        $entity_ids
                    );

                    foreach ($groups_resultset as $group)
                    {
                        $condition = new EqualityCondition(
                            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                            new StaticConditionVariable($group->get_id())
                        );

                        $group_user_rels = \Chamilo\Core\Group\Storage\DataManager::retrieves(
                            GroupRelUser::class, new DataClassRetrievesParameters($condition)
                        );
                        foreach ($group_user_rels as $group_user_rel)
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
     * Is the given right locked for the given location
     *
     * @param $location_id int
     * @param $right_id int
     *
     * @return boolean
     */
    public static function is_right_locked_for_location($location_id, $right_id)
    {
        return self::count(
                RightsLocationLockedRight::class,
                new DataClassCountParameters(self::get_rights_location_locked_right_condition($location_id, $right_id))
            ) > 0;
    }

    /**
     * **************************************************************************************************************
     * CourseTypeUserCategory Functionality *
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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest::class, CourseRequest::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest::class, CourseRequest::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseRequest::class, CourseRequest::PROPERTY_DECISION),
            new StaticConditionVariable(CourseRequest::NO_DECISION)
        );

        $condition = new AndCondition($conditions);

        return (self::count(CourseRequest::class, new DataClassCountParameters($condition)) > 0);
    }

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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_COURSE_CODE
            ), new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_USER_ID
            ), new StaticConditionVariable($user_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_MODULE_NAME
            ), new StaticConditionVariable($module_name)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_CATEGORY_ID
            ), new StaticConditionVariable($category_id)
        );

        $condition = new AndCondition($conditions);

        $course_module_last_access = self::retrieve(
            CourseModuleLastAccess::class, new DataClassRetrieveParameters($condition)
        );

        if (!$course_module_last_access)
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_all_courses_with_course_categories(User $user)
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_courses_with_course_categories(
            $user
        );
    }

    /**
     * Retrieves a content object publication with content object
     *
     * @static Static method
     *
     * @param int $publication_id
     *
     * @return mixed
     */
    public static function retrieve_content_object_publication_with_content_object($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID
            ), new StaticConditionVariable($publication_id)
        );

        $publications = self::retrieve_content_object_publications($condition, [], 0, 1);

        return $publications->current();
    }

    /**
     * **************************************************************************************************************
     * CourseCategory Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves content object publications joined with the repository content object table
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication>
     */
    public static function retrieve_content_object_publications(
        $condition = null, $order_by = [], $offset = 0, $max_objects = - 1
    )
    {
        $data_class_properties = [];

        $data_class_properties[] = new PropertiesConditionVariable(ContentObjectPublication::class);

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TITLE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_TYPE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_CURRENT
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_OWNER_ID
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_CREATION_DATE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE,
            ContentObjectPublication::CONTENT_OBJECT_MODIFICATION_DATE_ALIAS
        );

        $properties = new DataClassProperties($data_class_properties);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, $max_objects, $offset, $order_by, self::get_content_object_publication_joins()
        );

        return self::records(ContentObjectPublication::class, $parameters);
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication>
     */
    public static function retrieve_content_object_publications_with_view_right_granted_in_category_location(
        $parent_location, $entities, $condition, $order_by = [], $offset = 0, $max_objects = - 1, $user_id = null
    )
    {
        $condition = self::get_content_object_publications_with_view_right_granted_in_category_location_condition(
            $parent_location, $entities, $condition, $user_id
        );

        return self::retrieve_content_object_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Retrieves the course categories ordered by name
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<CourseCategory>
     */
    public static function retrieve_course_categories_ordered_by_name(
        $condition = null, $offset = null, $count = null, $order_by = []
    )
    {
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_NAME)
        );

        return DataManager::retrieves(
            CourseCategory::class, new DataClassRetrievesParameters($condition, $offset, $count, $order_by)
        );
    }

    /**
     * **************************************************************************************************************
     * CourseSettings Functionality *
     * **************************************************************************************************************
     */

    public static function retrieve_course_category_by_code($category_code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_CODE),
            new StaticConditionVariable($category_code)
        );

        return self::retrieve(CourseCategory::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves the course settings joined with the course type setting values
     *
     * @param int $course_type_id
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_course_settings_with_course_type_values($courseTypeIdentifiers)
    {
        if (!is_array($courseTypeIdentifiers))
        {
            $courseTypeIdentifiers = array($courseTypeIdentifiers);
        }

        $condition = new InCondition(
            new PropertyConditionVariable(
                CourseTypeRelCourseSetting::class, CourseTypeRelCourseSetting::PROPERTY_COURSE_TYPE_ID
            ), $courseTypeIdentifiers
        );

        $parameters = self::get_course_settings_with_values_parameters(
            CourseTypeRelCourseSetting::class, CourseTypeRelCourseSetting::PROPERTY_COURSE_SETTING_ID, $condition
        );

        return self::records(CourseSetting::class, $parameters);
    }

    /**
     * Retrieves the course settings joined with the course setting values
     *
     * @param int $course_id
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_course_settings_with_course_values($courseIdentifiers)
    {
        if (!is_array($courseIdentifiers))
        {
            $courseIdentifiers = array($courseIdentifiers);
        }

        $condition = new InCondition(
            new PropertyConditionVariable(
                CourseRelCourseSetting::class, CourseRelCourseSetting::PROPERTY_COURSE_ID
            ), $courseIdentifiers
        );

        $parameters = self::get_course_settings_with_values_parameters(
            CourseRelCourseSetting::class, CourseRelCourseSetting::PROPERTY_COURSE_SETTING_ID, $condition
        );

        return self::records(CourseSetting::class, $parameters);
    }

    /**
     * **************************************************************************************************************
     * Course Settings Helper Functions *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the course settings joined with the course setting default values
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_by
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_course_settings_with_default_values(
        $condition = null, $offset = null, $count = null, $order_by = []
    )
    {
        $data_class_properties = [];

        $data_class_properties[] = new PropertiesConditionVariable(CourseSetting::class);

        $data_class_properties[] = new PropertyConditionVariable(
            CourseSettingDefaultValue::class, CourseSettingDefaultValue::PROPERTY_VALUE
        );

        $joins = [];

        $joins[] = new Join(
            CourseSettingDefaultValue::class, new EqualityCondition(
                new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_ID),
                new PropertyConditionVariable(
                    CourseSettingDefaultValue::class, CourseSettingDefaultValue::PROPERTY_COURSE_SETTING_ID
                )
            )
        );

        $parameters = new RecordRetrievesParameters(
            new DataClassProperties($data_class_properties), $condition, $count, $offset, $order_by, new Joins($joins)
        );

        return self::records(CourseSetting::class, $parameters);
    }

    /**
     * **************************************************************************************************************
     * CourseTool Functionality *
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_course_settings_with_tools(
        $condition = null, $offset = null, $count = null, $order_by = null
    )
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertiesConditionVariable(CourseSetting::class));

        $properties->add(
            new PropertyConditionVariable(
                CourseTool::class, CourseTool::PROPERTY_NAME, CourseSetting::PROPERTY_COURSE_TOOL_NAME
            )
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                CourseTool::class, new EqualityCondition(
                new PropertyConditionVariable(CourseSetting::class, CourseSetting::PROPERTY_TOOL_ID),
                new PropertyConditionVariable(CourseTool::class, CourseTool::PROPERTY_ID)
            ), Join::TYPE_LEFT
            )
        );

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $order_by, $joins);

        return self::records(CourseSetting::class, $parameters);
    }

    /**
     * **************************************************************************************************************
     * Content Object Publication Target Entities Functionality * TODO: Check if refactoring is possible because there
     * is some copy paste code *
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
            new PropertyConditionVariable(CourseTool::class, CourseTool::PROPERTY_NAME),
            new StaticConditionVariable($course_tool_name)
        );

        return self::retrieve(CourseTool::class, new DataClassRetrieveParameters($condition));
    }

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
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_USER_ID
            ), new StaticConditionVariable($user_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($course_type_id)
        );

        if ($direction == 'up')
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_SORT
                ), ComparisonCondition::LESS_THAN, new StaticConditionVariable($sort)
            );

            $order_direction = SORT_DESC;
        }
        elseif ($direction == 'down')
        {
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_SORT
                ), ComparisonCondition::GREATER_THAN, new StaticConditionVariable($sort)
            );

            $order_direction = SORT_ASC;
        }

        $condition = new AndCondition($conditions);
        $object_table_order = array(
            new OrderBy(
                new PropertyConditionVariable(CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_SORT),
                $order_direction
            )
        );

        return self::retrieve(
            CourseTypeUserCategory::class, new DataClassRetrieveParameters($condition, $object_table_order)
        );
    }

    /**
     * Retrieves the course user categories from a given course type
     *
     * @static Static method
     *
     * @param int $course_type_id
     * @param int $user_id
     *
     * @return  \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory>
     */
    public static function retrieve_course_user_categories_from_course_type($course_type_id, $user_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_TYPE_ID
            ), new StaticConditionVariable($course_type_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_USER_ID
            ), new StaticConditionVariable($user_id)
        );
        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(CourseTypeUserCategory::class));

        $properties->add(
            new PropertyConditionVariable(CourseUserCategory::class, CourseUserCategory::PROPERTY_TITLE)
        );

        $join_condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID
            ), new PropertyConditionVariable(CourseUserCategory::class, CourseUserCategory::PROPERTY_ID)
        );

        $joins = new Joins();
        $joins->add(new Join(CourseUserCategory::class, $join_condition));

        $order_by = array(
            new OrderBy(
                new PropertyConditionVariable(
                    CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_SORT
                )
            )
        );

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, $order_by, $joins);

        return self::records(CourseTypeUserCategory::class, $parameters);
    }

    /**
     * Retrieves an introduction from a given condition
     *
     * @static Static method
     *
     * @param Condition $condition
     *
     * @return ContentObjectPublication
     */
    public static function retrieve_introduction_publication($condition)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                    ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
                )
            )
        );

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Introduction::class)
        );

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition, [], $joins);

        return self::retrieve(ContentObjectPublication::class, $parameters);
    }

    /**
     * Helper function to retrieve an introduction publication by a given course and tool
     *
     * @param int $course_id
     * @param string $tool
     *
     * @return ContentObjectPublication
     */
    public static function retrieve_introduction_publication_by_course_and_tool($course_id, $tool)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable($tool)
        );

        $condition = new AndCondition($conditions);

        return self::retrieve_introduction_publication($condition);
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication>
     */
    public static function retrieve_my_publications(
        $parent_location, $entities, $condition = null, $order_by = [], $offset = 0, $max_objects = - 1,
        $user_id = null
    )
    {
        $condition = self::get_my_publications_condition($parent_location, $entities, $user_id, $condition);

        return self::retrieve_content_object_publications($condition, $order_by, $offset, $max_objects);
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_new_publication_icon_ids(
        $course_id, $user_id, $is_teacher = false, $tool = null, $category_id = null
    )
    {
        $join_conditions = [];

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_COURSE_CODE
            ), new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            )
        );

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_MODULE_NAME
            ), new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            )
        );

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_USER_ID
            ), new StaticConditionVariable($user_id)
        );

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_CATEGORY_ID
            ), new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
            )
        );

        $join = new Join(CourseModuleLastAccess::class, new AndCondition($join_conditions), Join::TYPE_LEFT);

        $conditions = [];

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), $course_id
        );

        if (!is_null($tool))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                ), new StaticConditionVariable($tool)
            );
        }

        if (!is_null($category_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
                ), new StaticConditionVariable($category_id)
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_HIDDEN
            ), new StaticConditionVariable(0)
        );

        $conditions_publication_period = [];

        $conditions_publication_period[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
            ), ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );

        $conditions_publication_period[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TO_DATE
            ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
        );

        $condition_publication_period = new AndCondition($conditions_publication_period);

        $condition_publication_forever = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
            ), new StaticConditionVariable(0)
        );

        $conditions[] = new OrCondition($condition_publication_forever, $condition_publication_period);

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                ), new StaticConditionVariable('home')
            )
        );

        $modified_conditions = [];

        $modified_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
            ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_ACCESS_DATE
            )
        );

        $modified_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseModuleLastAccess::class, CourseModuleLastAccess::PROPERTY_ACCESS_DATE
            ), null
        );

        $conditions[] = new OrCondition($modified_conditions);

        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();

        if ($is_teacher)
        {
            $properties->add(
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                    )
                )
            );
            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
                )
            );
        }
        else
        {
            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID
                )
            );

            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                )
            );

            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
                )
            );
            $properties->add(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
                )
            );
        }

        $parameters =
            new RecordRetrievesParameters($properties, $condition, null, null, [], new Joins(array($join)));

        return self::records(ContentObjectPublication::class, $parameters);
    }

    /**
     * Retrieves parent ID's of given publication categories.
     * Result contains only child ID's whose parent ID is not null.
     *
     * @return array Key: child publication category ID's Value: parent publication category ID's.
     */
    public static function retrieve_publication_category_parent_ids($publication_category_ids)
    {
        $conditions = [];
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            ), $publication_category_ids
        );
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
                ), new StaticConditionVariable(0)
            )
        );
        $condition = new AndCondition($conditions);

        $properties = new DataClassProperties();
        $properties->add(
            new PropertiesConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            )
        );
        $properties->add(
            new PropertiesConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            )
        );

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $categories = self::records(ContentObjectPublicationCategory::class, $parameters);

        $parent_ids = [];

        foreach ($categories as $category)
        {
            $parent_ids[$category[ContentObjectPublicationCategory::PROPERTY_ID]] =
                $category[ContentObjectPublicationCategory::PROPERTY_PARENT];
        }

        return $parent_ids;
    }

    /**
     * Returns an array mapping child publication category ID's onto parent ID's.
     * Idea: Retrieve the child-parent relation of publication categories with as few queries as possible and store them
     * in the memory. The function
     * ContentObjectPublicationCategory::is_recursive_visible_on_arrays(...) will loop over the child-parent tree,
     * which is much faster
     * than the recursive function calls to ContentObjectPublicationCategory::is_recursive_visible(...). This function
     * actually retrieves
     * the publication category tree level-by-level starting with the leaf level, followed by parent level, then
     * grandparents until an empty level is
     * found.
     * Result is a flat array mapping each ID in $publication_category_ids onto its parent ID and each parent onto its
     * grand parent ID, etc.
     * Result will only contain child ID's if the 'inherit' property of the location is true and the parent is not null.
     *
     * @return array Keys: child location ID's Values: parent location ID's.
     */
    public static function retrieve_publication_category_parent_ids_recursive($publication_category_ids)
    {
        $all_parent_ids = [];

        $parent_ids = $publication_category_ids;

        while (true)
        {
            $parent_ids = self::retrieve_publication_category_parent_ids($parent_ids);

            if (count($parent_ids) == 0)
            {
                break;
            }

            $all_parent_ids = $all_parent_ids + $parent_ids;
        }

        return $all_parent_ids;
    }

    /**
     * Retrieves the visibility property of given publication categories.
     *
     * @return array Key: publication category ID Value: true or false.
     */
    public static function retrieve_publication_category_visibility($publication_category_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            ), $publication_category_ids
        );

        $properties = new DataClassProperties();
        $properties->add(
            new PropertiesConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            )
        );
        $properties->add(
            new PropertiesConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_VISIBLE
            )
        );

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $categories = self::records(ContentObjectPublicationCategory::class, $parameters);

        $visibilities = [];
        foreach ($categories as $category)
        {
            $visibilities[$category[ContentObjectPublicationCategory::PROPERTY_ID]] =
                $category[ContentObjectPublicationCategory::PROPERTY_VISIBLE];
        }

        return $visibilities;
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
        $course_id = self::get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity::ENTITY_TYPE] = false;
        }

        return $target_entities[CourseGroupEntity::ENTITY_TYPE];
    }

    /**
     * **************************************************************************************************************
     * Introduction Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the target course groups for a given publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<CourseGroup>
     */
    public static function retrieve_publication_target_course_groups(
        $publication_id, $course_id, $offset = null, $count = null, $order_by = null, $condition = null
    )
    {
        if (is_null($course_id))
        {
            $course_id = self::get_course_id_from_publication($publication_id);
        }
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        if ($target_entities[0])
        {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
                new StaticConditionVariable($course_id)
            );
            $conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
                ComparisonCondition::GREATER_THAN, new StaticConditionVariable(0)
            );

            if ($condition)
            {
                $conditions[] = $condition;
            }

            $group_condition = new AndCondition($conditions);
        }
        else
        {
            return CourseGroupDataManager::retrieve_course_groups_and_subgroups(
                $target_entities[CourseGroupEntity::ENTITY_TYPE], $condition, $offset, $count, $order_by
            );
        }

        return self::retrieves(
            CourseGroup::class, new DataClassRetrievesParameters($group_condition, $count, $offset, $order_by)
        );
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
        $course_id = self::get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity::ENTITY_TYPE] = false;
        }

        return $target_entities[CoursePlatformGroupEntity::ENTITY_TYPE];
    }

    /**
     * **************************************************************************************************************
     * RightsLocationLockedRight Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the target platform groups for a given publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\group\Group>
     */
    public static function retrieve_publication_target_platform_groups(
        $publication_id, $course_id, $offset = null, $count = null, $order_by = null, $condition = null
    )
    {
        if (is_null($course_id))
        {
            $course_id = self::get_course_id_from_publication($publication_id);
        }
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = [];
        }

        if ($target_entities[0])
        {
            $cgrConditions = [];
            $cgrConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($course_id)
            );
            $cgrConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
            );

            $group_ids = DataManager::distinct(
                CourseEntityRelation::class, new DataClassDistinctParameters(
                    new AndCondition($cgrConditions), new DataClassProperties(
                        array(
                            new PropertyConditionVariable(
                                CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID
                            )
                        )
                    )
                )
            );

            return \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups(
                $group_ids, $condition, $count, $offset, $order_by
            );
        }

        return \Chamilo\Core\Group\Storage\DataManager::retrieve_groups_and_subgroups(
            $target_entities[CoursePlatformGroupEntity::ENTITY_TYPE], $condition, $count, $offset, $order_by
        );
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
        $course_id = self::get_course_id_from_publication($publication_id);

        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, Manager::context(), $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                $course_id, WeblcmsRights::TREE_TYPE_COURSE
            );
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities[CourseUserEntity::ENTITY_TYPE] = false;
        }

        return $target_entities[CourseUserEntity::ENTITY_TYPE];
    }

    /**
     * **************************************************************************************************************
     * RightsLocationLockedRight Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the target users of a publication
     *
     * @param int $publication_id
     * @param int $course_id
     * @param int $offset
     * @param int $count
     * @param OrderBy $order_by
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_publication_target_users(
        $publication_id, $course_id, $offset = null, $count = null, $order_by = null, $condition = null
    )
    {
        $userIds = self::getPublicationTargetUserIds(
            $publication_id, $course_id, $offset, $count, $order_by, $condition
        );

        if (count($userIds) == 0)
        {
            $userIds[] = - 1;
        }

        $conditions = [];

        $conditions[] = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $userIds);

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $user_condition = new AndCondition($conditions);

        return \Chamilo\Core\User\Storage\DataManager::retrieves(
            User::class, new DataClassRetrievesParameters($user_condition, $count, $offset, $order_by)
        );
    }

    /**
     * **************************************************************************************************************
     * CourseRequest Functionality *
     * **************************************************************************************************************
     */

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
            $tools_with_new_publications = DataManager::retrieve_new_publication_icon_ids(
                $course->get_id(), $user->get_id(), true, $tool, $category
            );
            if ($tools_with_new_publications->count() > 0)
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
            $weblcms_rights = WeblcmsRights::getInstance();

            $publications = DataManager::retrieve_new_publication_icon_ids(
                $course->get_id(), $user->get_id(), false, $tool, $category
            );

            foreach ($publications as $publication)
            {
                if ($weblcms_rights->is_allowed_in_courses_subtree(
                    WeblcmsRights::VIEW_RIGHT, $publication[ContentObjectPublication::PROPERTY_ID],
                    WeblcmsRights::TYPE_PUBLICATION, $course->get_id()
                ))
                {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Determines if a tool has new publications since the last time the current user visited the tool.
     *
     * @param string $tool string
     * @param User $user
     * @param Course $course
     *
     * @return bool
     * @see fill_new_publications_cache(...) for more information.
     *
     */
    public static function tool_has_new_publications($tool, User $user, Course $course)
    {
        $key = self::create_new_publications_cache_key($user->get_id(), $course->get_id());

        if (!isset(self::$is_cached[$key]))
        {
            // Fill cache for given course.
            self::fill_new_publications_cache($user, self::create_courses_array($course));
            assert(self::$is_cached[$key]);
        }

        return self::$new_publications_cache[$key][$tool];
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
        $publication = self::retrieve_by_id(ContentObjectPublication::class, $publication_attr->get_id());
        $publication->set_content_object_id($publication_attr->get_content_object_id());

        return $publication->update();
    }
}
