<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

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
        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $glyph = new FontAwesomeGlyph('chalkboard', array(), null, 'fas');
        $html[] = $glyph->render() . '&nbsp;' . Translation::get('Coursegroups');

        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $course_groups = CourseGroupDataManager::retrieve_course_groups_from_user($this->user_id, $this->course_id);

        if ($course_groups->size() > 0)
        {
            $html[] = '<ul>';

            while ($course_group = $course_groups->next_result())
            {
                $html[] = '<li>';
                $html[] = $course_group->get_name();
                $html[] = '</li>';
            }

            $html[] = '</ul>';
        }
        else
        {
            $html[] = Translation::get('NoCourseGroupSubscriptions');
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
