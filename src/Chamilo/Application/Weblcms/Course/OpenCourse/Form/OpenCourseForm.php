<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Form;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Builds the open course form
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseForm extends FormValidator
{
    const FORM_TYPE_ADD = 1;
    const FORM_TYPE_EDIT = 2;
    const ELEMENT_COURSES = 'courses';
    const ELEMENT_ROLES = 'roles';

    /**
     *
     * @var Translation
     */
    protected $translator;

    /**
     *
     * @var int
     */
    protected $formType;

    /**
     * OpenCourseForm constructor.
     * 
     * @param int $formType
     * @param string $action
     * @param Translation $translator
     */
    public function __construct($formType, $action, Translation $translator)
    {
        parent::__construct('open_course_form', 'POST', $action);
        
        $this->formType = $formType;
        $this->translator = $translator;
        $this->buildForm();
    }

    /**
     * Sets the default roles
     * 
     * @param Role[] $defaultRoles
     */
    public function setDefaultRoles($defaultRoles = array())
    {
        $defaultRoleElements = new AdvancedElementFinderElements();
        foreach ($defaultRoles as $defaultRole)
        {
            $defaultRoleElements->add_element(
                new AdvancedElementFinderElement(
                    'role_' . $defaultRole->getId(), 
                    'type type_role', 
                    $defaultRole->getRole(), 
                    $defaultRole->getRole()));
        }
        
        $element = $this->getElement(self::ELEMENT_ROLES);
        $element->setDefaultValues($defaultRoleElements);
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        if ($this->formType == self::FORM_TYPE_ADD)
        {
            $this->addCoursesSelector();
        }
        
        $this->addRolesSelector();
        $this->addButtons();
    }

    /**
     * Adds the course selector
     */
    protected function addCoursesSelector()
    {
        $advancedElementFinderTypes = new AdvancedElementFinderElementTypes();
        $advancedElementFinderTypes->add_element_type(
            new AdvancedElementFinderElementType(
                'courses', 
                $this->translator->getTranslation('Courses', null, Manager::context()), 
                'Chamilo\Application\Weblcms\Course\OpenCourse\Ajax', 
                'GetCoursesForElementFinder'));
        
        $this->addElement(
            'advanced_element_finder', 
            self::ELEMENT_COURSES, 
            $this->translator->getTranslation('SelectCourses', null, Manager::context()), 
            $advancedElementFinderTypes);
    }

    /**
     * Adds the role selector
     */
    protected function addRolesSelector()
    {
        $advancedElementFinderTypes = new AdvancedElementFinderElementTypes();
        $advancedElementFinderTypes->add_element_type(
            new AdvancedElementFinderElementType(
                'roles', 
                $this->translator->getTranslation('Roles', null, Manager::context()), 
                'Chamilo\Core\User\Roles\Ajax', 
                'GetRolesForElementFinder'));
        
        $this->addElement(
            'advanced_element_finder', 
            self::ELEMENT_ROLES, 
            $this->translator->getTranslation('SelectRoles', null, Manager::context()), 
            $advancedElementFinderTypes);
    }

    /**
     * Adds the buttons to the form
     */
    protected function addButtons()
    {
        $buttons = array();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            $this->translator->getTranslation('Save', null, Utilities::COMMON_LIBRARIES));
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            $this->translator->getTranslation('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}