<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;

/**
 * @package application.lib.weblcms.course
 */
class CourseRequest extends CommonRequest
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_COURSE_ID = 'course_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_COURSE_ID]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_request';
    }

    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    public function set_course_id($course_id)
    {
        return $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }
}
