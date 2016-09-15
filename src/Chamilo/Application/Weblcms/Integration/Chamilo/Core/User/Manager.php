<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\User;

use Chamilo\Core\User\UserInterface;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use HTML_Table;

class Manager implements UserInterface
{

    /*
     * (non-PHPdoc) @see \core\user\UserInterface::get_additional_user_information()
     */
    public static function get_additional_user_information(\Chamilo\Core\User\Storage\DataClass\User $user)
    {
        $html = array();

        $table = new HTML_Table(array('class' => 'table table-striped table-bordered table-hover table-responsive'));

        $table->setHeaderContents(0, 0, Translation::get('Courses'));
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'style' => 'text-align: center;'));

        $table->setHeaderContents(1, 0, Translation::get('CourseCode'));
        $table->setHeaderContents(1, 1, Translation::get('CourseName'));

        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_courses_from_user($user);

        if ($courses->size() == 0)
        {
            $table->setCellContents(2, 0, Translation::get('NoCourses'));
            $table->setCellAttributes(2, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
        }

        $index = 2;

        while ($course = $courses->next_result())
        {
            $redirect = new Redirect(
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                    \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->get_id()));
            $url = $redirect->getUrl();

            $url = '<a href="' . $url . '">';
            $table->setCellContents($index, 0, $url . $course->get_visual_code() . '</a>');
            $table->setCellAttributes($index, 0, array('style' => 'width: 150px;'));
            $table->setCellContents($index, 1, $url . $course->get_title() . '</a>');
            $index ++;
        }

        $table->altRowAttributes(1, array('class' => 'row_odd'), array('class' => 'row_even'), true);

        $html[] = $table->toHtml();

        return implode(PHP_EOL, $html);
    }
}
