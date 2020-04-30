<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseIntegrationTest extends DataClassIntegrationTestCase
{

    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_course_type_id($entity->get_course_type_id() + 1);
        $entity->set_titular_id($entity->get_titular_id() + 2);
        $entity->set_title(str_rot13($entity->get_title()));
        $entity->set_visual_code(str_rot13($entity->get_visual_code()));
        $entity->set_creation_date($entity->get_creation_date() - self::WEEK);
        $entity->set_expiration_date($entity->get_expiration_date() + self::WEEK);
        $entity->set_last_edit($entity->get_last_edit() - self::DAY);
        $entity->set_last_visit($entity->get_last_visit() + self::DAY);
        $entity->set_category_id($entity->get_category_id() + 1);
        $entity->set_language($entity->get_language() !== 'en' ? 'en' : 'nl');
    }
}
