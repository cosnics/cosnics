<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Forms;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form can let the user select the sections and publications of a course for copying them to another course them.
 */
class CourseCopierForm extends FormValidator
{

    private $parent;

    /**
     * Constructor
     *
     * @param type $parent
     */
    public function __construct($parent)
    {
        parent :: __construct('course_copier_form');
        $this->parent = $parent;
    }

    /**
     * Build the form for copying the course publications,sections and categorys
     */
    public function buildForm()
    {
        $defaults = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->parent->get_course_id()));

        $parameters = new DataClassRetrievesParameters(
            $condition,
            null,
            null,
            array(
                new OrderBy(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX),
                    SORT_ASC)));

        $publications_set = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublication :: class_name(),
            $parameters);

        while ($publication = $publications_set->next_result())
        {
            $publications[$publication->get_tool()][] = $publication;
        }

        $this->addElement('html', '<h3>' . Translation :: get('Publications') . '</h3>');

        foreach ($publications as $tool => $tool_publications)
        {
            foreach ($tool_publications as $index => $publication)
            {
                $label = $index == 0 ? Translation :: get(
                    'TypeName',
                    null,
                    \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($tool)) : '';

                $id = 'publications[' . $publication->get_id() . ']';
                $this->addElement('checkbox', $id, $label, $publication->get_content_object()->get_title());
                $defaults[$id] = true;
            }
        }

        $this->addElement('html', '<h3>' . Translation :: get('CourseSections') . '</h3>');

        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->parent->get_course_id()));
        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(),
            new DataClassRetrievesParameters($condition));

        $common_sections = array(
            CourseSection :: TYPE_TOOL,
            CourseSection :: TYPE_DISABLED,
            CourseSection :: TYPE_LINK,
            CourseSection :: TYPE_ADMIN);

        while ($course_section = $course_sections->next_result())
        {
            if (! in_array($course_section->get_type(), $common_sections))
            {
                $id = 'course_sections[' . $course_section->get_id() . ']';
                $this->addElement('checkbox', $id, $course_section->get_name());
                $defaults[$id] = true;
            }
        }

        $this->addElement('html', '<h3>' . Translation :: get('Other') . '</h3>');
        $this->addElement('checkbox', 'content_object_categories', Translation :: get('PublicationCategories'));
        $defaults['content_object_categories'] = true;

        $this->setDefaults($defaults);

        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_courses_from_user_where_user_is_teacher(
            $this->parent->get_user());

        $this->addElement('html', '<h3>' . Translation :: get('SelectCourse') . '</h3>');
        $current_code = $this->parent->get_course_id();

        $options = array();

        while ($course = $courses->next_result())
        {
            if ($course->get_id() != $current_code)
            {
                $options[$course->get_id()] = $course->get_title() . ' (' . $course->get_visual_code() . ')';
            }
        }

        asort($options);

        $this->addElement(
            'select',
            'course',
            Translation :: get('Course'),
            $options,
            array('multiple' => 'multiple', 'size' => '75', 'style' => 'height: 500px;'));
        $this->addRule('course', Translation :: get('Required', null, Utilities :: COMMON_LIBRARIES), 'required');

        $this->addElement('html', '<h3>' . Translation :: get('CopyThisCourseInformation') . '</h3>');
        $this->addElement('checkbox', 'confirm', Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(
            'confirm',
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $prevnext = array();
        $prevnext[] = $this->createElement(
            'submit',
            $this->parent->get_url(),
            Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->updateAttributes(array('action' => $this->parent->get_url()));
    }
}
