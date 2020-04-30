<?php
namespace Chamilo\Application\Weblcms\Course\Test\Integration;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Libraries\Storage\DataClassIntegrationTestCase;

/**
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class CourseGroupRelationIntegrationTest extends DataClassIntegrationTestCase
{
    protected static function modifyEntity(&$entity, $alter_id)
    {
        if ($alter_id)
        {
            $entity->set_id($entity->get_id() + 5);
        }
        $entity->set_group_id($entity->get_group_id() + 1);
        $entity->set_course_id($entity->get_course_id() + 2);
        $new_status =
            $entity->get_status() === CourseGroupRelation::STATUS_TEACHER ? CourseGroupRelation::STATUS_STUDENT :
                CourseGroupRelation::STATUS_TEACHER;
        $entity->set_status($new_status);
    }
}
