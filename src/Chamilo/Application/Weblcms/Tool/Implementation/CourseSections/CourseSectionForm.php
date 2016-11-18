<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_section_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_sections
 */
class CourseSectionForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'CourseSettingUpdated';
    const RESULT_ERROR = 'CourseSettingUpdateFailed';

    private $course_section;

    private $form_type;

    public function __construct($form_type, $course_section, $action)
    {
        parent::__construct('course_sections', 'post', $action);
        
        $this->course_section = $course_section;
        $this->form_type = $form_type;
        
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', CourseSection::PROPERTY_NAME, Translation::get('Name'), array("size" => "50"));
        $this->addRule(
            CourseSection::PROPERTY_NAME, 
            Translation::get('Required', null, Utilities::COMMON_LIBRARIES), 
            'required');
        $this->addElement(
            'checkbox', 
            CourseSection::PROPERTY_VISIBLE, 
            Translation::get('Visible', null, Utilities::COMMON_LIBRARIES));
        
        // $this->addElement('submit', 'course_section_sections', 'OK');
    }

    public function build_editing_form()
    {
        // $course_section = $this->course_section;
        // $parent = $this->parent;
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseSection::PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_course_section()
    {
        $course_section = $this->course_section;
        $values = $this->exportValues();
        
        $course_section->set_name($values[CourseSection::PROPERTY_NAME]);
        $visible = $values[CourseSection::PROPERTY_VISIBLE] ? $values[CourseSection::PROPERTY_VISIBLE] : 0;
        $course_section->set_visible($visible);
        
        return $course_section->update();
    }

    public function create_course_section()
    {
        $course_section = $this->course_section;
        $values = $this->exportValues();
        
        $name = $values[CourseSection::PROPERTY_NAME];
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class_name(), CourseSection::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->course_section->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class_name(), CourseSection::PROPERTY_NAME), 
            new StaticConditionVariable($name));
        $condition = new AndCondition($conditions);
        
        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            CourseSection::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        if ($course_sections->size() > 0)
        {
            return false;
        }
        
        $course_section->set_name($name);
        $visible = $values[CourseSection::PROPERTY_VISIBLE] ? $values[CourseSection::PROPERTY_VISIBLE] : 0;
        $course_section->set_visible($visible);
        
        return $course_section->create();
    }

    /**
     * Sets default values.
     * 
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $course_section = $this->course_section;
        $defaults[CourseSection::PROPERTY_ID] = $course_section->get_id();
        $defaults[CourseSection::PROPERTY_NAME] = $course_section->get_name();
        $defaults[CourseSection::PROPERTY_VISIBLE] = is_null($course_section->is_visible()) ? 1 : $course_section->is_visible();
        parent::setDefaults($defaults);
    }

    public function get_course_section()
    {
        return $this->course_section;
    }
}
