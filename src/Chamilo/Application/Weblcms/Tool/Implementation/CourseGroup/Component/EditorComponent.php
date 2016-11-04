<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_editor.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class EditorComponent extends TabComponent
{

    public function renderTabContent()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $course_group_id = Request :: get(self :: PARAM_COURSE_GROUP);
        $this->set_parameter(self :: PARAM_COURSE_GROUP, $course_group_id);

        $course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $course_group_id);

        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get('EditorComponent', array('GROUPNAME' => $course_group->get_name()))));

        $form = new CourseGroupForm(
            CourseGroupForm :: TYPE_EDIT,
            $course_group,
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_EDIT_COURSE_GROUP,
                    self :: PARAM_COURSE_GROUP => $course_group_id)));

        if ($form->validate())
        {
            $succes = $form->update_course_group();

            if ($succes)
            {
                $message = Translation :: get(
                    'ObjectUpdated',
                    array('OBJECT' => Translation :: get('CourseGroup')),
                    Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get(
                    'ObjectNotUpdated',
                    array('OBJECT' => Translation :: get('CourseGroup')),
                    Utilities :: COMMON_LIBRARIES) . '<br />' . implode('<br />', $course_group->get_errors());
            }

            $this->redirect($message, ! $succes, array(self :: PARAM_ACTION => self :: ACTION_GROUP_DETAILS));
        }

        return $form->toHtml();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_group_editor');
    }
}
