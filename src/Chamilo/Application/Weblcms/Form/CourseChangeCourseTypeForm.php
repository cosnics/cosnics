<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: group_move_form.class.php 224 2010-04-06 14:40:30Z yannick $
 * 
 * @package applicatie.lib.weblcms.course
 */
class CourseChangeCourseTypeForm extends FormValidator
{
    const SELECT_COURSE_TYPE = 'course_type';

    private $size;

    private $single_course_type_id;

    private $course;

    public function __construct($action, $course, $user)
    {
        parent::__construct('course_change_course_type', 'post', $action);
        $this->course = $course;
        $this->allow_no_course_type = $user->is_platform_admin() || Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype'));
        
        $this->build_form();
    }

    public function build_form()
    {
        $this->addElement('hidden', Course::PROPERTY_ID);
        
        $this->addElement(
            'select', 
            self::SELECT_COURSE_TYPE, 
            Translation::get('NewCourseType'), 
            $this->get_course_types());
        $this->addRule(
            'CourseType', 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('ChangeCourseType'), 
            null, 
            null, 
            'move');
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function get_selected_course_type()
    {
        return $this->exportValue(self::SELECT_COURSE_TYPE);
    }

    public function get_course_types()
    {
        $course_type_objects = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieves(
            CourseType::class_name(), 
            new DataClassRetrievesParameters());
        $course_types = array();
        if (empty($this->course_type_id) || $this->allow_no_course_type)
        {
            $course_types[0] = Translation::get('NoCourseType');
        }
        $this->size = $course_type_objects->size();
        if ($this->size != 0)
        {
            $count = 0;
            while ($course_type = $course_type_objects->next_result())
            {
                $course_types[$course_type->get_id()] = $course_type->get_name();
            }
            
            if (is_null($this->course_type_id) && $count == 0 && ! $this->allow_no_course_type)
            {
                $parameters = array(
                    'go' => Manager::ACTION_COURSE_CHANGE_COURSETYPE, 
                    'course' => $this->course->get_id());
                $this->parent->simple_redirect($parameters);
            }
            $this->addElement(
                'select', 
                Course::PROPERTY_ID, 
                Translation::get('CourseType'), 
                $course_types, 
                array('class' => 'course_type_selector'));
            $this->addRule(
                'CourseType', 
                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                'required');
        }
        else
        {
            $course_type_name = Translation::get('NoCourseType');
            if (! is_null($this->course_type_id))
            {
                $course_type_name = $this->object->get_course_type()->get_name();
            }
            $this->addElement('static', 'course_type', Translation::get('CourseType'), $course_type_name);
            $this->addElement('hidden', Course::PROPERTY_ID);
        }
        return $course_types;
    }

    public function get_new_parent()
    {
        return $this->exportValue(self::SELECT_COURSE_TYPE);
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
