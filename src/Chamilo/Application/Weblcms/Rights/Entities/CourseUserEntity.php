<?php
namespace Chamilo\Application\Weblcms\Rights\Entities;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extension on the user entity specific for the course to limit the users
 *
 * @author Sven Vanpoucke
 */
class CourseUserEntity extends UserEntity
{

    /**
     * Singleton
     */
    private static $instance;

    /**
     * The current course id
     *
     * @var int
     */
    private $course_id;

    /**
     * Excludes the users by id
     *
     * @var Array<int>
     */
    private $excluded_users;

    /**
     * Limits the users by id
     *
     * @var Array<int>
     */
    private $limited_users;

    public function __construct($course_id = 0, $limited_users = [], $excluded_users = [])
    {
        $this->limited_users = $limited_users;
        $this->excluded_users = $excluded_users;
        $this->course_id = $course_id;
    }

    public function exclude_users($excluded_users)
    {
        $this->excluded_users = $excluded_users;
    }

    public function getElementFinderType()
    {
        $elementFinderType = static::getElementFinderTypeInstance();
        $elementFinderType->set_parameters(array('course_id' => $this->course_id));

        return $elementFinderType;
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public static function getElementFinderTypeInstance()
    {
        return new AdvancedElementFinderElementType(
            'users', Translation::get('CourseUsers'), Manager::CONTEXT, 'course_users_feed'
        );
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Builds the condition with the limited and excluded users
     *
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    public function get_condition(Condition $condition = null)
    {
        $conditions = [];

        if ($this->limited_users)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_ID), $this->limited_users
            );
        }

        if ($this->excluded_users)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID), $this->excluded_users
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

    public function limit_users($limited_users)
    {
        $this->limited_users = $limited_users;
    }
}
