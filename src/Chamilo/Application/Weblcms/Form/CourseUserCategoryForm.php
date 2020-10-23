<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CourseUserCategoryForm extends FormValidator
{
    const COURSE_TYPE_TARGET = 'course_type_target';

    const COURSE_TYPE_TARGET_ELEMENTS = 'course_type_target_elements';

    const COURSE_TYPE_TARGET_OPTION = 'course_type_target_option';

    const TYPE_CREATE = 1;

    const TYPE_EDIT = 2;

    private $course_user_category;

    private $user;

    private $parent;

    public function __construct($form_type, $course_user_category, $user, $action, $parent)
    {
        parent::__construct('course_settings', self::FORM_METHOD_POST, $action);

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
        $this->add_textfield(
            CourseUserCategory::PROPERTY_TITLE, Translation::get('Title', null, Utilities::COMMON_LIBRARIES), true,
            array("maxlength" => 50, "size" => 50)
        );
        $this->addRule(
            CourseUserCategory::PROPERTY_TITLE,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'course_types', Translation::get('ContentObjects'), 'Chamilo\Application\Weblcms\CourseType\Ajax',
                'CourseTypeFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', self::COURSE_TYPE_TARGET_ELEMENTS, Translation::get('CourseType'), $types
        );
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', CourseUserCategory::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Update', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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

        if (!$this->course_user_category->create())
        {
            return false;
        }

        foreach ($course_types as $course_type)
        {
            $course_type->set_course_user_category_id($this->course_user_category->get_id());

            if (!$course_type->create())
            {
                return false;
            }
        }

        return true;
    }

    public function get_course_type_array($course_type_id)
    {
        $glyph = new FontAwesomeGlyph('layer-group', array(), null, 'fas');

        $selected_course_type = array();
        $selected_course_type['classes'] = $glyph->getClassNamesString();

        if ($course_type_id != 0)
        {
            $course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_by_id(
                CourseType::class, $course_type_id
            );

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

    public function get_selected_course_types()
    {
        $values = $this->exportValues();
        $course_type_user_category_id = $this->course_user_category->get_id();
        $selected_course_types_array = array();

        foreach ($values[self::COURSE_TYPE_TARGET_ELEMENTS]['course_type'] as $value)
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
    public function setDefaults($defaults = array())
    {
        $course_user_category = $this->course_user_category;
        $defaults[CourseUserCategory::PROPERTY_TITLE] = $course_user_category->get_title();

        if (!is_null($course_user_category->get_id()))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID
                ), new StaticConditionVariable($course_user_category->get_id())
            );

            $courseTypeUserCategories = DataManager::retrieves(
                CourseTypeUserCategory::class, new DataClassRetrievesParameters($condition)
            );

            $defaultCourseTypes = new AdvancedElementFinderElements();
            $courseTypeGlyph = new FontAwesomeGlyph('layer-group', array(), null, 'fas');

            foreach($courseTypeUserCategories as $type)
            {
                $courseType = DataManager::retrieve_by_id(CourseType::class, $type->get_course_type_id());
                $defaultCourseTypes->add_element(
                    new AdvancedElementFinderElement(
                        'course_type_' . $type->get_course_type_id(), $courseTypeGlyph->getClassNamesString(),
                        $courseType->get_title(), $courseType->get_title()
                    )
                );
            }

            $element = $this->getElement(self::COURSE_TYPE_TARGET_ELEMENTS);
            $element->setDefaultValues($defaultCourseTypes);
        }

        parent::setDefaults($defaults);
    }

    public function update_course_user_category()
    {
        $course_user_category = $this->course_user_category;
        $values = $this->exportValues();

        // EDIT TITLE
        $course_user_category->set_title($values[CourseUserCategory::PROPERTY_TITLE]);

        if (!$course_user_category->update())
        {
            return false;
        }

        // EDIT COURSE TYPE
        // get the existing course types
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategory::class, CourseTypeUserCategory::PROPERTY_COURSE_USER_CATEGORY_ID
            ), new StaticConditionVariable($course_user_category->get_id())
        );

        $existing_types = DataManager::retrieves(
            CourseTypeUserCategory::class, new DataClassRetrievesParameters($condition)
        );

        if ($existing_types)
        {
            $existing_types = $existing_types;
        }
        else
        {
            $existing_types = array();
        }

        // get the selected course types
        $selected_types = $this->get_selected_course_types();

        $existingCourseTypeIds = array();

        foreach ($existing_types as $existing_type)
        {
            $existingCourseTypeIds[] = $existing_type->get_course_type_id();
        }

        $selectedCourseTypeIds = array();

        foreach ($selected_types as $selected_type)
        {
            $selectedCourseTypeIds[] = $selected_type->get_course_type_id();
        }

        $failures = 0;

        foreach ($existing_types as $existing_type)
        {
            if (!in_array($existing_type->get_course_type_id(), $selectedCourseTypeIds))
            {
                if (!$existing_type->delete())
                {
                    $failures ++;
                }
            }
        }

        foreach ($selected_types as $selected_type)
        {
            if (!in_array($selected_type->get_course_type_id(), $existingCourseTypeIds))
            {
                if (!$selected_type->create())
                {
                    $failures ++;
                }
            }
        }

        return ($failures == 0);
    }
}
