<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Form\CourseRequestForm;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component that will allow the user to do course requests for multiple users.
 *
 * @author Minas Zilyas - Hogeschool Gent
 * @package application\weblcms\tool\user
 */
class RequestSubscribeUsersComponent extends Manager implements DelegateComponent
{

    private $user_ids;

    private $form;

    public function run()
    {
        $request = new CourseRequest();
        $this->form = new CourseRequestForm(
            CourseRequestForm::TYPE_CREATE, $this->get_url(), $this->get_course(), $this, $request, false,
            Session::get_user_id()
        );

        $this->user_ids = $this->get_selected_user_ids();
        $users = $this->retrieve_selected_users($this->user_ids);

        if ($this->form->validate())
        {
            $values = $this->form->exportValues();
            $success_requests = $this->create_course_request_for_users($users, $request, $values);
            $array_type = array();
            $array_type['go'] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_WEBLCMS_HOME;
            $this->redirect(
                Translation::get($success_requests ? 'CourseCreateRequestSent' : 'CourseCreateRequestNotSent'),
                ($success_requests ? false : true), $array_type,
                array(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE)
            );
        }
        else
        {
            $this->form->addElement('hidden', 'user_ids');
            $this->form->setDefaults(array('user_ids' => json_encode($this->user_ids)));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->display_selected_users($users);
            $html[] = $this->form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER)),
                Translation::get('SubscribeBrowserComponent')
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER)),
                Translation::get('RequestSubscribeUsersComponent')
            )
        );
    }

    /**
     * Function that will check if a student is allowed to be subscribed to the course.
     *
     * @param int $user_id
     *
     * @return bool
     */
    protected function check_student_allowed_to_subscribe($user_id)
    {
        return ($this->get_user()->is_platform_admin() || CourseManagementRights::getInstance()->is_allowed(
                CourseManagementRights::TEACHER_REQUEST_SUBSCRIBE_RIGHT, $this->get_course_id(),
                CourseManagementRights::TYPE_COURSE, $user_id
            ));
    }

    /**
     * Will loop through the users to create a course request for every selected user.
     *
     * @param \user\User[] $users
     * @param $request
     * @param String[] $values
     *
     * @return bool
     */
    protected function create_course_request_for_users($users, $request, $values)
    {
        $course_id = $this->get_course_id();

        if ($request instanceof Request)
        {
            $request->set_course_name(
                $values[Request::PROPERTY_NAME]
            );
            $request->set_course_type_id(
                $values[Request::PROPERTY_COURSE_TYPE_ID]
            );
        }
        else
        {
            $request->set_course_id($course_id);
        }

        $request->set_subject($values[CommonRequest::PROPERTY_SUBJECT]);
        $request->set_motivation($values[CommonRequest::PROPERTY_MOTIVATION]);
        $request->set_creation_date(time());
        $request->set_decision_date($values[CommonRequest::PROPERTY_DECISION_DATE]);
        $request->set_decision(CommonRequest::NO_DECISION);

        $success_requests = true;
        foreach ($users as $user)
        {
            if (!$this->check_student_allowed_to_subscribe($user->get_id()))
            {
                continue;
            }

            $request->set_user_id($user->get_id());

            if (!$request->create())
            {
                $success_requests &= false;
            }
        }

        return $success_requests;
    }

    /**
     *
     * @param \user\User[] $users
     */
    public function display_selected_users($users)
    {
        $html = array();

        $html[] = '<div class="attachments" style="margin-top: 1em; background-image:url(' .
            Theme::getInstance()->getCommonImagePath('Place/Selected') . ')">';

        if (count($users) > 1)
        {
            $html[] = '<div class="attachments_title">' . htmlentities(Translation::get('SelectedUsers')) . '</div>';
        }
        else
        {
            $html[] = '<div class="attachments_title">' . htmlentities(Translation::get('SelectedUser')) . '</div>';
        }

        $html[] = '<ul class="attachments_list">';
        foreach ($users as $user)
        {
            $html[] = '<li><img src="' . Theme::getInstance()->getImagePath(
                    '\Chamilo\Core\User', 'Logo/' . Theme::ICON_MINI
                ) . '" alt="' . htmlentities(Translation::get('TypeName')) . '"/> ' . $user->get_fullname() . ' (' .
                $user->get_official_code() . ')' . '</li>';
        }
        $html[] = '</ul>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * This method will look for selected user_ids in the HTTP header or the form.
     * When first visiting the page the
     * user_ids will be located in the HTTP header. They are inserted into the form as an invisible field to store this
     * information.
     *
     * @return mixed null string
     * @throws \common\libraries\NoObjectSelectedException
     */
    public function get_selected_user_ids()
    {
        $user_ids = $this->getRequest()->get(self::PARAM_OBJECTS);

        if (!$user_ids)
        {
            $user_ids = json_decode($this->form->getSubmitValue('user_ids'));
        }

        if (!is_array($user_ids))
        {
            $user_ids = array($user_ids);
        }

        if (count($user_ids) == 0)
        {
            Throw new NoObjectSelectedException(Translation::get('User'));
        }

        return $user_ids;
    }

    /**
     * Will retrieve the selected users from the database.
     * Ignores and removes users from the array that are not allowed
     * to be subscribed.
     *
     * @return array
     */
    public function retrieve_selected_users()
    {
        $users = array();
        $allowed_user_ids = array();

        foreach ($this->user_ids as $id)
        {
            if (!$this->check_student_allowed_to_subscribe($id))
            {
                continue;
            }

            $user = DataManager::retrieve_by_id(User::class_name(), $id);
            if ($user)
            {
                $allowed_user_ids[] = $id;
                $users[] = $user;
            }
        }
        $this->user_ids = $allowed_user_ids;

        if (!$users || count($users) == 0)
        {
            Throw new NoObjectSelectedException(Translation::get('User'));
        }

        return $users;
    }
}