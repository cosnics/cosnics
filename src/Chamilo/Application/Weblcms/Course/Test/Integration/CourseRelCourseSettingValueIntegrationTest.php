<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseRelCourseSettingValueIntegrationTest extends DataClassIntegrationTestCase
{
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_course_rel_course_setting_id($entity->get_course_rel_course_setting_id() + 1);
        $entity->set_value(str_rot13($entity->get_value()));
    }
}
