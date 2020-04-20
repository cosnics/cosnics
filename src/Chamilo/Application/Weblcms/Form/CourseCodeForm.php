<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.course_type
 */
class CourseCodeForm extends FormValidator
{
    const TEMP_CODE = 'temp_code';

    private $course;

    private $parent;

    private $user;

    public function __construct($action, $course, $parent, $user)
    {
        parent::__construct('course_code', self::FORM_METHOD_POST, $action);
        $this->parent = $parent;
        $this->course = $course;
        $this->user = $user;

        $this->build_creating_form();

        $this->setDefaults();
    }

    public function build_code_form()
    {
        $this->addElement('category', Translation::get('CourseCodeProperties'));

        $course_name = $this->course->get_name();
        $this->addElement('static', 'course', Translation::get('Course'), $course_name);

        $user_name = $this->user->get_fullname();
        $this->addElement(
            'static', 'user', Translation::get('User', null, Manager::context()), $user_name
        );

        $this->add_textfield(self::TEMP_CODE, Translation::get('Code'));
    }

    public function build_creating_form()
    {
        $this->build_code_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Subscribe'), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function check_code()
    {
        $temp_code = $this->exportValue(self::TEMP_CODE);
        $code = $this->course->get_code();

        if ($temp_code == $code)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
