<?php
namespace Chamilo\Application\Weblcms\Rights;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\RightsLocation;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms
 */
class WeblcmsRights extends RightsUtil
{
    // Course Rights
    const VIEW_RIGHT = '1';
    const ADD_RIGHT = '2';
    const EDIT_RIGHT = '3';
    const DELETE_RIGHT = '4';
    const MANAGE_CATEGORIES_RIGHT = 5;
    const LOCATION_BROWSER = 1;
    const LOCATION_HOME = 2;
    const LOCATION_VIEWER = 3;
    const TREE_TYPE_COURSE = 1;
    const TYPE_COURSE_TYPE = 1;
    const TYPE_COURSE = 2;
    const TYPE_COURSE_MODULE = 3;
    const TYPE_COURSE_CATEGORY = 4;
    const TYPE_PUBLICATION = 5;

    private static $instance;

    /**
     *
     * @return WeblcmsRights
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get_available_rights($location)
    {
        if ($location && $location->get_type() == self::TYPE_PUBLICATION)
        {
            return array(
                Translation::get('ViewRight') => self::VIEW_RIGHT,
                Translation::get('EditRight') => self::EDIT_RIGHT,
                Translation::get('DeleteRight') => self::DELETE_RIGHT);
        }

        return array(
            Translation::get('ViewRight') => self::VIEW_RIGHT,
            Translation::get('AddRight') => self::ADD_RIGHT,
            Translation::get('EditRight') => self::EDIT_RIGHT,
            Translation::get('DeleteRight') => self::DELETE_RIGHT,
            Translation::getInstance()->getTranslation('ManageCategoriesRight', null, Manager::context()) => self::MANAGE_CATEGORIES_RIGHT);
    }

    public function get_weblcms_location_by_identifier($type, $identifier)
    {
        return parent::get_location_by_identifier(Manager::context(), $type, $identifier);
    }

    public function get_weblcms_location_id_by_identifier($type, $identifier)
    {
        return parent::get_location_id_by_identifier(Manager::context(), $type, $identifier);
    }

    public function create_location_in_courses_subtree($type, $identifier, $parent, $tree_identifier = 0,
        $create_in_batch = false, $inherit = 1)
    {
        return parent::create_location(
            \Chamilo\Application\Weblcms\Manager::context(),
            $type,
            $identifier,
            $inherit,
            $parent,
            0,
            $tree_identifier,
            WeblcmsRights::TREE_TYPE_COURSE,
            true,
            $create_in_batch);
    }

    public function get_weblcms_root_id()
    {
        return parent::get_root_id(Manager::context());
    }

    public function get_weblcms_root()
    {
        return parent::get_root(Manager::context());
    }

    public function get_courses_subtree_root($tree_identifier = 0)
    {
        return parent::get_root(Manager::context(), WeblcmsRights::TREE_TYPE_COURSE, $tree_identifier);
    }

    public function get_courses_subtree_root_id($tree_identifier = 0)
    {
        return parent::get_root_id(Manager::context(), WeblcmsRights::TREE_TYPE_COURSE, $tree_identifier);
    }

    public function get_weblcms_location_id_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent::get_location_id_by_identifier(
            Manager::context(),
            $type,
            $identifier,
            $course_id,
            WeblcmsRights::TREE_TYPE_COURSE);
    }

    public function get_weblcms_location_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent::get_location_by_identifier(
            Manager::context(),
            $type,
            $identifier,
            $course_id,
            WeblcmsRights::TREE_TYPE_COURSE);
    }

    public function is_allowed_in_courses_subtree($right, $identifier, $type, $tree_identifier = 0, $user_id = null)
    {
        if (is_null($user_id))
        {
            $user_id = Session::get_user_id();
        }
        $course_id = Request::get(Manager::PARAM_COURSE);

        $entities = array();
        $entities[] = CourseGroupEntity::getInstance($course_id);
        $entities[] = CourseUserEntity::getInstance();
        $entities[] = CoursePlatformGroupEntity::getInstance($course_id);

        return parent::is_allowed(
            $right,
            Manager::context(),
            $user_id,
            $entities,
            $identifier,
            $type,
            $tree_identifier,
            WeblcmsRights::TREE_TYPE_COURSE,
            true
        );
    }

    public function render_target_entities_as_string($entities)
    {
        $target_list = array();

        // don't display each individual user if it is published for
        // everybody...
        // if a name is alfabetically before "everybody" this would be the
        // selected
        // item in the dropdownlist which works confusing when you expect
        // "everybody"
        if (is_array($entities[0]) && array_key_exists(0, $entities[0]))
        {
            $target_list[] = Translation::get('Everybody', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case CoursePlatformGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                                Group::class_name(),
                                $group_id);
                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case CourseUserEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $target_list[] = '<option>' .
                                 \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user($user_id) . '</option>';
                        }
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class_name(),
                                $course_group_id);

                            if ($course_group)
                            {
                                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
                            }
                        }
                        break;

                    case 0 :
                        $target_list[] = '<option>' . Translation::get('Everybody', null, Utilities::COMMON_LIBRARIES) .
                             '</option>';
                        break;
                }
            }

            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }

    /**
     * Returns the publication identifiers on which a right has been granted for a given user in a given course
     *
     * @param int $right
     * @param RightsLocation $parent_location
     * @param Course $course
     * @param User $user
     *
     * @return mixed
     */
    public function get_publication_identifiers_with_right_granted($right, RightsLocation $parent_location,
        Course $course, User $user)
    {
        $entities = array();
        $entities[] = CourseGroupEntity::getInstance($course->getId());
        $entities[] = CourseUserEntity::getInstance();
        $entities[] = CoursePlatformGroupEntity::getInstance($course->getId());

        return $this->get_identifiers_with_right_granted(
            $right,
            \Chamilo\Application\Weblcms\Manager::context(),
            $parent_location,
            WeblcmsRights::TYPE_PUBLICATION,
            $user->getId(),
            $entities);
    }
}
