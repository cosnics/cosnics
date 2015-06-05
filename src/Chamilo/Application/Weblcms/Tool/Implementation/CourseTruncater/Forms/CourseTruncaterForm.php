<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Forms;

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
 * This form can let the user select the sections and publications of a course for deleting them.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class CourseTruncaterForm extends FormValidator
{

    private $parent;

    /**
     * Constructor
     *
     * @param type $parent
     */
    public function __construct($parent)
    {
        parent :: __construct('course_truncater_form');
        $this->parent = $parent;
    }

    /**
     * Builds the form
     */
    public function buildForm()
    {
        $defaults = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->parent->get_course_id()));

        $publications_set = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublication :: class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            ContentObjectPublication :: class_name(),
                            ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX)))));

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
                $content_object = $publication->get_content_object();
                $id = 'publications[' . $publication->get_id() . ']';
                $this->addElement('checkbox', $id, $label, $content_object->get_title());
                $defaults[$id] = true;
            }
        }

        $this->addFormRule(array('PublicationSelectionMaintenanceWizardPage', 'count_selected_publications'));

        $this->addElement('html', '<h3>' . Translation :: get('CourseSections') . '</h3>');

        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->parent->get_course_id()));

        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(),
            $condition);

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

        $this->addElement('html', '<h3>' . Translation :: get('EmptyThisCourseInformation') . '</h3>');
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
