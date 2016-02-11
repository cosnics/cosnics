<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Weblcms\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HomeComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-10">';
        $html[] = $this->getCourseListRenderer()->as_html();
        $html[] = '</div>';

        $html[] = '<div class="col-md-2">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer
     */
    protected function getCourseListRenderer()
    {
        $renderer = new CourseTypeCourseListRenderer($this);
        $renderer->show_new_publication_icons();
        return $renderer;
    }

    /**
     *
     * @return string
     */
    protected function renderMenu()
    {
        $buttonToolBar = new ButtonToolBar();

        if ($this->get_user()->is_platform_admin())
        {
            $buttonToolBar->addButtonGroup($this->buildAdminCourseManagementButtonGroup($buttonToolBar));
        }
        elseif ($this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview"))
        {
            $buttonToolBar->addButtonGroup($this->buildTeacherManagementButtonGroup());
        }

        $buttonToolBar->addButtonGroup($this->buildUserManagementButtonGroup());

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    protected function buildAdminCourseManagementButtonGroup()
    {
        $buttonGroup = new ButtonGroup(array(), array('btn-group-vertical'));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('CourseCreate'),
                Theme :: getInstance()->getCommonImagePath('Action/Create'),
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                        \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('CourseList'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('RequestList'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REQUEST))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('UserRequestList'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_ADMIN_REQUEST_BROWSER))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('CourseCategoryManagement'),
                Theme :: getInstance()->getCommonImagePath('Action/Move'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('ImportCourseCSV'),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_COURSES))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('ImportUsersForCourseCSV'),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_COURSE_USERS))));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('Reporting'),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REPORTING))));

        return $buttonGroup;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    protected function buildTeacherManagementButtonGroup()
    {
        $buttonGroup = new ButtonGroup(array(), array('btn-group-vertical'));

        $courseManagementRights = CourseManagementRights :: get_instance();

        $countDirect = $countRequest = 0;

        $courseTypes = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_active_course_types();

        while ($courseType = $courseTypes->next_result())
        {
            if ($courseManagementRights->is_allowed(
                CourseManagementRights :: CREATE_COURSE_RIGHT,
                $courseType->get_id(),
                CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                $countDirect ++;
            }
            elseif ($courseManagementRights->is_allowed(
                CourseManagementRights :: REQUEST_COURSE_RIGHT,
                $courseType->get_id(),
                CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                $countRequest ++;
            }
        }

        if (PlatformSetting :: get('allow_course_creation_without_coursetype', 'Chamilo\Application\Weblcms'))
        {
            $countDirect ++;
        }

        if ($countDirect)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('CourseCreate'),
                    Theme :: getInstance()->getCommonImagePath('Action/Create'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE))));
        }

        if ($countRequest)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('CourseRequest'),
                    Theme :: getInstance()->getCommonImagePath('Action/Create'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_REQUEST,
                            \Chamilo\Application\Weblcms\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager :: ACTION_CREATE))));
        }

        if (\Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->request_is_allowed())
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('RequestList'),
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REQUEST))));
        }

        if (\Chamilo\Application\Weblcms\Admin\Storage\DataManager :: user_is_admin($this->get_user()))
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('TypeName', null, \Chamilo\Application\Weblcms\Admin\Manager :: package()),
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Admin\Manager :: package(),
                            Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Admin\Manager :: ACTION_BROWSE))));
        }

        return $buttonGroup;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup
     */
    protected function buildUserManagementButtonGroup()
    {
        $buttonGroup = new ButtonGroup(array(), array('btn-group-vertical'));

        $buttonGroup->addButton(
            new Button(
                Translation :: get('SortMyCourses'),
                Theme :: getInstance()->getCommonImagePath('Action/Reset'),
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_MANAGER_SORT))));

        if (PlatformSetting :: get('show_subscribe_button_on_course_home', self :: package()))
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('CourseSubscribe'),
                    Theme :: getInstance()->getCommonImagePath('Action/Subscribe'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_UNSUBSCRIBED_COURSES))));

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('CourseUnsubscribe'),
                    Theme :: getInstance()->getCommonImagePath('Action/Unsubscribe'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_SUBSCRIBED_COURSES))));
        }

        return $buttonGroup;
    }

    /**
     *
     * @return boolean
     */
    public function show_empty_courses()
    {
        return false;
    }
}
