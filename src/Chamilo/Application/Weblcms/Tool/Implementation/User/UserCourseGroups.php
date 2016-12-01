<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class UserCourseGroups
{

    /**
     * The user ID
     */
    private $user_id;

    /**
     * The course ID
     */
    private $course_id;

    /**
     * Indicates if a border should be included
     */
    private $border;

    /**
     * Constructor
     * 
     * @param $user_id int
     * @param $border boolean Indicates if a border should be included
     */
    public function __construct($user_id, $course_id, $border = true)
    {
        $this->user_id = $user_id;
        $this->border = $border;
        $this->course_id = $course_id;
    }

    /**
     * Returns a HTML representation of the user details
     * 
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div ';
        if ($this->border)
        {
            $html[] = 'class="user_details"';
        }
        else
        {
            $html[] = 'class="vertical_space"';
        }
        $html[] = 'style="clear: both;background-image: url(' . Theme::getInstance()->getImagePath(
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager::context(), 
            'Logo/22') . ');">';
        $html[] = '<div class="title">';
        $html[] = Translation::get('Coursegroups');
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul>';
        $course_groups = CourseGroupDataManager::retrieve_course_groups_from_user($this->user_id, $this->course_id);
        if ($course_groups->size() > 0)
        {
            while ($course_group = $course_groups->next_result())
            {
                $html[] = '<li>';
                $html[] = $course_group->get_name();
                $html[] = '</li>';
            }
        }
        else
        {
            $html[] = Translation::get('NoCourseGroupSubscriptions');
        }
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '<div style="clear:both;"><span></span></div>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }
}
