<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package application.lib.weblcms.course_type
 */
class CourseTypeUserCategoryRelCourse extends DataClass implements DisplayOrderDataClassListenerSupport
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_COURSE_ID = 'course_id';
    public const PROPERTY_COURSE_TYPE_USER_CATEGORY_ID = 'course_type_user_category_id';
    public const PROPERTY_SORT = 'sort';
    public const PROPERTY_USER_ID = 'user_id';

    public function __construct($default_properties = [], $optional_properties = [])
    {
        parent::__construct($default_properties = $optional_properties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Get the default properties of all user course user categories.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return [
            self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID,
            self::PROPERTY_COURSE_ID,
            self::PROPERTY_USER_ID,
            self::PROPERTY_SORT
        ];
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getDisplayOrderContextProperties(): array
    {
        return [
            new PropertyConditionVariable(self::class, self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID),
            new PropertyConditionVariable(self::class, self::PROPERTY_USER_ID)
        ];
    }

    public function getDisplayOrderProperty(): PropertyConditionVariable
    {
        return new PropertyConditionVariable(self::class, self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_type_user_category_rel_course';
    }

    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    public function get_course_type_user_category_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID);
    }

    public function get_sort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }

    public function set_course_type_user_category_id($course_type_user_category_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID, $course_type_user_category_id);
    }

    public function set_sort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
