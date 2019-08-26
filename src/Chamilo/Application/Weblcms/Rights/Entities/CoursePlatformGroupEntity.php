<?php
namespace Chamilo\Application\Weblcms\Rights\Entities;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Extension on the platform group entity specific for the course to limit the platform groups
 * 
 * @author Sven Vanpoucke
 */
class CoursePlatformGroupEntity extends PlatformGroupEntity
{

    /**
     * The subscribed group ids for the course
     * 
     * @var Array<int>
     */
    private $subscribed_platform_group_ids;

    /**
     * Limits the groups by id
     * 
     * @var Array<int>
     */
    private $limited_groups;

    /**
     * Excludes the groups by id
     * 
     * @var Array<int>
     */
    private $excluded_groups;

    private $course_id;

    private static $instance;

    private $platform_group_cache;

    public static function getInstance($course_id = 0)
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self($course_id);
        }
        return self::$instance;
    }

    public function __construct($course_id = 0, $subscribed_platform_group_ids = array(), $limited_groups = array(),
        $excluded_groups = array())
    {
        parent::__construct();

        $this->course_id = $course_id;
        $this->limited_groups = $limited_groups;
        $this->excluded_groups = $excluded_groups;
        $this->subscribed_platform_group_ids = $subscribed_platform_group_ids;
    }

    /**
     * Getters and setters
     */
    public function limit_groups($limited_groups)
    {
        $this->limited_groups = $limited_groups;
    }

    public function exclude_groups($excluded_groups)
    {
        $this->excluded_groups = $excluded_groups;
    }

    public function get_subscribed_platform_group_ids()
    {
        return $this->subscribed_platform_group_ids;
    }

    public function set_subscribed_platform_group_ids($subscribed_platform_group_ids)
    {
        $this->subscribed_platform_group_ids = $subscribed_platform_group_ids;
    }

    /**
     * Builds the condition with the limited and excluded groups
     * 
     * @param $condition Condition
     * @return Condition
     */
    public function get_condition($condition)
    {
        $conditions = array();
        
        if ($this->limited_groups)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), 
                $this->limited_groups);
        }
        
        if ($this->excluded_groups)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID), 
                    $this->excluded_groups));
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
        if (! empty($this->subscribed_platform_group_ids))
        {
            return $this->subscribed_platform_group_ids;
        }
        
        return parent::get_root_ids();
    }

    /**
     * Retrieves the entity item ids relevant for a given user.
     * Overrides because only subscribed platformgroups need to
     * be checked. Also none of their parents as they are not subscribed in the course, and therefore cannot have
     * specific rights set to them
     * 
     * @param $user_id integer
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->platform_group_cache[$user_id]))
        {
            $this->platform_group_cache[$user_id] = \Chamilo\Core\Group\Storage\DataManager::retrieve_all_subscribed_groups_array(
                $user_id, 
                true);
        }
        return $this->platform_group_cache[$user_id];
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'platform_groups', 
            Translation::get('PlatformGroups'), 
            Manager::package(), 
            'platform_groups_feed', 
            array('course_id' => $this->course_id));
    }
}
