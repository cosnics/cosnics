<?php
namespace Chamilo\Application\Weblcms\Rights\Entities;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extension on the platform group entity specific for the course to limit the platform groups
 *
 * @author Sven Vanpoucke
 */
class CoursePlatformGroupEntity extends PlatformGroupEntity
{

    private static $instance;

    private $course_id;

    /**
     * Excludes the groups by id
     *
     * @var Array<int>
     */
    private $excluded_groups;

    /**
     * Limits the groups by id
     *
     * @var Array<int>
     */
    private $limited_groups;

    private $platform_group_cache;

    /**
     * The subscribed group ids for the course
     *
     * @var Array<int>
     */
    private $subscribed_platform_group_ids;

    public function __construct(
        $course_id = 0, $subscribed_platform_group_ids = [], $limited_groups = [], $excluded_groups = []
    )
    {
        $this->course_id = $course_id;
        $this->limited_groups = $limited_groups;
        $this->excluded_groups = $excluded_groups;
        $this->subscribed_platform_group_ids = $subscribed_platform_group_ids;
    }

    public function exclude_groups($excluded_groups)
    {
        $this->excluded_groups = $excluded_groups;
    }

    public function getElementFinderType()
    {
        $elementFinderType = static::getElementFinderTypeInstance();
        $elementFinderType->set_parameters(['course_id' => $this->course_id]);

        return $elementFinderType;
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public static function getElementFinderTypeInstance()
    {
        return new AdvancedElementFinderElementType(
            'platform_groups', Translation::get('PlatformGroups'), Manager::CONTEXT, 'platform_groups_feed'
        );
    }

    public static function getInstance($course_id = 0)
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self($course_id);
        }

        return self::$instance;
    }

    /**
     * Builds the condition with the limited and excluded groups
     *
     * @param $condition Condition
     *
     * @return Condition
     */
    public function get_condition($condition)
    {
        $conditions = [];

        if ($this->limited_groups)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $this->limited_groups
            );
        }

        if ($this->excluded_groups)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_ID), $this->excluded_groups
                )
            );
        }

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $count = count($conditions);
        if ($count > 1)
        {
            return new AndCondition($conditions);
        }

        if ($count == 1)
        {
            return $conditions[0];
        }
    }

    /**
     * Override the get root ids to only return the subscribed groups instead of the chamilo root group
     *
     * @return Array<int>
     */
    public function get_root_ids()
    {
        if (!empty($this->subscribed_platform_group_ids))
        {
            return $this->subscribed_platform_group_ids;
        }

        return parent::get_root_ids();
    }

    public function get_subscribed_platform_group_ids()
    {
        return $this->subscribed_platform_group_ids;
    }

    /**
     * Getters and setters
     */
    public function limit_groups($limited_groups)
    {
        $this->limited_groups = $limited_groups;
    }

    /**
     * Retrieves the entity item ids relevant for a given user.
     * Overrides because only subscribed platformgroups need to
     * be checked. Also none of their parents as they are not subscribed in the course, and therefore cannot have
     * specific rights set to them
     *
     * @param $user_id int
     *
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->platform_group_cache[$user_id]))
        {
            $this->platform_group_cache[$user_id] =
                $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($user_id);
        }

        return $this->platform_group_cache[$user_id];
    }

    public function set_subscribed_platform_group_ids($subscribed_platform_group_ids)
    {
        $this->subscribed_platform_group_ids = $subscribed_platform_group_ids;
    }
}
