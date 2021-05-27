<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.course_type
 */
class CourseRequestForm extends FormValidator
{
    const CHOOSE_DATE = 'choose date';

    const TYPE_CREATE = 1;

    const TYPE_EDIT = 2;

    const TYPE_VIEW = 3;

    private $form_type;

    private $course;

    private $parent;

    private $request;

    private $user_id;

    private $multiple_users;

    private $request_user_id;

    public function __construct(
        $form_type, $action, $course, $parent, $request, $multiple_users = false, $request_user_id = null
    )
    {
        parent::__construct('course_request', self::FORM_METHOD_POST, $action);
        $this->multiple_users = $multiple_users;
        $this->parent = $parent;
        $this->request = $request;
        $this->form_type = $form_type;
        $this->course = $course;
        $this->user_id = $parent->get_user_id();

        if (!$request_user_id)
        {
            $request_user_id = $this->user_id;
        }

        $this->request_user_id = $request_user_id;

        if ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creating_form();
        }

        if ($this->form_type == self::TYPE_VIEW)
        {
            $this->build_viewing_form();
        }

        $this->setDefaults();
    }

    public function build_creating_form()
    {
        $this->build_request_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Create', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_request_form()
    {
        $this->addElement('html', '<div class="clearfix"></div><br/>');
        if ($this->form_type == self::TYPE_CREATE)
        {

            $this->addElement('category', Translation::get('CourseRequestProperties'));
            if ($this->multiple_users)
            {
                $order = [];
                $order[] = new OrderBy(
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), SORT_ASC
                );
                $order[] = new OrderBy(
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), SORT_ASC
                );

                $parameters = new DataClassRetrievesParameters(null, null, null, $order);
                $users_result = DataManager::retrieves(
                    User::class, $parameters
                );
                $users = [];
                foreach($users_result as $user)
                {
                    $user_name = $user->get_fullname();
                    $users[$user->get_id()] = $user_name;
                }
                $this->addElement(
                    'select', CommonRequest::PROPERTY_USER_ID,
                    Translation::get('User', null, Manager::context()), $users
                );
            }
            else
            {
                $user_name = DataManager::retrieve_by_id(
                    User::class, (int) $this->request_user_id
                )->get_fullname();
                $this->addElement(
                    'static', 'user', Translation::get('User', null, Manager::context()), $user_name
                );
            }

            $course_name = $this->course->get_title();
            $this->addElement('static', 'course', Translation::get('CourseName'), $course_name);

            $this->add_textfield(CommonRequest::PROPERTY_SUBJECT, Translation::get('Subject'), true);

            $this->add_html_editor(
                CommonRequest::PROPERTY_MOTIVATION, Translation::get('Motivation'), true,
                array(FormValidatorHtmlEditorOptions::OPTION_TOOLBAR => 'BasicMarkup')
            );
        }

        if ($this->form_type == self::TYPE_VIEW)
        {
            $this->addElement(
                'category', Translation::get(ClassnameUtilities::getInstance()->getClassnameFromObject($this->request))
            );

            $name_user = DataManager::retrieve_by_id(
                User::class, (int) $this->request->get_user_id()
            )->get_fullname();
            $this->addElement(
                'static', 'request', Translation::get('User', null, Manager::context()), $name_user
            );

            $request_name =
                CourseDataManager::retrieve_by_id(Course::class, $this->request->get_course_id())->get_title();

            $this->addElement('static', 'request', Translation::get('CourseName'), $request_name);

            $request_subject = $this->request->get_subject();
            $this->addElement('static', 'request', Translation::get('Subject'), $request_subject);

            $motivation = $this->request->get_motivation();
            $this->addElement('static', 'request', Translation::get('Motivation'), $motivation);

            $creation_date = DatetimeUtilities::format_locale_date(null, $this->request->get_creation_date());
            $this->addElement('static', 'request', Translation::get('CreationDate'), $creation_date);

            $decision = $this->request->get_decision();
            $decision_date = DatetimeUtilities::format_locale_date(null, $this->request->get_decision_date());
            switch ($decision)
            {
                case CommonRequest::ALLOWED_DECISION :
                    $this->addElement('static', 'request', Translation::get('Decision'), Translation::get('Allowed'));
                    $this->addElement(
                        'static', 'request', Translation::get('ConfirmOn', null, Utilities::COMMON_LIBRARIES),
                        $decision_date
                    );
                    break;
                case CommonRequest::DENIED_DECISION :
                    $this->addElement('static', 'request', Translation::get('Decision'), Translation::get('Denied'));
                    $this->addElement(
                        'static', 'request', Translation::get('ConfirmOn', null, Utilities::COMMON_LIBRARIES),
                        $decision_date
                    );
                    break;
                default :
                    $this->addElement(
                        'static', 'request', Translation::get('Decision'), Translation::get('NoDecisionYet')
                    );
                    break;
            }
        }
    }

    public function build_viewing_form()
    {
        $this->build_request_form();
    }

    public function create_request()
    {
        $values = $this->exportValues();

        $course = $this->course;
        $request = $this->request;

        $request->set_course_id($course->get_id());

        if ($this->multiple_users)
        {
            $request->set_user_id($values[CommonRequest::PROPERTY_USER_ID]);
        }
        else
        {
            $request->set_user_id($this->request_user_id);
        }

        $request->set_subject($values[CommonRequest::PROPERTY_SUBJECT]);
        $request->set_motivation($values[CommonRequest::PROPERTY_MOTIVATION]);
        $request->set_creation_date(time());
        $request->set_decision_date($values[CommonRequest::PROPERTY_DECISION_DATE]);
        $request->set_decision(CommonRequest::NO_DECISION);

        if (!$request->create())
        {
            return false;
        }

        return true;
    }

    public function get_form_type()
    {
        return $this->form_type;
    }

    public function setDefaults($defaults = [])
    {
        $request = $this->request;
        $defaults[CourseRequest::PROPERTY_COURSE_ID] = $request->get_course_id();

        $defaults[CommonRequest::PROPERTY_USER_ID] = $request->get_user_id();
        $defaults[CommonRequest::PROPERTY_SUBJECT] = $request->get_subject();
        $defaults[CommonRequest::PROPERTY_MOTIVATION] = $request->get_motivation();
        $defaults[CommonRequest::PROPERTY_CREATION_DATE] = $request->get_creation_date();
        $defaults[CommonRequest::PROPERTY_DECISION_DATE] = $request->get_decision_date();

        parent::setDefaults($defaults);
    }
}
