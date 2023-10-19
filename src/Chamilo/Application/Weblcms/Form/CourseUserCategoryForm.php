<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class CourseUserCategoryForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const COURSE_TYPE_TARGET = 'course_type_target';
    const COURSE_TYPE_TARGET_ELEMENTS = 'course_type_target_elements';
    const COURSE_TYPE_TARGET_OPTION = 'course_type_target_option';

    private $course_user_category;

    private $user;

    private $parent;

    public function __construct($form_type, $course_user_category, $user, $action, $parent)
    {
        parent::__construct('course_settings', 'post', $action);
        
        $this->course_user_category = $course_user_category;
        $this->user = $user;
        $this->parent = $parent;
        
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
        $this->addElement(
            'text', 
            CourseUserCategory::PROPERTY_TITLE, 
            Translation::get('Title', null, Utilities::COMMON_LIBRARIES), 
            array("maxlength" => 50, "size" => 50));
        $this->addRule(
            CourseUserCategory::PROPERTY_TITLE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $attributes = array();
        $attributes['search_url'] = Path::getInstance()->getBasePath(true) .
             'index.php?go=XmlCourseTypeFeed&application=Chamilo%5CApplication%5CWeblcms%5CCourseType%5CAjax';
        $locale = array();
        $locale['Display'] = Translation::get('SelectRecipients');
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);
        $locale['load_elements'] = true;
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        
        $element_finder = $this->createElement(
            'user_group_finder', 
            self::COURSE_TYPE_TARGET_ELEMENTS, 
            Translation::get('CourseType'), 
            $attributes['search_url'], 
            $attributes['locale'], 
            $attributes['defaults']);

        if(is_array($attributes['exclude']))
            $element_finder->excludeElements($attributes['exclude']);

        $this->addElement($element_finder);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseUserCategory::PROPERTY_ID);
        
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

    public function update_course_user_category()
    {
        $course_user_category = $this->course_user_category;
        $values = $this->exportValues();
        
        // EDIT TITLE
        $course_user_category->set_title($values[CourseUserCategory::PROPERTY_TITLE]);
        
        if (! $course_user_category->update())
        {
            return false;
        }
        
        // EDIT COURSE TYPE
        // get the existing course types
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class_name(), 
                CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID), 
            new StaticConditionVariable($course_user_category->get_id()));
        
        $existing_types = DataManager::retrieves(
            CourseTypeUserCategory::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        if ($existing_types)
        {
            $existing_types = $existing_types->as_array();
        }
        else
        {
            $existing_types = array();
        }
        
        // get the selected course types
        $selected_types = $this->get_selected_course_types();
        
        // uses the compare function of CourseTypeUserCategory
        $compare_class = "Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory";
        $compare_method = "compare";
        
        // create the types that are selected but don't exist, and delete the
        // types that are no longer selected.
        // update is not needed at the moment, but if it ever is: just update
        // all elements that are in both arrays
        $to_be_created = array_udiff($selected_types, $existing_types, array($compare_class, $compare_method));
        $to_be_deleted = array_udiff($existing_types, $selected_types, array($compare_class, $compare_method));
        
        // process changes
        $failures = 0;
        foreach ($to_be_created as $create_this)
        {
            if (! $create_this->create())
            {
                $failures ++;
            }
        }
        
        $failures += count($to_be_deleted);
        foreach ($to_be_deleted as $delete_this)
        {
            if (! $delete_this->delete())
            {
                $failures ++;
            }
        }
        
        return ($failures == 0);
    }

    public function create_course_user_category()
    {
        $values = $this->exportValues();
        $course_types = $this->get_selected_course_types();
        
        if (count($course_types) == 0)
        {
            return false;
        }
        
        $this->course_user_category->set_id($values[CourseUserCategory::PROPERTY_ID]);
        $this->course_user_category->set_title($values[CourseUserCategory::PROPERTY_TITLE]);
        
        if (! $this->course_user_category->create())
        {
            return false;
        }
        
        foreach ($course_types as $course_type)
        {
            $course_type->set_course_user_category_id($this->course_user_category->get_id());
            
            if (! $course_type->create())
            {
                return false;
            }
        }
        
        return true;
    }

    public function get_selected_course_types()
    {
        $values = $this->exportValues();
        $course_type_user_category_id = $this->course_user_category->get_id();
        $selected_course_types_array = array();
        
        foreach ($values[self::COURSE_TYPE_TARGET_ELEMENTS]['coursetype'] as $value)
        {
            $selected_course_type_user_category = new CourseTypeUserCategory();
            $selected_course_type_user_category->set_course_type_id($value);
            $selected_course_type_user_category->set_user_id($this->user->get_id());
            $selected_course_type_user_category->set_course_user_category_id($course_type_user_category_id);
            $selected_course_types_array[] = $selected_course_type_user_category;
        }
        
        return $selected_course_types_array;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults(array $defaults = [], $filter = null)
    {
        $course_user_category = $this->course_user_category;
        $defaults[CourseUserCategory::PROPERTY_TITLE] = $course_user_category->get_title();
        
        if (! is_null($course_user_category->get_id()))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory::class_name(), 
                    CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID), 
                new StaticConditionVariable($course_user_category->get_id()));
            
            $course_types = DataManager::retrieves(
                CourseTypeUserCategory::class_name(), 
                new DataClassRetrievesParameters($condition));
            
            while ($type = $course_types->next_result())
            {
                $selected_course_type = $this->get_course_type_array($type->get_course_type_id());
                $defaults[self::COURSE_TYPE_TARGET_ELEMENTS][$selected_course_type['id']] = $selected_course_type;
            }
            
            if (count($defaults[self::COURSE_TYPE_TARGET_ELEMENTS]) > 0)
            {
                $active = $this->getElement(self::COURSE_TYPE_TARGET_ELEMENTS);
                $active->setValue($defaults[self::COURSE_TYPE_TARGET_ELEMENTS]);
            }
        }
        
        parent::setDefaults($defaults);
    }

    public function get_course_type_array($course_type_id)
    {
        $selected_course_type = array();
        $selected_course_type['classes'] = 'type type_course_type';
        if ($course_type_id != 0)
        {
            $course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_by_id(
                CourseType::class_name(), 
                $course_type_id);
            
            $selected_course_type['id'] = 'coursetype_' . $course_type->get_id();
            $selected_course_type['title'] = $course_type->get_title();
            $selected_course_type['description'] = $course_type->get_title();
        }
        else
        {
            $selected_course_type['id'] = 'coursetype_0';
            $selected_course_type['title'] = Translation::get('NoCourseType');
            $selected_course_type['description'] = Translation::get('NoCourseType');
        }
        return $selected_course_type;
    }
}
