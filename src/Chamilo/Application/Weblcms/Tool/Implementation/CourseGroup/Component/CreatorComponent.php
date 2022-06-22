<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::ADD_RIGHT))
        {
            throw new NotAllowedException();
        }
        $course_group_id = Request::get(self::PARAM_COURSE_GROUP);

        // $trail = BreadcrumbTrail::getInstance();

        $course = $this->get_course();
        $course_group = new CourseGroup();
        $course_group->set_course_code($course->get_id());
        $course_group->set_parent_id($course_group_id);

        $param_add_course_group[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
            self::ACTION_ADD_COURSE_GROUP;
        $param_add_course_group[self::PARAM_COURSE_GROUP] = $course_group_id;

        if ($_REQUEST['submit'] == 'AddTitles')
        {
            $form = new CourseGroupForm(
                $this->getCourseGroupDecoratorsManager(),
                CourseGroupForm::TYPE_ADD_COURSE_GROUP_TITLES,
                $course_group,
                $this->get_url($param_add_course_group),
                $this->getUser()
            );
            if ($form->validate())
            {
                $form->add_titles();
            }
            else
            {
                $form = new CourseGroupForm(
                    $this->getCourseGroupDecoratorsManager(),
                    CourseGroupForm::TYPE_CREATE,
                    $course_group,
                    $this->get_url($param_add_course_group),
                    $this->getUser()
                );
            }

            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $form = new CourseGroupForm(
                $this->getCourseGroupDecoratorsManager(),
                CourseGroupForm::TYPE_CREATE,
                $course_group,
                $this->get_url($param_add_course_group),
                $this->getUser()
            );

            if ($form->validate())
            {
                $succes = $form->create_course_group();

                if ($succes)
                {
                    $message = Translation::get(
                        'ObjectCreated',
                        array('OBJECT' => Translation::get('CourseGroup')),
                        StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                            'ObjectNotCreated',
                            array('OBJECT' => Translation::get('CourseGroup')),
                            StringUtilities::LIBRARIES
                        ) . '<br />' . implode('<br />', $course_group->getErrors());
                }
                $this->redirectWithMessage(
                    $message,
                    !$succes,
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_GROUPS,
                        self::PARAM_COURSE_GROUP => $course_group->get_parent_id()
                    )
                );
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

}
