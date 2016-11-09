<?php
namespace Chamilo\Application\Weblcms\Request\Form;

use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class RequestForm extends FormValidator
{

    private $request;

    private $course_types;

    private $course_categories;

    function __construct($request, $action)
    {
        parent :: __construct('request', 'post', $action);

        $this->request = $request;

        $this->build();
        $this->setDefaults();
    }

    function build()
    {
        if ($this->request->get_id())
        {
            $user_details = new \Chamilo\Core\User\UserDetails($this->request->get_user());
            $this->addElement('static', null, Translation :: get('User'), $user_details->toHtml());
        }

        $this->addElement(
            'select',
            Request :: PROPERTY_COURSE_TYPE_ID,
            Translation :: get('CourseType'),
            $this->get_course_types());
        $this->addRule(
            Request :: PROPERTY_COURSE_TYPE_ID,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'select',
            Request :: PROPERTY_CATEGORY_ID,
            Translation :: get('CategoryId'),
            $this->get_course_categories());
        $this->addRule(
            Request :: PROPERTY_CATEGORY_ID,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement('text', Request :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(
            Request :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement('text', Request :: PROPERTY_SUBJECT, Translation :: get('Subject'));
        $this->addRule(
            Request :: PROPERTY_SUBJECT,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'textarea',
            Request :: PROPERTY_MOTIVATION,
            Translation :: get('Motivation'),
            array("cols" => 50, "rows" => 6));
        $this->addRule(
            Request :: PROPERTY_MOTIVATION,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        if ($this->request->get_id())
        {
            $this->addElement(
                'textarea',
                Request :: PROPERTY_DECISION_MOTIVATION,
                Translation :: get('DecisionMotivation'),
                array("cols" => 50, "rows" => 6));
            $this->addRule(
                Request :: PROPERTY_DECISION_MOTIVATION,
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
                'required');

            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'arrow-right');
        }
        else
        {
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('Send', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'envelope');
        }

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $defaults[Request :: PROPERTY_COURSE_TYPE_ID] = $this->request->get_course_type_id();
        $defaults[Request :: PROPERTY_NAME] = $this->request->get_name();
        $defaults[Request :: PROPERTY_SUBJECT] = $this->request->get_subject();
        $defaults[Request :: PROPERTY_MOTIVATION] = $this->request->get_motivation();

        if ($this->request->get_id())
        {
            $defaults[Request :: PROPERTY_DECISION_MOTIVATION] = $this->request->get_decision_motivation();
        }

        parent :: setDefaults($defaults);
    }

    /**
     *
     * @return string[int]
     */
    function get_course_types()
    {
        if (! isset($this->course_types))
        {
            $course_type_objects = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_active_course_types();
            $course_management_rights = CourseManagementRights :: getInstance();

            while ($course_type = $course_type_objects->next_result())
            {
                if ($course_management_rights->is_allowed(
                    CourseManagementRights :: REQUEST_COURSE_RIGHT,
                    $course_type->get_id(),
                    CourseManagementRights :: TYPE_COURSE_TYPE))
                {
                    $this->course_types[$course_type->get_id()] = $course_type->get_title();
                }
            }
        }
        return $this->course_types;
    }

    /**
     *
     * @return string[int]
     */
    function get_course_categories()
    {
        if (! isset($this->course_categories))
        {
            $condition = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_STATE),
                    new StaticConditionVariable(CourseCategory :: STATE_ARCHIVE)));

            $course_categories = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_course_categories_ordered_by_name(
                $condition);

            $course_categories_array = array();

            while ($category = $course_categories->next_result())
            {
                $course_categories_array[$category->get_id()] = $category->get_name();
            }

            $this->course_categories = $course_categories_array;
        }

        return $this->course_categories;
    }
}