<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.course_type
 */
class CourseTypeSelectForm extends FormValidator
{
    const RESULT_ERROR = 'ObjectUpdateFailed';

    const RESULT_SUCCESS = 'ObjectUpdated';

    const SELECT_ELEMENT = 'course_type';

    const TYPE_CREATE = 1;

    const TYPE_EDIT = 2;

    private $size;

    private $single_course_type_id;

    public function __construct($action)
    {
        parent::__construct('course_type_select', self::FORM_METHOD_POST, $action);
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $this->addElement('hidden', Course::PROPERTY_ID);

        $course_type_objects =
            DataManager::retrieve_active_course_types();
        $course_types = [];
        $this->size = $course_type_objects->count();
        if ($this->size == 1)
        {
            $this->single_course_type_id = $course_type_objects->current()->get_id();
        }
        else
        {
            foreach($course_type_objects as $course_type)
            {
                $course_types[$course_type->get_id()] = $course_type->get_name();
            }
        }

        $this->addElement('select', self::SELECT_ELEMENT, Translation::get('CourseType'), $course_types);
        $this->addRule(
            'CourseType', Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Select', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function get_selected_id()
    {
        if ($this->size != 1)
        {
            $values = $this->exportValues();

            return $values[self::SELECT_ELEMENT];
        }
        else
        {
            return $this->single_course_type_id;
        }
    }

    public function get_size()
    {
        return $this->size;
    }
}
