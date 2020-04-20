<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.course
 */
class CourseTypeUserCategoryRelCourseForm extends FormValidator
{

    private $course_type_user_category_rel_course;

    private $user;

    public function __construct($course_type_user_category_rel_course, $user, $action)
    {
        parent::__construct('course_type_user_category_rel_course_form', self::FORM_METHOD_POST, $action);

        $this->course_type_user_category_rel_course = $course_type_user_category_rel_course;
        $this->user = $user;

        $this->build_basic_form();
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('static', Course::PROPERTY_ID, Translation::get('CourseCode'));

        $course = CourseDataManager::retrieve_by_id(
            Course::class_name(),
            $this->course_type_user_category_rel_course->get_course_id());

        $categories = DataManager::retrieve_course_user_categories_from_course_type(
            $course->get_course_type_id(),
            $this->user->get_id());

        $cat_options['0'] = Translation::get('NoCategory');
        while ($category = $categories->next_result())
        {
            $cat_options[$category[CourseTypeUserCategory::PROPERTY_ID]] = $category[CourseUserCategory::PROPERTY_TITLE];
        }

        $this->addElement(
            'select',
            CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID,
            Translation::get('Category', null, Utilities::COMMON_LIBRARIES),
            $cat_options);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_course_type_user_category_rel_course()
    {
        $course_type_user_category_rel_course = $this->course_type_user_category_rel_course;
        $values = $this->exportValues();

        $current_category = $course_type_user_category_rel_course->get_course_type_user_category_id();
        $selected_category = $values[CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID];

        if ($current_category != $selected_category)
        {
            if ($current_category)
            {
                $course_type_user_category_rel_course->delete();
            }

            if ($selected_category)
            {
                $course_type_user_category_rel_course->set_course_type_user_category_id($selected_category);
                return $course_type_user_category_rel_course->create();
            }
        }

        return true;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $course_type_user_category_rel_course = $this->course_type_user_category_rel_course;

        $course = CourseDataManager::retrieve_by_id(
            Course::class_name(),
            $course_type_user_category_rel_course->get_course_id());

        $defaults[Course::PROPERTY_ID] = $course->get_title();
        $defaults[CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID] = $course_type_user_category_rel_course->get_course_type_user_category_id();

        parent::setDefaults($defaults);
    }
}
