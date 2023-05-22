<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\CourseGroupMenu;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @author  Anthony Hurst (Hogeschool Gent)
 * @package application.lib.weblcms.course_group
 */
class CourseGroupForm extends FormValidator
{
    public const COURSE_GROUP_QUANTITY = 'course_group_quantity';

    public const OPTION_PARENT_GROUP_EXISTING = 1;

    public const OPTION_PARENT_GROUP_NEW = 2;

    public const OPTION_PARENT_GROUP_NONE = 0;

    public const PARENT_GROUP_EXISTING = 'parent_group_existing';

    public const PARENT_GROUP_NEW = 'parent_group_new';

    public const PARENT_GROUP_NONE = 'parent_group_none';

    public const PARENT_GROUP_SELECTION = 'parent_group_selection';

    public const RESULT_ERROR = 'ObjectUpdateFailed';

    public const RESULT_SUCCESS = 'ObjectUpdated';

    public const TYPE_ADD_COURSE_GROUP_TITLES = 3;

    public const TYPE_CREATE = 1;

    public const TYPE_EDIT = 2;

    // private $parent;

    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    /**
     * @var User
     */
    protected $currentUser;

    private $course_group;

    private $form_type;

    private $rights;

    /**
     * CourseGroupForm constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager $courseGroupDecoratorsManager
     * @param string $form_type
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $course_group
     * @param string $action
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     */
    public function __construct(
        CourseGroupDecoratorsManager $courseGroupDecoratorsManager, $form_type, CourseGroup $course_group, $action,
        User $currentUser
    )
    {
        parent::__construct('course_groups', self::FORM_METHOD_POST, $action);
        $this->form_type = $form_type;
        $this->course_group = $course_group;
        $this->currentUser = $currentUser;
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;

        if ($this->form_type == self::TYPE_EDIT)
        {
            $counter = 0;
            $this->build_editing_form();
            $this->setDefaultValues([], $counter);
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->add_top_fields();
            $this->build_creation_form();
            $this->setDefaultValues();
        }
        elseif (($this->form_type == self::TYPE_ADD_COURSE_GROUP_TITLES))
        {
            $this->add_top_fields();
            $this->setDefaultValues();
        }
    }

    public function add_name_field($number = null)
    {
        $element = $this->createElement(
            'text', CourseGroup::PROPERTY_NAME . $number,
            Translation::getInstance()->getTranslation('Title', null, StringUtilities::LIBRARIES), ['size' => '50']
        );

        return $element;
    }

    public function add_titles()
    {
        // $number_of_options = intval($_SESSION['mc_number_of_options']);
        //
        // $qty_groups = $number_of_options -
        // count($_SESSION['mc_skip_options']);
        //
        // $numbering = 0;
        // add the titles automatically
        $values = $this->exportValues();
        $qty_titles = (int) $values[CourseGroupForm::COURSE_GROUP_QUANTITY];

        $_SESSION['mc_number_of_options'] = $qty_titles;
        $this->build_creation_form();
    }

    /**
     * @param $course_group CourseGroup
     */
    public function add_tools($course_group)
    {
        $this->addElement('category', Translation::getInstance()->getTranslation('Integrations'));
        $this->courseGroupDecoratorsManager->decorateCourseGroupForm($this, $course_group);
    }

    public function add_top_fields()
    {
        // $this->build_header(Translation::get("NewCourseGroup"));
        // $group = [];
        // $this->addRule(CourseGroupForm::COURSE_GROUP_QUANTITY, Translation
        // ::get('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
        // 'regex', '/^[0-9]*$/');
    }

    public function build_basic_editing_form()
    {
        $this->build_header($this->course_group->get_name());
        $this->addElement(
            'text', CourseGroup::PROPERTY_NAME,
            Translation::getInstance()->getTranslation('Title', null, StringUtilities::LIBRARIES), ['size' => '50']
        );

        $this->addElement(
            'select', CourseGroup::PROPERTY_PARENT_ID, Translation::getInstance()->getTranslation('GroupParent'),
            $this->get_groups()
        );
        $this->addRule(
            CourseGroup::PROPERTY_PARENT_ID,
            Translation::getInstance()->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->addElement(
            'text', CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS,
            Translation::getInstance()->getTranslation('MaxNumberOfMembers'), 'size="4"'
        );
        $this->addRule(
            CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS,
            Translation::getInstance()->getTranslation('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
            'regex', '/^[0-9]*$/'
        );

        $this->addElement(
            'textarea', CourseGroup::PROPERTY_DESCRIPTION, Translation::getInstance()->getTranslation('Description'),
            'cols="50"'
        );

        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_REG, Translation::getInstance()->getTranslation('Registration'),
            Translation::getInstance()->getTranslation('SelfRegAllowed')
        );
        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_UNREG, null,
            Translation::getInstance()->getTranslation('SelfUnRegAllowed')
        );

        $this->add_tools($this->course_group);
        $this->close_header();
    }

