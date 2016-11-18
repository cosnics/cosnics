<?php
namespace Chamilo\Application\Weblcms\Rights\Entities;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Extension on the user entity specific for the course to limit the users
 * 
 * @author Sven Vanpoucke
 */
class CourseUserEntity extends UserEntity
{

    /**
     * Limits the users by id
     * 
     * @var Array<int>
     */
    private $limited_users;

    /**
     * Excludes the users by id
     * 
     * @var Array<int>
     */
    private $excluded_users;

    /**
     * The current course id
     * 
     * @var int
     */
    private $course_id;

    /**
     * Singleton
     */
    private static $instance;

    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($course_id, $limited_users = array(), $excluded_users = array())
    {
        $this->limited_users = $limited_users;
        $this->excluded_users = $excluded_users;
        $this->course_id = $course_id;
    }

    public function limit_users($limited_users)
    {
        $this->limited_users = $limited_users;
    }

    public function exclude_users($excluded_users)
    {
        $this->excluded_users = $excluded_users;
    }

    /**
     * Builds the condition with the limited and excluded users
     * 
     * @param $condition Condition
     * @return Condition
     */
    public function get_condition(Condition $condition = null)
    {
        $conditions = array();
        
        if ($this->limited_users)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), 
                $this->limited_users);
        }
        
        if ($this->excluded_users)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), 
                    $this->excluded_users));
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
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'users', 
            Translation::get('CourseUsers'), 
            Manager::package(), 
            'course_users_feed', 
            array('course_id' => $this->course_id));
    }
}
