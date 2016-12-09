<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\UserExporter;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\UserExporter\UserExportExtender;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

/**
 * Extends the user exporter with additional data for the course groups
 * 
 * @package application\weblcms\tool\course_group
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupUserExportExtender implements UserExportExtender
{
    const EXPORT_COLUMN_COURSE_GROUPS = 'course_groups';

    /**
     * The id of the current course
     * 
     * @var int
     */
    private $course_id;

    /**
     * The constructor
     * 
     * @param int $course_id
     */
    public function __construct($course_id)
    {
        $this->course_id = $course_id;
    }

    /**
     * Exports additional headers
     * 
     * @return array
     */
    public function export_headers()
    {
        $headers = array();
        
        $headers[self::EXPORT_COLUMN_COURSE_GROUPS] = Translation::get('CourseGroups');
        
        return $headers;
    }

    /**
     * Exports additional data for a given user
     * 
     * @param User $user
     *
     * @return array
     */
    public function export_user(User $user)
    {
        $data = array();
        
        $course_groups = DataManager::retrieve_course_groups_from_user($user->get_id(), $this->course_id);
        
        $course_groups_subscribed = array();
        while ($course_group = $course_groups->next_result())
        {
            $course_groups_subscribed[] = $course_group->get_name();
        }
        
        $data[self::EXPORT_COLUMN_COURSE_GROUPS] = implode(", ", $course_groups_subscribed);
        
        return $data;
    }
}