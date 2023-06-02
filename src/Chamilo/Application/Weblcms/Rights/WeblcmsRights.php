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
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package application.lib.weblcms
 */
class WeblcmsRights extends RightsUtil
{
    public const ADD_RIGHT = '2';

    // Course Rights

    public const CONTEXT = Manager::CONTEXT;

    public const DELETE_RIGHT = '4';

    public const EDIT_RIGHT = '3';

    public const LOCATION_BROWSER = 1;

    public const LOCATION_HOME = 2;

    public const LOCATION_VIEWER = 3;

    public const MANAGE_CATEGORIES_RIGHT = 5;

    public const TREE_TYPE_COURSE = 1;

    public const TYPE_COURSE = 2;

    public const TYPE_COURSE_CATEGORY = 4;

    public const TYPE_COURSE_MODULE = 3;

    public const TYPE_COURSE_TYPE = 1;

    public const TYPE_PUBLICATION = 5;

    public const VIEW_RIGHT = '1';

    private static $instance;

    public function create_location_in_courses_subtree(
        $type, $identifier, $parent, $tree_identifier = 0, $create_in_batch = false, $inherit = 1
    )
    {
        return parent::create_location(
            Manager::CONTEXT, $type, $identifier, $inherit, $parent, 0, $tree_identifier,
            WeblcmsRights::TREE_TYPE_COURSE, true, $create_in_batch
        );
    }

    /**
     * @return WeblcmsRights
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    public static function get_available_rights($location)
    {
        if ($location && $location->getType() == self::TYPE_PUBLICATION)
        {
            return [
                Translation::get('ViewRight') => self::VIEW_RIGHT,
                Translation::get('EditRight') => self::EDIT_RIGHT,
                Translation::get('DeleteRight') => self::DELETE_RIGHT
            ];
        }

        return [
            Translation::get('ViewRight') => self::VIEW_RIGHT,
            Translation::get('AddRight') => self::ADD_RIGHT,
            Translation::get('EditRight') => self::EDIT_RIGHT,
            Translation::get('DeleteRight') => self::DELETE_RIGHT,
            Translation::getInstance()->getTranslation(
                'ManageCategoriesRight', null, Manager::CONTEXT
            ) => self::MANAGE_CATEGORIES_RIGHT
        ];
    }

    public function get_courses_subtree_root($tree_identifier = 0)
    {
        return parent::get_root(Manager::CONTEXT, WeblcmsRights::TREE_TYPE_COURSE, $tree_identifier);
    }

    public function get_courses_subtree_root_id($tree_identifier = 0)
    {
        return parent::get_root_id(Manager::CONTEXT, WeblcmsRights::TREE_TYPE_COURSE, $tree_identifier);
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
    public function get_publication_identifiers_with_right_granted(
        $right, RightsLocation $parent_location, Course $course, User $user
    )
    {
        $entities = [];
        $entities[] = CourseGroupEntity::getInstance($course->getId());
        $entities[] = CourseUserEntity::getInstance();
        $entities[] = CoursePlatformGroupEntity::getInstance($course->getId());

        return $this->get_identifiers_with_right_granted(
            $right, Manager::CONTEXT, $parent_location, WeblcmsRights::TYPE_PUBLICATION, $user->getId(), $entities
        );
    }

    public function get_weblcms_location_by_identifier($type, $identifier)
    {
        return parent::get_location_by_identifier(Manager::CONTEXT, $type, $identifier);
    }

    public function get_weblcms_location_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent::get_location_by_identifier(
            Manager::CONTEXT, $type, $identifier, $course_id, WeblcmsRights::TREE_TYPE_COURSE
        );
    }

    public function get_weblcms_location_id_by_identifier($type, $identifier)
    {
        return parent::get_location_id_by_identifier(Manager::CONTEXT, $type, $identifier);
    }

    public function get_weblcms_location_id_by_identifier_from_courses_subtree($type, $identifier, $course_id = 0)
    {
        return parent::get_location_id_by_identifier(
            Manager::CONTEXT, $type, $identifier, $course_id, WeblcmsRights::TREE_TYPE_COURSE
        );
    }

    public function get_weblcms_root()
    {
        return parent::get_root(Manager::CONTEXT);
    }

    public function get_weblcms_root_id()
    {
        return parent::get_root_id(Manager::CONTEXT);
    }

    public function is_allowed_in_courses_subtree($right, $identifier, $type, $tree_identifier = 0, $user_id = null)
    {
        if (is_null($user_id))
        {
            $user_id = $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_IO);
        }
        $course_id = Request::get(Manager::PARAM_COURSE);

        $entities = [];
        $entities[] = CourseGroupEntity::getInstance($course_id);
        $entities[] = CourseUserEntity::getInstance();
        $entities[] = CoursePlatformGroupEntity::getInstance($course_id);

        return parent::is_allowed(
            $right, Manager::CONTEXT, $user_id, $entities, $identifier, $type, $tree_identifier,
            WeblcmsRights::TREE_TYPE_COURSE, true
        );
    }

    public function render_target_entities_as_string($entities)
    {
        $target_list = [];

        // don't display each individual user if it is published for
        // everybody...
        // if a name is alfabetically before "everybody" this would be the
        // selected
        // item in the dropdownlist which works confusing when you expect
        // "everybody"
        if (array_key_exists(0, $entities[0]))
        {
            $target_list[] = Translation::get('Everybody', null, StringUtilities::LIBRARIES);
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
                                Group::class, $group_id
                            );
                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case CourseUserEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $target_list[] = '<option>' . DataManager::get_fullname_from_user($user_id) . '</option>';
                        }
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class, $course_group_id
                            );

                            if ($course_group)
                            {
                                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
                            }
                        }
                        break;

                    case 0 :
                        $target_list[] =
                            '<option>' . Translation::get('Everybody', null, StringUtilities::LIBRARIES) . '</option>';
                        break;
                }
            }

            $target_list[] = '</select>';
        }

        return implode(PHP_EOL, $target_list);
    }
}
