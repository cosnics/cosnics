<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Form;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Builds the open course form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseForm extends FormValidator
{
    const ELEMENT_COURSES = 'courses';

    const ELEMENT_ROLES = 'roles';

    const FORM_TYPE_ADD = 1;

    const FORM_TYPE_EDIT = 2;

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
     * Adds the buttons to the form
     */
    protected function addButtons()
    {
        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit',
            $this->translator->getTranslation('Save', null, StringUtilities::LIBRARIES)
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $this->translator->getTranslation('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Adds the course selector
     */
    protected function addCoursesSelector()
    {
        $advancedElementFinderTypes = new AdvancedElementFinderElementTypes();
        $advancedElementFinderTypes->add_element_type(
            new AdvancedElementFinderElementType(
                'courses', $this->translator->getTranslation('Courses', null, Manager::context()),
                'Chamilo\Application\Weblcms\Course\OpenCourse\Ajax', 'GetCoursesForElementFinder'
            )
        );

        $this->addElement(
            'advanced_element_finder', self::ELEMENT_COURSES,
            $this->translator->getTranslation('SelectCourses', null, Manager::context()), $advancedElementFinderTypes
        );
    }

    /**
     * Adds the role selector
     */
    protected function addRolesSelector()
    {
        $advancedElementFinderTypes = new AdvancedElementFinderElementTypes();
        $advancedElementFinderTypes->add_element_type(
            new AdvancedElementFinderElementType(
                'roles', $this->translator->getTranslation('Roles', null, Manager::context()),
                'Chamilo\Core\User\Roles\Ajax', 'GetRolesForElementFinder'
            )
        );

        $this->addElement(
            'advanced_element_finder', self::ELEMENT_ROLES,
            $this->translator->getTranslation('SelectRoles', null, Manager::context()), $advancedElementFinderTypes
        );
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
     * Sets the default roles
     *
     * @param Role[] $defaultRoles
     */
    public function setDefaultRoles($defaultRoles = [])
    {
        $defaultRoleElements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('mask', [], null, 'fas');

        foreach ($defaultRoles as $defaultRole)
        {
            $defaultRoleElements->add_element(
                new AdvancedElementFinderElement(
                    'role_' . $defaultRole->getId(), $glyph->getClassNamesString(), $defaultRole->getRole(),
                    $defaultRole->getRole()
                )
            );
        }

        $element = $this->getElement(self::ELEMENT_ROLES);
        $element->setDefaultValues($defaultRoleElements);
    }
}