<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Ajax\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Ajax\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 * Ajax backing to retrieve details of a course group.
 */
class GetCourseGroupComponent extends Manager
{
    const PARAM_COURSE_GROUP_ID = 'course_group_id';
    const PROPERTY_COURSE_GROUP = 'course_group';

    public function run()
    {
        $course_group = $this->get_course_group();
        
        if (! $course_group)
        {
            JsonAjaxResult::not_found(
                Translation::get('CourseGroupNotFound'), 
                array('id' => $this->getPostDataValue(self::PARAM_COURSE_GROUP_ID)));
        }
        
        $properties = $course_group->getDefaultProperties() + $course_group->getOptionalProperties();
        $properties['is_root'] = $course_group->is_root();
        
        $result = new JsonAjaxResult(200, $properties);
        $result->display();
    }

    /**
     *
     * @return CourseGroup
     */
    public function get_course_group()
    {
        $id = $this->getPostDataValue(self::PARAM_COURSE_GROUP_ID);
        return DataManager::retrieve_by_id(CourseGroup::class, $id);
    }

    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_COURSE_GROUP_ID);
    }
}
