<?php
namespace Chamilo\Application\Weblcms\Rights;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * $Id: weblcms_rights.class.php 218 2009-11-13 14:21:26Z kariboe $
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
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public static function get_available_rights($location)
    {
        if ($location && $location->get_type() == self :: TYPE_PUBLICATION)
        {
            return array(
                Translation :: get('ViewRight') => self :: VIEW_RIGHT,
                Translation :: get('EditRight') => self :: EDIT_RIGHT,
                Translation :: get('DeleteRight') => self :: DELETE_RIGHT);
        }
        return array(
            Translation :: get('ViewRight') => self :: VIEW_RIGHT,
            Translation :: get('AddRight') => self :: ADD_RIGHT,
            Translation :: get('EditRight') => self :: EDIT_RIGHT,
            Translation :: get('DeleteRight') => self :: DELETE_RIGHT);
    }

    public function get_weblcms_location_by_identifier($type, $identifier)
    {

        return parent :: get_location_by_identifier(Manager :: context(), $type, $identifier);
    }

    public function get_weblcms_location_id_by_identifier($type, $identifier)
    {
        return parent :: get_location_id_by_identifier(Manager :: context(), $type, $identifier);
    }

    public function create_location_in_courses_subtree($type, $identifier, $parent, $tree_identifier = 0,
        $create_in_batch = false, $inherit = 1)
    {
        return parent :: create_location(
            Manager :: context(),
            $type,
            $identifier,
            $inherit,
            $parent,
            0,
            $tree_identifier,
            WeblcmsRights :: TREE_TYPE_COURSE,
            true,
            $create_in_batch);
    }

    public function get_weblcms_root_id()
    {
        return parent :: get_root_id(Manager :: context());
    }

    public function get_weblcms_root()
    {
        return parent :: get_root(Manager :: context());
    }

    public function get_courses_subtree_root($tree_identifier = 0)
    {
        return parent :: get_root(Manager :: context(), WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

    public function get_courses_subtree_root_id($tree_identifier = 0)
    {
        return parent :: get_root_id(Manager :: context(), WeblcmsRights :: TREE_TYPE_COURSE, $tree_identifier);
    }

    public function get_weblcms_location_id_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent :: get_location_id_by_identifier(
            Manager :: context(),
            $type,
            $identifier,
            $course_id,
            WeblcmsRights :: TREE_TYPE_COURSE);
    }

    public function get_weblcms_location_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent :: get_location_by_identifier(
            Manager :: context(),
            $type,
            $identifier,
            $course_id,
            WeblcmsRights :: TREE_TYPE_COURSE);
    }

    public function is_allowed_in_courses_subtree($right, $identifier, $type, $tree_identifier = 0, $user_id = null)
    {
        if (is_null($user_id))
        {
            $user_id = Session :: get_user_id();
        }
        $course_id = Request :: get(Manager :: PARAM_COURSE);

        $entities = array();
        $entities[] = CourseGroupEntity :: get_instance($course_id);
        $entities[] = CourseUserEntity :: get_instance();
        $entities[] = CoursePlatformGroupEntity :: get_instance($course_id);

        try
        {
            return parent :: is_allowed(
                $right,
                Manager :: context(),
                $user_id,
                $entities,
                $identifier,
                $type,
                $tree_identifier,
                WeblcmsRights :: TREE_TYPE_COURSE,
                true);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            return false;
        }
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
        if (array_key_exists(0, $entities[0]))
        {
            $target_list[] = Translation :: get('Everybody', null, Utilities :: COMMON_LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';

            foreach ($entities as $entity_type => $entity_ids)
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
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case CourseUserEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $target_list[] = '<option>' .
                                 \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user($user_id) . '</option>';
                        }
                        break;
                    case CourseGroupEntity :: ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager :: retrieve_by_id(
                                CourseGroup :: class_name(),
                                $course_group_id);

                            if ($course_group)
                            {
                                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
                            }
                        }
                        break;

                    case 0 :
                        $target_list[] = '<option>' .
                             Translation :: get('Everybody', null, Utilities :: COMMON_LIBRARIES) . '</option>';
                        break;
                }
            }

            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }

    public function create_subtree_root_location($tree_identifier, $tree_type, $return_location = false)
    {
        return $this->create_location(
            Manager :: context(),
            self :: TYPE_ROOT,
            0,
            0,
            0,
            0,
            $tree_identifier,
            $tree_type,
            $return_location);
    }

    /**
     * Inverts the location entity right for a given right, entity, entity type and location
     *
     * @param $right int
     * @param $entity_id int
     * @param $entity_type int
     * @param $location_id int
     *
     * @return boolean
     */
    public function invert_location_entity_right($right, $entity_id, $entity_type, $location_id)
    {
        return parent :: invert_location_entity_right(
            Manager :: context(),
            $right,
            $entity_id,
            $entity_type,
            $location_id);
    }
}
