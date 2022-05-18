<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application.lib.weblcms.course_group
 */
class CourseGroupUserRelation extends DataClass
{
    const PROPERTY_COURSE_GROUP = 'course_group_id';
    const PROPERTY_SUBSCRIPTION_TIME = 'subscription_time';
    const PROPERTY_USER = 'user_id';

    private $defaultProperties;

    /**
     * Creates a new course user relation object.
     *
     * @param $id int The numeric ID of the course user relation object. May be omitted if creating a new object.
     * @param $defaultProperties array The default properties of the course user relation object. Associative array.
     */
    public function __construct($defaultProperties = [])
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Creates a new subscription and adds the subscription time
     */
    public function create()
    {
        if (!$this->get_subscription_time())
        {
            $this->set_subscription_time(time());
        }

        return parent::create();
    }

    /**
     * Returns the course group of this course group user relation object
     *
     * @return int
     */
    public function get_course_group()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_GROUP);
    }

    /**
     * Gets the default properties of this course user relation object.
     *
     * @return array An associative array containing the properties.
     */
    public function getDefaultProperties()
    {
        return $this->defaultProperties;
    }

    public function setDefaultProperties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this course user relation object by name.
     *
     * @param $name string The name of the property.
     */
    public function getDefaultProperty($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Get the default properties of all course user relations.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return array(self::PROPERTY_COURSE_GROUP, self::PROPERTY_USER);
    }

    /**
     * Returns the subscription time
     *
     * @return int
     */
    public function get_subscription_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBSCRIPTION_TIME);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_group_user_relation';
    }

    /**
     * Returns the user of this course user relation object
     *
     * @return int
     */
    public function get_user()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER);
    }

    /**
     * Gets the user
     *
     * @return User
     * @todo The functions get_user and set_user should work with a User object and not with the user id's!
     */
    public function get_user_object()
    {
        return DataManager::retrieve_by_id(
            User::class, $this->get_user()
        );
    }

    /**
     * Sets the course group of this course group user relation object
     *
     * @param $course int
     */
    public function set_course_group($course_group)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_GROUP, $course_group);
    }

    /**
     * Sets a default property of this course user relation object by name.
     *
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function setDefaultProperty($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the subscription time
     *
     * @param int $subscription_time
     */
    public function set_subscription_time($subscription_time)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBSCRIPTION_TIME, $subscription_time);
    }

    /**
     * Sets the user of this course user relation object
     *
     * @param $user int
     */
    public function set_user($user)
    {
        $this->setDefaultProperty(self::PROPERTY_USER, $user);
    }
}