    /**
     * Builds a static part of the form which does not expand while creating several course groups refers to the
     * section: Properties
     *
     * @param string $counter
     */
    public function build_basic_form($counter = '')
    {
        $this->addElement(
            'select', CourseGroup::PROPERTY_PARENT_ID . $counter,
            Translation::getInstance()->getTranslation('GroupParent'), $this->get_groups()
        );
        $this->addRule(
            CourseGroup::PROPERTY_PARENT_ID . $counter,
            Translation::getInstance()->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        $this->build_header(Translation::getInstance()->getTranslation('SingleCourseGroupOptions'));

        $this->addElement(
            'text', CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter,
            Translation::getInstance()->getTranslation('MaximumGroupSubscriptionsPerMember'), 'size="4"'
        );

        $this->addRule(
            CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter,
            Translation::getInstance()->getTranslation('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
            'regex', '/^[0-9]*$/'
        );

        $this->addElement(
            'text', CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation::getInstance()->getTranslation('MaxNumberOfMembers'), 'size="4"'
        );
        $this->addRule(
            CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation::getInstance()->getTranslation('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
            'regex', '/^[0-9]*$/'
        );

        $this->addElement(
            'textarea', CourseGroup::PROPERTY_DESCRIPTION . $counter,
            Translation::getInstance()->getTranslation('Description'), 'cols="50"'
        );

        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_REG . $counter,
            Translation::getInstance()->getTranslation('Registration'),
            Translation::getInstance()->getTranslation('SelfRegAllowed')
        );
        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_UNREG . $counter, null,
            Translation::getInstance()->getTranslation('SelfUnRegAllowed')
        );

        $this->add_tools($this->course_group);

        $this->close_header();
    }

    public function build_children_tab_form_elements()
    {
        $this->build_extended_editing_form();
    }

    /**
     * Course_group creation form
     */
    public function build_creation_form()
    {
        if (!$this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }

        if (!isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 1;
        }

        if (!isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = [];
        }

        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        $qty_groups = $number_of_options - count($_SESSION['mc_skip_options']);

        $numbering = 0;

        $defaults = [];

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup'
            ) . 'CourseGroupForm.js'
        )
        );

        $this->addElement(
            'category', Translation::getInstance()->getTranslation('General', null, StringUtilities::LIBRARIES)
        );

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (!in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = [];
                $group[] = $this->add_name_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement(
                        'style_button', 'remove[' . $option_number . ']', null, [], null,
                        new FontAwesomeGlyph('times', [], null, 'fas')
                    );
                }
                // numbering of the titels
                if ($numbering < $qty_groups)
                {
                    $numbering ++;
                }

                $this->addGroup(
                    $group, CourseGroup::PROPERTY_NAME . $option_number,
                    Translation::getInstance()->getTranslation('CountedTitle', ['COUNT' => $numbering]), '', false
                );
                // fill the title field automatically
                // $defaults[CourseGroup::PROPERTY_NAME . $option_number] =
                // 'Group ' . $numbering;
                parent::setDefaults($defaults);
                $this->addRule(
                    CourseGroup::PROPERTY_NAME . $option_number, Translation::getInstance()->getTranslation(
                    'ThisFieldIsRequired', null, StringUtilities::LIBRARIES
                ), 'required'
                );
            }
        }

        $this->addElement(
            'style_button', 'add[]', null, [], null, new FontAwesomeGlyph(
                'plus', ['title' => Translation::getInstance()->getTranslation('AddGroupExplained')], null, 'fas'
            )
        );

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup'
            ) . 'CourseGroupForm.js'
        )
        );

        $this->build_header(Translation::getInstance()->getTranslation('CourseGroupParent'));
        $this->build_parent_form_create();
        $this->close_header();

        $this->build_header(Translation::getInstance()->getTranslation('CourseGroupOptions'));
        $this->build_options_form_create();
        $this->close_header();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit',
            Translation::getInstance()->getTranslation('Create', null, StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset',
            Translation::getInstance()->getTranslation('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_editing_form()
    {
        if ($this->course_group->count_children() > 0)
        {
            $tabs = new TabsCollection();

            $tabs->add(
                new FormTab('parent', $this->course_group->get_name(), null, 'build_parent_tab_form_elements')
            );

            $tabs->add(
                new FormTab(
                    'children', Translation::getInstance()->getTranslation('CourseGroupChildren'), null,
                    'build_children_tab_form_elements'
                )
            );

            $formTabsGenerator = $this->getFormTabsGenerator();
            $formTabsGenerator->generate('course_groups', $this, $tabs);
        }
        else
        {
            $this->build_parent_tab_form_elements();
        }

        $this->addElement('hidden', CourseGroup::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit',
            Translation::getInstance()->getTranslation('Update', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset',
            Translation::getInstance()->getTranslation('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Extends the basic editing form when the the course group is chosen to be edited which has been created in the
     * group
     */
    public function build_extended_editing_form()
    {
        $counter = 1; // Index 1 means the first child, index 0 is the 'parent' course group.
        $data_set = $this->course_group->get_children();

        foreach ($data_set as $next)
        {
            $course_groups = $next;
            $this->build_header($course_groups->get_name());

            $this->addElement(
                'text', CourseGroup::PROPERTY_NAME . $counter,
                Translation::getInstance()->getTranslation('Title', null, StringUtilities::LIBRARIES), ['size' => '50']
            );

            $this->addElement(
                'select', CourseGroup::PROPERTY_PARENT_ID . $counter,
                Translation::getInstance()->getTranslation('GroupParent'), $this->get_groups()
            );
            $this->addRule(
                CourseGroup::PROPERTY_PARENT_ID . $counter,
                Translation::getInstance()->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
                'required'
            );

            $this->addElement(
                'text', CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
                Translation::getInstance()->getTranslation('MaxNumberOfMembers'), 'size="4"'
            );
            $this->addRule(
                CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter, Translation::getInstance()->getTranslation(
                'ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES
            ), 'regex', '/^[0-9]*$/'
            );

            $this->addElement(
                'textarea', CourseGroup::PROPERTY_DESCRIPTION . $counter,
                Translation::getInstance()->getTranslation('Description'), 'cols="100"'
            );
            $this->addElement(
                'checkbox', CourseGroup::PROPERTY_SELF_REG . $counter,
                Translation::getInstance()->getTranslation('Registration'),
                Translation::getInstance()->getTranslation('SelfRegAllowed')
            );
            $this->addElement(
                'checkbox', CourseGroup::PROPERTY_SELF_UNREG . $counter, null,
                Translation::getInstance()->getTranslation('SelfUnRegAllowed')
            );

            $this->add_tools($course_groups);

            $this->addElement('hidden', CourseGroup::PROPERTY_ID . $counter);
            $this->addElement('hidden', CourseGroup::PROPERTY_PARENT_ID . $counter . 'old');
            $this->addElement('hidden', CourseGroup::PROPERTY_COURSE_CODE . $counter);

            $this->setDefaults_extended($counter, $course_groups);
            $this->close_header();
            $counter ++;
        }
        $this->close_header();
    }

    /**
     * Builds a header of the form
     *
     * @param $header_title string The title to be shown in the header of the form
     */
    public function build_header($header_title)
    {
        $this->addElement('category', $header_title);
    }

    public function build_options_form_create($counter = '')
    {
        $this->addElement('html', '<div id="parent_group_random">');
        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_RANDOM_REG,
            Translation::getInstance()->getTranslation('RandomRegistration'),
            Translation::getInstance()->getTranslation('RegisterCourseUsersRandomly')
        );
        $this->addElement('html', '</div>');

        $this->addElement(
            'text', CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation::getInstance()->getTranslation('MaxNumberOfMembers'), 'size="4"'
        );
        $this->addRule(
            CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation::getInstance()->getTranslation('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
            'regex', '/^[0-9]*$/'
        );

        $this->addElement(
            'textarea', CourseGroup::PROPERTY_DESCRIPTION . $counter,
            Translation::getInstance()->getTranslation('Description'), 'cols="50"'
        );

        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_REG . $counter,
            Translation::getInstance()->getTranslation('Registration'),
            Translation::getInstance()->getTranslation('SelfRegAllowed')
        );
        $this->addElement(
            'checkbox', CourseGroup::PROPERTY_SELF_UNREG . $counter, null,
            Translation::getInstance()->getTranslation('SelfUnRegAllowed')
        );

        $this->add_tools($this->course_group);

        $this->close_header();
    }

    public function build_parent_form_create($counter = '')
    {
        $choices = [];
        $choices[] = $this->createElement(
            'radio', self::PARENT_GROUP_SELECTION, '', Translation::getInstance()->getTranslation('NoParentGroup'),
            self::OPTION_PARENT_GROUP_NONE, ['id' => self::PARENT_GROUP_NONE]
        );
        $choices[] = $this->createElement(
            'radio', self::PARENT_GROUP_SELECTION, '',
            Translation::getInstance()->getTranslation('ExistingParentGroup'), self::OPTION_PARENT_GROUP_EXISTING,
            ['id' => self::PARENT_GROUP_EXISTING]
        );

        $choices[] = $this->createElement(
            'radio', self::PARENT_GROUP_SELECTION, '', Translation::getInstance()->getTranslation('NewParentGroup'),
            self::OPTION_PARENT_GROUP_NEW, ['id' => self::PARENT_GROUP_NEW]
        );
        $this->addGroup($choices, null, Translation::getInstance()->getTranslation('ParentGroupType'), '', false);

        $this->addElement('html', '<div id="parent_group_list">');
        $this->addElement(
            'select', CourseGroup::PROPERTY_PARENT_ID . $counter,
            Translation::getInstance()->getTranslation('GroupParent'),
            // 'select', CourseGroup::PROPERTY_PARENT_ID . $counter, null,
            $this->get_groups()
        );
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="parent_group_name">');
        $this->addElement(
            'text', 'parent_' . CourseGroup::PROPERTY_NAME,
            Translation::getInstance()->getTranslation('ParentGroupTitle'),
            ['id' => 'parent_' . CourseGroup::PROPERTY_NAME, 'size' => '50']
        );
        $this->addRule(
            'parent_' . CourseGroup::PROPERTY_NAME,
            Translation::getInstance()->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="parent_group_max_registrations">');
        $this->addElement(
            'text', 'parent_' . CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation::getInstance()->getTranslation('MaximumGroupSubscriptionsPerMember'),
            ['id' => 'parent_' . CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, 'size' => '4']
        );
        $this->addRule(
            'parent_' . CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation::getInstance()->getTranslation('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES),
            'regex', '/^[0-9]*$/'
        );
        $this->addRule(
            'parent_' . CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation::getInstance()->getTranslation('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );
        $this->addElement('html', '</div>');
    }

    public function build_parent_tab_form_elements()
    {
        $counter = 0; // Index 0 is the 'parent' course group.
        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath(
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup', true
            ) . 'CourseGroupEditForm.js'
        )
        );

        $this->addElement(
            'category', Translation::getInstance()->getTranslation('General', null, StringUtilities::LIBRARIES)
        );

        $this->addElement(
            'text', CourseGroup::PROPERTY_NAME . $counter,
            Translation::getInstance()->getTranslation('Title', null, StringUtilities::LIBRARIES), ['size' => '50']
        );

        $this->build_basic_form($counter);
        // $this->close_header();
    }

    /**
     * @param CourseGroup $parent_course_group
     * @param int $child_max_size
     *
     * @return bool
     */
    private function check_parent_max_members($parent_course_group, $child_max_size)
    {
        if ($parent_course_group->get_max_number_of_members() == 0)
        {
            return true;
        }

        // existing groups size
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_course_group->getId())
        );
        $course_groups = DataManager::retrieves(CourseGroup::class, new DataClassRetrievesParameters($condition));

        $size_children = 0;
        foreach ($course_groups as $existing_course_group)
        {
            $size_children += $existing_course_group->get_max_number_of_members();
        }

        if ($size_children + $child_max_size > $parent_course_group->get_max_number_of_members())
        {
            $this->course_group->addError(
                Translation::getInstance()->getTranslation('MaxMembersTooBigForParentCourseGroup')
            );

            return false;
        }

        return true;
    }

    /**
     * Closes the divs of the healer of the form
     */
    public function close_header()
    {
        // $this->addElement('html', '<div style="clear: both;"></div>');
        // $this->addElement('html', '</div>');
    }

    /**
     * Creates a new CourseGroup.
     *
     * @param string $new_title The title of the new CourseGroup.
     * @param string $course_code
     * @param mixed[] $values   The form values.
     *
     * @return CourseGroup The constructed, but not created CourseGroup.
     */
    private function construct_course_group($new_title, $course_code, $values)
    {
        $course_group = new CourseGroup();
        $course_group->set_name($values[$new_title]);
        $course_group->set_description($values[CourseGroup::PROPERTY_DESCRIPTION]);
        $course_group->set_max_number_of_members($values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS]);
        $course_group->set_self_registration_allowed($values[CourseGroup::PROPERTY_SELF_REG]);
        $course_group->set_self_unregistration_allowed($values[CourseGroup::PROPERTY_SELF_UNREG]);
        $course_group->set_parent_id($values[CourseGroup::PROPERTY_PARENT_ID]);
        if ($values[CourseGroup::PROPERTY_RANDOM_REG])
        {
            $course_group->set_random_registration_done($values[CourseGroup::PROPERTY_RANDOM_REG]);
        }
        if ($values[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER])
        {
            $course_group->set_max_number_of_course_group_per_member(
                $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER]
            );
        }
        $course_group->set_course_code($course_code);

        return $course_group;
    }

    /**
     * Creates a set of new course groups based on the titles received.
     *
     * @param string[] $new_titles The titles for which new course groups are to be created.
     * @param string $course_code  The course code.
     * @param mixed[] $values      The form values.
     *
     * @return CourseGroup[] The constructed, but not created CourseGroups.
     */
    private function construct_course_groups($new_titles, $course_code, $values)
    {
        $course_groups = [];
        foreach ($new_titles as $new_title)
        {
            $course_groups[] = $this->construct_course_group($new_title, $course_code, $values);
        }

        return $course_groups;
    }

    /**
     * A course_group title should be unique per course
     *
     * @param $course_group CourseGroup
     *
     * @return bool
     */
    public function course_group_name_exists($course_group)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME),
            new StaticConditionVariable($course_group->get_name())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_group->get_course_code())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
            new StaticConditionVariable($course_group->get_parent_id())
        );

        // If updating, the name will already exist in the database if the name has not been changed -
        // Exclude course group being updated.
        // If creating, course group will not yet have an id.
        if ($course_group->getId())
        {
            $not_condition = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_ID),
                new StaticConditionVariable($course_group->getId())
            );
            $conditions[] = new NotCondition($not_condition);
        }
        $condition = new AndCondition($conditions);

        $data_set = DataManager::retrieves(CourseGroup::class, new DataClassRetrievesParameters($condition));

        return ($data_set->count() > 0);
    }

    /**
     * This methos creates one or several course_groups for the given course
     *
     * @return bool
     */
    public function create_course_group()
    {
        $this->rights = [];
        $this->rights[] = WeblcmsRights::VIEW_RIGHT;
        $this->rights[] = WeblcmsRights::ADD_RIGHT;
        $this->rights[] = WeblcmsRights::MANAGE_CATEGORIES_RIGHT;

        $course_group = $this->course_group;
        $course_code = $course_group->get_course_code();
        $values = $this->exportValues();
        $new_titles = preg_grep('/^name*[0-9]*$/', array_keys($values));
        $qty = sizeof($new_titles);
        $groups = [];

        // check parent's max size >= combined total size of parent's children (could be more than just this group)
        // new children size

        $new_max_size = $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS] * $qty;

        $parent_group_select = $values[self::PARENT_GROUP_SELECTION];
        $parent_group_id = null;
        $parent_group = null;

        switch ($parent_group_select)
        {
            case self::OPTION_PARENT_GROUP_NONE :
                $parent_course_group = DataManager::retrieve_course_group_root($course_code);
                if (!$this->check_parent_max_members($parent_course_group, $new_max_size))
                {
                    return false;
                }

                $parent_group_id = $parent_course_group->getId();
                $values[CourseGroup::PROPERTY_PARENT_ID] = $parent_group_id;
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
            case self::OPTION_PARENT_GROUP_EXISTING :
                $parent_group_id = $values[CourseGroup::PROPERTY_PARENT_ID];

                /** @var CourseGroup $parent_course_group */
                $parent_course_group = DataManager::retrieve_by_id(CourseGroup::class, $parent_group_id);
                if (!$this->check_parent_max_members($parent_course_group, $new_max_size))
                {
                    return false;
                }
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
            case self::OPTION_PARENT_GROUP_NEW :

                $parent_values = [];
                $parent_values[CourseGroup::PROPERTY_NAME] = $values['parent_' . CourseGroup::PROPERTY_NAME];
                $parent_values[CourseGroup::PROPERTY_DESCRIPTION] = $values['parent_' . CourseGroup::PROPERTY_NAME];
                $parent_values[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER] =
                    $values['parent_' . CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER];
                $parent_values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS] = 0;
                $parent_values[CourseGroup::PROPERTY_SELF_REG] = false;
                $parent_values[CourseGroup::PROPERTY_SELF_UNREG] = false;
                $parent_values[CourseGroup::PROPERTY_PARENT_ID] =
                    DataManager::retrieve_course_group_root($course_code)->getId();

                $parent_group = $this->construct_course_group(CourseGroup::PROPERTY_NAME, $course_code, $parent_values);

                if ($this->course_group_name_exists($parent_group))
                {
                    $this->course_group->addError(
                        Translation::getInstance()->getTranslation(
                            'CourseGroupTitleExists', ['NAME' => $parent_group->get_name()]
                        )
                    );
                }

                if (!$parent_group->create())
                {
                    $course_group->addError(
                        Translation::getInstance()->getTranslation(
                            'CannotCreateCourseGroup', ['NAME' => $parent_group->get_name()]
                        )
                    );
                }

                $parent_group_id = $parent_group->getId();
                $values[CourseGroup::PROPERTY_PARENT_ID] = $parent_group_id;
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
        }

        if ($parent_group_id)
        {
            $parent_group = DataManager::retrieve_by_id(CourseGroup::class, $parent_group_id);
        }
        else
        {
            $parent_group = DataManager::retrieve_course_group_root($course_code);
        }
        if (sizeof($groups) > 0)
        {
            foreach ($groups as $course_group)
            {
                if (!$this->course_group_name_exists($course_group))
                {
                    if ($course_group->create())
                    {
                        $this->courseGroupDecoratorsManager->createGroup($course_group, $this->currentUser, $values);
                    }
                    else
                    {
                        $course_group->addError(
                            Translation::getInstance()->getTranslation(
                                'CreationFailed', ['NAME' => $course_group->get_name()]
                            )
                        );
                    }
                }
                else
                {
                    $this->course_group->addError(
                        Translation::getInstance()->getTranslation(
                            'CourseGroupTitleExists', ['NAME' => $course_group->get_name()]
                        )
                    );
                }
            }
        }

        if (isset($values, $values[CourseGroup::PROPERTY_RANDOM_REG]))
        {
            switch ($parent_group_select)
            {
                case self::OPTION_PARENT_GROUP_NONE :
                    break;
                case self::OPTION_PARENT_GROUP_EXISTING :
                    $this->random_user_subscription_to_course_groups($parent_group);
                    break;
                case self::OPTION_PARENT_GROUP_NEW :
                    $this->random_user_subscription_to_course_groups($parent_group);
                    break;
            }
        }

        return !$this->course_group->hasErrors();
    }

    public function getErrors()
    {
        return $this->course_group->getErrors();
    }

    public function get_groups()
    {
        $course = new Course();
        $course->setId($this->course_group->get_course_code());
        $menu = new CourseGroupMenu($course, 0);
        $renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * randomly selects the users from the course users and subscribes to course group takes into the account the max
     * number of members
     *
     * @param CourseGroup $course_group
     */
    public function random_user_subscription_to_course_group($course_group)
    {
        $course_code = $course_group->get_course_code();
        $max_num_members = $course_group->get_max_number_of_members();

        // randomize course_users
        /** @var \Doctrine\Common\Collections\ArrayCollection $course_users_data_set */
        $course_users_data_set = CourseDataManager::retrieve_all_course_users($course_code);
        $course_users = [];
        foreach ($course_users_data_set as $course_user)
        {
            $course_users[] = $course_user;
        }
        shuffle($course_users);

        $members_to_add = [];
        $qty_course_users = sizeof($course_users);

        if ($max_num_members < $qty_course_users && $max_num_members > 0)
        {
            // if course_users > max number of subscriptions allowed =>
            // subscribe all the users to the course_group
            $qty_members_to_add = $max_num_members;
        }
        else
        {
            $qty_members_to_add = $qty_course_users;
        }

        $count = 0;
        while (sizeof($members_to_add) < $qty_members_to_add && $count < count($course_users))
        {
            $member = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username(
                $course_users[$count]->get_username()
            );
            $members_to_add[] = $member->getId();

            $count ++;
        }
        $course_group->subscribe_users($members_to_add);
    }

    /**
     * Randomly selects the users from the course users and subscribes them in the course groups directly under the
     * given parent course group. It takes into the account the max number of members and the maximum number of
     * subscriptions allowed for a course user. This method front-loads (it will fill up spaces as soon as it gets them.
     * If there are no more users with unused subscriptions, all remaining groups will remain empty).
     *
     * @param CourseGroup $parent_course_group The parent of the course groups to which the random subscription applies.
     *
     * @return bool True if all the available spaces are filled. False otherwise.
     */
    public function random_user_subscription_to_course_groups($parent_course_group)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $course_users_drs */
        $course_users_drs = CourseDataManager::retrieve_all_course_users(
            $parent_course_group->get_course_code()
        );
        $course_users = [];
        if ($course_users_drs)
        {
            foreach ($course_users_drs as $course_user)
            {
                $course_users[$course_user[User::PROPERTY_ID]] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    User::class, $course_user[User::PROPERTY_ID]
                );
            }
        }

        /** @var CourseGroup[] $course_groups */
        $course_groups = $parent_course_group->get_children();

        $max_number_subscriptions = $parent_course_group->get_max_number_of_course_group_per_member();
        $user_number_subscriptions = [];

        foreach ($course_groups as $course_group)
        {
            /** @var int[] $subscribed_users */
            $subscribed_users = $course_group->get_members(true, true);
            if ($subscribed_users)
            {
                foreach ($subscribed_users as $user_id)
                {
                    $user_number_subscriptions[$user_id] = $user_number_subscriptions[$user_id] + 1;
                }
            }
        }

        foreach ($user_number_subscriptions as $user_id => $number_subscriptions)
        {
            if ($number_subscriptions >= $max_number_subscriptions)
            {
                unset($course_users[$user_id]);
            }
        }

        shuffle($course_users);
        $all_groups_filled = true;
        foreach ($course_groups as $course_group)
        {
            if (!$all_groups_filled)
            {
                break;
            }
            /** @var int[] $subscribed_users_drs */
            $subscribed_users_drs = $course_group->get_members(true, true);
            $subscribed_users = [];
            if ($subscribed_users_drs)
            {
                foreach ($subscribed_users_drs as $user_id)
                {
                    $subscribed_users[$user_id] = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        User::class, $user_id
                    );
                }
            }
            $max_subscribed_users = $course_group->get_max_number_of_members();
            $new_users = [];
            while (count($new_users) < $max_subscribed_users - count($subscribed_users) && count($course_users) > 0)
            {
                $random_int = mt_rand(0, count($course_users) - 1);
                if (!array_key_exists($course_users[$random_int]->getId(), $new_users) &&
                    !array_key_exists($course_users[$random_int]->getId(), $subscribed_users))
                {
                    $new_users[$course_users[$random_int]->getId()] = $course_users[$random_int];
                    $user_number_subscriptions[$course_users[$random_int]->getId()] =
                        $user_number_subscriptions[$course_users[$random_int]->getId()] + 1;

                    if ($user_number_subscriptions[$course_users[$random_int]->getId()] >= $max_number_subscriptions)
                    {
                        unset($course_users[$random_int]);
                        $course_users = array_values($course_users);
                    }
                }
            }
            if (count($new_users) > 0)
            {
                $course_group->subscribe_users($new_users);
            }
        }

        return $all_groups_filled;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param $defaults array Default values for this form's parameters.
     * @param string $counter
     */
    public function setDefaultValues($defaults = [], $counter = '')
    {
        $course_group = $this->course_group;
        $defaults[CourseGroup::PROPERTY_NAME . $counter] = $course_group->get_name();
        $defaults[CourseGroup::PROPERTY_DESCRIPTION . $counter] = $course_group->get_description();

        $defaults[self::PARENT_GROUP_SELECTION . $counter] =
            $course_group->get_parent_id() == 0 ? self::OPTION_PARENT_GROUP_NONE : self::OPTION_PARENT_GROUP_EXISTING;

        if (is_null($course_group->get_max_number_of_members()))
        {
            $defaults[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] = 20;
        }
        else
        {
            $defaults[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] =
                $course_group->get_max_number_of_members();
        }

        if (is_null($course_group->get_max_number_of_course_group_per_member()))
        {
            $defaults[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter] = 20;
        }
        else
        {
            $defaults[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter] =
                $course_group->get_max_number_of_course_group_per_member();
        }

        $defaults[CourseGroup::PROPERTY_SELF_REG . $counter] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup::PROPERTY_SELF_UNREG . $counter] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup::PROPERTY_RANDOM_REG . $counter] = $course_group->is_random_registration_done();
        $defaults[CourseGroup::PROPERTY_PARENT_ID . $counter] = $course_group->get_parent_id();
        parent::setDefaults($defaults);
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param int $counter
     * @param CourseGroup $course_group
     */
    public function setDefaults_extended($counter, $course_group)
    {
        $defaults[CourseGroup::PROPERTY_NAME . $counter] = $course_group->get_name();
        $defaults[CourseGroup::PROPERTY_ID . $counter] = $course_group->getId();
        $defaults[CourseGroup::PROPERTY_COURSE_CODE . $counter] = $course_group->get_course_code();
        $defaults[CourseGroup::PROPERTY_DESCRIPTION . $counter] = $course_group->get_description();
        $defaults[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] = $course_group->get_max_number_of_members();
        $defaults[CourseGroup::PROPERTY_SELF_REG . $counter] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup::PROPERTY_SELF_UNREG . $counter] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup::PROPERTY_PARENT_ID . $counter] = $course_group->get_parent_id();
        $defaults[CourseGroup::PROPERTY_PARENT_ID . $counter . 'old'] = $course_group->get_parent_id();
        parent::setDefaults($defaults);
    }

    /**
     * Updates the course group
     *
     * @return bool $result True when successful
     */
    public function update_course_group()
    {
        $values = $this->exportValues();

        $counter = 1;

        $data_set = $this->course_group->get_children();

        if ($this->course_group->getErrors() == null)
        {
            // group size check -> total size must not be greater than parent group's max size
            $course_groups = [];
            $parent_cgid = null;
            $total_size_diff = 0;

            if ($data_set->count() != 0)
            {
                foreach ($data_set as $course_group)
                {
                    $course_groups[] = $course_group;
                    $parent_cgid = $course_group->get_parent_id();
                    $total_size_diff += $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter];
                    $total_size_diff -= $course_group->get_max_number_of_members();
                    $counter ++;
                }

                $parent_course_group = DataManager::retrieve_by_id(CourseGroup::class, $parent_cgid);
                // existing groups size
                $total_size = $total_size_diff;
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_PARENT_ID),
                    new StaticConditionVariable($parent_course_group->getId())
                );

                $c_course_groups = DataManager::retrieves(
                    CourseGroup::class, new DataClassRetrievesParameters($condition)
                );

                foreach ($c_course_groups as $course_group)
                {
                    $total_size += $course_group->get_max_number_of_members();
                }

                $parent_group_form_max_number_of_members = $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . '0'];

                if ($parent_group_form_max_number_of_members > 0 &&
                    $total_size > $parent_group_form_max_number_of_members)
                {
                    $this->course_group->addError(
                        Translation::getInstance()->getTranslation('MaxMembersFromChildrenTooBigForParentCourseGroup')
                    );

                    return false;
                }
            }
            else
            {
                /** @var CourseGroup $parent_course_group */
                $parent_course_group = DataManager::retrieve_by_id(
                    CourseGroup::class, $this->course_group->get_parent_id()
                );
                if ($parent_course_group->get_max_number_of_members() > 0)
                {
                    $parent_course_group_children = $parent_course_group->get_children();
                    $total_size = 0;

                    foreach ($parent_course_group_children as $child_group)
                    {
                        if ($child_group->getId() == $this->course_group->getId())
                        {
                            $total_size += $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . 0];
                        }
                        else
                        {
                            $total_size += $child_group->get_max_number_of_members();
                        }
                    }

                    if ($parent_course_group->get_max_number_of_members() < $total_size)
                    {
                        $this->course_group->addError(
                            Translation::getInstance()->getTranslation('MaxMembersTooBigForParentCourseGroup')
                        );

                        return false;
                    }
                }
            }
            $counter = 0;
            array_unshift($course_groups, $this->course_group); // Add the parent group to array at index 0, to match
            // the array indices with the form element counters.
            foreach ($course_groups as $course_group)
            {

                // Re-retrieve the course group ... . The update statement for NestedSet dataclasses includes the left
                // and right values. If a
                // move has been performed on a course group, other course groups' left and right values will have been
                // changed in the database,
                // but not yet in the $course_groups array. If we then update those course groups, their left and right
                // values will be overridden
                // with their previous values.

                /** @var CourseGroup $course_group */
                $course_group = DataManager::retrieve_by_id(CourseGroup::class, $course_group->getId());

                $course_group->set_description($values[CourseGroup::PROPERTY_DESCRIPTION . $counter]);
                $course_group->set_max_number_of_members(
                    $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter]
                );
                $course_group->set_self_registration_allowed($values[CourseGroup::PROPERTY_SELF_REG . $counter]);
                $course_group->set_self_unregistration_allowed($values[CourseGroup::PROPERTY_SELF_UNREG . $counter]);
                $course_group->set_max_number_of_course_group_per_member(
                    $values[CourseGroup::PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter]
                );

                if ($course_group->get_name() != $values[CourseGroup::PROPERTY_NAME . $counter])
                {
                    $old_name = $course_group->get_name();
                    $course_group->set_name($values[CourseGroup::PROPERTY_NAME . $counter]);
                    if ($this->course_group_name_exists($course_group))
                    {
                        $counter ++;
                        $this->course_group->addError(
                            Translation::getInstance()->getTranslation(
                                'CourseGroupNotUpdated',
                                ['NAME_OLD' => $old_name, 'NAME_NEW' => $course_group->get_name()]
                            )
                        );
                        continue;
                    }
                }

                $course_group->update();

                $this->courseGroupDecoratorsManager->updateGroup($course_group, $this->currentUser, $values);

                // Change the parent
                if ($course_group->get_parent_id() != $values[CourseGroup::PROPERTY_PARENT_ID . $counter])
                {
                    if (!$course_group->move($values[CourseGroup::PROPERTY_PARENT_ID . $counter]))
                    {
                        return false;
                    }
                }
                $counter ++;
            }

            return count($this->course_group->getErrors()) == 0;
        }
        else
        {
            return false;
        }
    }

    /**
     * Validates the filled in data of the form when the new course group is added
     */
    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }

        return parent::validate();
    }
}
