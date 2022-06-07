<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.course
 */
class CourseEntityImportForm extends FormValidator
{

    public function __construct($action)
    {
        parent::__construct('course_user_import', self::FORM_METHOD_POST, $action);

        $this->addElement('file', 'file', Translation::get('FileName'));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Ok', null, StringUtilities::LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
