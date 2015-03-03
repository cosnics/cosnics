<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_user_relation_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.course
 */
class CourseUserRelationForm extends FormValidator
{
    const TYPE_EDIT = 2;

    private $course_user_relation;

    private $user;

    public function __construct($form_type, $course_user_relation, $user, $action)
    {
        parent :: __construct('course_user', 'post', $action);
        
        $this->course_user_relation = $course_user_relation;
        $this->user = $user;
        
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('static', Course :: PROPERTY_ID, Translation :: get('CourseCode'));
        
        $course = CourseDataManager :: retrieve_by_id(
            Course :: class_name(), 
            $this->course_user_relation->get_course_id());
        
        $categories = DataManager :: retrieve_course_user_categories_from_course_type(
            $course->get_course_type_id(), 
            $this->user->get_id());
        
        $cat_options['0'] = Translation :: get('NoCategory');
        while ($category = $categories->next_result())
        {
            $cat_options[$category->get_id()] = $category->get_title();
        }
        
        $this->addElement(
            'select', 
            CourseUserRelation :: PROPERTY_CATEGORY, 
            Translation :: get('Category'), 
            $cat_options);
        
        // $this->addElement('submit', 'course_user_category', Translation ::
        // get('Ok'));
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        
        $this->addElement('hidden', CourseUserRelation :: PROPERTY_COURSE_ID);
    }

    public function update_course_user_relation()
    {
        $course_user_relation = $this->course_user_relation;
        $values = $this->exportValues();
        
        $old_category = $course_user_relation->get_category();
        $counter = $course_user_relation->get_sort();
        
        $course_user_relation->set_category($values[CourseUserRelation :: PROPERTY_CATEGORY]);
        
        $succes = $course_user_relation->update();
        
        $course = CourseDataManager :: retrieve_by_id(Course :: class_name(), $course_user_relation->get_course_id());
        
        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_COURSE_TYPE_ID), 
            new StaticConditionVariable($course->get_course_type_id()));
        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_COURSE_ID), 
            new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID), 
            Course :: get_table_name(), 
            $subcondition);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_USER_ID), 
            new StaticConditionVariable($course_user_relation->get_user()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_CATEGORY), 
            new StaticConditionVariable($old_category));
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_SORT), 
            InEqualityCondition :: GREATER_THAN, 
            new StaticConditionVariable($counter));
        
        $condition = new AndCondition($conditions);
        
        $course_user_relations = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            CourseUserRelation :: class_name(), 
            new DataClassRetrievesParameters(
                $condition, 
                null, 
                null, 
                new OrderBy(
                    new PropertyConditionVariable(
                        CourseUserRelation :: class_name(), 
                        CourseUserRelation :: PROPERTY_SORT))));
        
        while ($relation = $course_user_relations->next_result())
        {
            $relation->set_sort($counter);
            $succes &= $relation->update();
            $counter ++;
        }
        
        return $succes;
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
        $course_user_relation = $this->course_user_relation;
        
        $course = CourseDataManager :: retrieve_by_id(Course :: class_name(), $course_user_relation->get_course_id());
        
        $defaults[Course :: PROPERTY_ID] = $course->get_visual_code();
        
        $defaults[CourseUserRelation :: PROPERTY_COURSE_ID] = $course_user_relation->get_course();
        $defaults[CourseUserRelation :: PROPERTY_CATEGORY] = $course_user_relation->get_category();
        parent :: setDefaults($defaults);
    }
}
