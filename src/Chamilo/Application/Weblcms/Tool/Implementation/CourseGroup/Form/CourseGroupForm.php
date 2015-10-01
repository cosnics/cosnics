<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\CourseGroupMenu;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_form.class.php 216 2009-11-13 14:08:06Z kariboe $ updated by Shoira Mukhsinova
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @package application.lib.weblcms.course_group
 */
class CourseGroupForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_ADD_COURSE_GROUP_TITLES = 3;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    const PARENT_GROUP_SELECTION = 'parent_group_selection';
    const PARENT_GROUP_NONE = 'parent_group_none';
    const PARENT_GROUP_EXISTING = 'parent_group_existing';
    const PARENT_GROUP_NEW = 'parent_group_new';
    const OPTION_PARENT_GROUP_NONE = 0;
    const OPTION_PARENT_GROUP_EXISTING = 1;
    const OPTION_PARENT_GROUP_NEW = 2;

    // private $parent;
    private $course_group;

    private $form_type;

    private $rights;

    public function __construct($form_type, $course_group, $action)
    {
        parent :: __construct('course_settings', 'post', $action);
        $this->form_type = $form_type;
        $this->course_group = $course_group;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $counter = 0;
            $this->build_editing_form($counter);
            $this->setDefaults(array(), $counter);
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->add_top_fields();
            $this->build_creation_form();
            $this->setDefaults();
        }
        elseif (($this->form_type == self :: TYPE_ADD_COURSE_GROUP_TITLES))
        {
            $this->add_top_fields();
            $this->setDefaults();
        }
    }

    public function add_top_fields()
    {
        $this->build_header(Translation :: get("NewCourseGroup"));
        // $group = array();
        // $this->addRule(CourseGroupForm::COURSE_GROUP_QUANTITY, Translation
        // ::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
        // 'regex', '/^[0-9]*$/');
    }

    /**
     * Builds a header of the form
     *
     * @param $header_title string The title to be shown in the header of the form
     */
    public function build_header($header_title)
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement(
            'html',
            '<span class="category">' . $header_title . // Translation :: get($header_title, null, Utilities ::
                                                        // COMMON_LIBRARIES) .
            '</span>');
    }

    /**
     * Closes the divs of the healer of the form
     */
    public function close_header()
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
    }

    public function build_parent_form_create($counter = '')
    {
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            self :: PARENT_GROUP_SELECTION,
            '',
            Translation :: get('NoParentGroup'),
            self :: OPTION_PARENT_GROUP_NONE,
            array('id' => self :: PARENT_GROUP_NONE));
        $choices[] = $this->createElement(
            'radio',
            self :: PARENT_GROUP_SELECTION,
            '',
            Translation :: get('ExistingParentGroup'),
            self :: OPTION_PARENT_GROUP_EXISTING,
            array('id' => self :: PARENT_GROUP_EXISTING));
        $choices[] = $this->createElement(
            'radio',
            self :: PARENT_GROUP_SELECTION,
            '',
            Translation :: get('NewParentGroup'),
            self :: OPTION_PARENT_GROUP_NEW,
            array('id' => self :: PARENT_GROUP_NEW));
        $this->addGroup($choices, null, Translation :: get('ParentGroupType'), '<br/>', false);

        $this->addElement('html', '<div id="parent_group_list">');
        $this->addElement(
            'select',
            CourseGroup :: PROPERTY_PARENT_ID . $counter,
            Translation :: get('GroupParent'),
            // 'select', CourseGroup :: PROPERTY_PARENT_ID . $counter, null,
            $this->get_groups());
        /*
         * $this->addRule( CourseGroup :: PROPERTY_PARENT_ID . $counter, Translation :: get('ThisFieldIsRequired', null,
         * Utilities :: COMMON_LIBRARIES), 'required' );
         */
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="parent_group_name">');
        // TODO :: hardcoded 'parent_' (throughout this entire method)
        $this->addElement(
            'text',
            'parent_' . CourseGroup :: PROPERTY_NAME,
            Translation :: get('ParentGroupTitle'),
            array('id' => 'parent_' . CourseGroup :: PROPERTY_NAME, "size" => "50"));
        $this->addRule(
            'parent_' . CourseGroup :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="parent_group_max_registrations">');
        $this->addElement(
            'text',
            'parent_' . CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation :: get('MaximumGroupSubscriptionsPerMember'),
            array('id' => 'parent_' . CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, 'size' => "4"));
        $this->addRule(
            'parent_' . CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');
        $this->addRule(
            'parent_' . CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="parent_group_random">');
        $this->addElement('checkbox', CourseGroup :: PROPERTY_RANDOM_REG,
            // Translation :: get('RandomRegistration'), Translation :: get('RegisterCourseUsersRandomly')
            null, Translation :: get('RegisterCourseUsersRandomly'));
        $this->addElement('html', '</div>');
    }

    public function build_options_form_create($counter = '')
    {
        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation :: get('MaxNumberOfMembers'),
            'size="4"');
        $this->addRule(
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'textarea',
            CourseGroup :: PROPERTY_DESCRIPTION . $counter,
            Translation :: get('Description'),
            'cols="50"');

        $this->addElement(
            'checkbox',
            CourseGroup :: PROPERTY_SELF_REG . $counter,
            Translation :: get('Registration'),
            Translation :: get('SelfRegAllowed'));
        $this->addElement(
            'checkbox',
            CourseGroup :: PROPERTY_SELF_UNREG . $counter,
            null,
            Translation :: get('SelfUnRegAllowed'));

        $this->add_tools($this->course_group, $counter);

        $this->close_header();
    }

    /**
     * Builds a static part of the form which does not expand while creating several course groups refers to the
     * section: Properties
     */
    public function build_basic_form($counter = '')
    {
        $this->addElement(
            'select',
            CourseGroup :: PROPERTY_PARENT_ID . $counter,
            Translation :: get('GroupParent'),
            $this->get_groups());
        $this->addRule(
            CourseGroup :: PROPERTY_PARENT_ID . $counter,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter,
            Translation :: get('MaximumGroupSubscriptionsPerMember'),
            'size="4"');
        $this->addRule(
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation :: get('MaxNumberOfMembers'),
            'size="4"');
        $this->addRule(
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'textarea',
            CourseGroup :: PROPERTY_DESCRIPTION . $counter,
            Translation :: get('Description'),
            'cols="50"');

        $this->addElement(
            'checkbox',
            CourseGroup :: PROPERTY_SELF_REG . $counter,
            Translation :: get('Registration'),
            Translation :: get('SelfRegAllowed'));
        $this->addElement(
            'checkbox',
            CourseGroup :: PROPERTY_SELF_UNREG . $counter,
            null,
            Translation :: get('SelfUnRegAllowed'));

        $this->add_tools($this->course_group, $counter);

        $this->close_header();
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
        return parent :: validate();
    }

    /**
     * Creates a document category for the course_group
     *
     * @param $element string value
     * @param $course_group CourseGroup
     * @return ContentObjectPublicationCategory
     */
    public function create_course_group_document_category($element_value, &$course_group)
    {
        switch ($element_value)
        {
            case 1 :

                $tool = 'document';
                $group_document_id = 0;

                $course_group_document_category = $this->create_category($course_group, $tool, $group_document_id);
                if (is_object($course_group_document_category))
                {
                    $course_group_document_category_id = $course_group_document_category->get_id();
                }
                else
                {
                    $course_group_document_category_id = $course_group_document_category;
                    $course_group_document_category = DataManager :: retrieve_by_id(
                        ContentObjectPublicationCategory :: class_name(),
                        $course_group_document_category_id);
                }
                if ($course_group_document_category_id)
                {
                    $course_group->set_document_category_id($course_group_document_category_id);

                    // settings rights to publication_category
                    $rights = $this->rights;
                    /*
                     * $rights[] = WeblcmsRights :: VIEW_RIGHT; $rights[] = WeblcmsRights :: ADD_RIGHT;
                     */

                    if ($course_group_document_category)
                    {
                        $this->set_rights_content_object_publication_category(
                            $course_group_document_category,
                            $course_group,
                            $rights);
                    }
                }
                else
                // error message that document category was not created
                {
                    $course_group->add_error(Translation :: get('DocumentCategoryIsNotCreated'));
                }

                break;
            case 0 :
                $course_group->set_document_category_id($element_value);
                break;
            case null :
                break;
        }
        return $course_group_document_category;
    }

    /**
     * Creates a forum category for the course_group
     *
     * @param $element string value (checkbox value in the form indicating the creation of the Forum)
     * @param $course_group CourseGroup
     * @return ContentObjectPublicationCategory
     */
    public function create_course_group_forum_category($element_value, &$course_group)
    {
        switch ($element_value)
        {
            case 1 :

                $tool = 'forum';
                $group_forum_id = 0;

                $course_group_forum_category = $this->create_category($course_group, $tool, $group_forum_id);
                if (is_object($course_group_forum_category))
                {
                    $course_group_forum_category_id = $course_group_forum_category->get_id();
                }
                else
                {
                    $course_group_forum_category_id = $course_group_forum_category;
                    $course_group_forum_category = DataManager :: retrieve_by_id(
                        ContentObjectPublicationCategory :: class_name(),
                        $course_group_forum_category_id);
                }
                if ($course_group_forum_category_id)
                {
                    $course_group->set_forum_category_id($course_group_forum_category_id);
                    // rights
                    $rights = array();
                    $rights[] = WeblcmsRights :: VIEW_RIGHT;
                    $rights[] = WeblcmsRights :: ADD_RIGHT;

                    if ($course_group_forum_category)
                    {
                        $this->set_rights_content_object_publication_category(
                            $course_group_forum_category,
                            $course_group,
                            $rights);
                    }
                }

                break;
            case 0 :
                $course_group->set_forum_category_id($element_value);
                break;
            case null :
                break;
        }
        return $course_group_forum_category;
    }

    public function create_course_group_forum_in_category($forum_category, $course_group)
    {
        $tool = 'forum';
        $content_object_publication = $this->publish_forum($tool, $forum_category->get_id(), $course_group);
        $rights = array();
        $rights[] = WeblcmsRights :: VIEW_RIGHT;
        $rights[] = WeblcmsRights :: ADD_RIGHT;
        $this->set_rights_content_object_publication($content_object_publication, $course_group, $rights);
    }

    /**
     * Updates the course group
     *
     * @return boolean $result True when successful
     */
    public function update_course_group()
    {
        $values = $this->exportValues();
        $course_group = $this->course_group;
        $course_id = $this->course_group->get_course_code();

        $counter = 1;

        $data_set = $this->course_group->get_children(false);

        if ($this->course_group->get_errors() == null)
        {
            // group size check -> total size must not be greater than parent group's max size
            $course_groups = array();
            $parent_cgid = null;
            $total_size_diff = 0;

            if (! $data_set->is_empty())
            {
                while ($course_group = $data_set->next_result())
                {
                    $course_groups[] = $course_group;
                    $parent_cgid = $course_group->get_parent_id();
                    $total_size_diff += $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter];
                    $total_size_diff -= $course_group->get_max_number_of_members();
                    $counter ++;
                }

                $parent_course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $parent_cgid);
                // existing groups size
                $total_size = $total_size_diff;
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                    new StaticConditionVariable($parent_course_group->get_id()));

                $c_course_groups = DataManager :: retrieves(CourseGroup :: class_name(), $condition);

                while ($course_group = $c_course_groups->next_result())
                {
                    $total_size += $course_group->get_max_number_of_members();
                }

                $parent_group_form_max_number_of_members = $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . '0'];

                if ($parent_group_form_max_number_of_members > 0 &&
                     $total_size > $parent_group_form_max_number_of_members)
                {
                    $this->course_group->add_error(Translation :: get('MaxMembersFromChildrenTooBigForParentCourseGroup'));
                    return false;
                }
            }
            else
            {
                $parent_course_group = DataManager :: retrieve_by_id(
                    CourseGroup :: class_name(),
                    $this->course_group->get_parent_id());
                if ($parent_course_group->get_max_number_of_members() > 0)
                {
                    $parent_course_group_children = $parent_course_group->get_children(false);
                    $total_size = 0;

                    while ($child_group = $parent_course_group_children->next_result())
                    {
                        if ($child_group->get_id() == $this->course_group->get_id())
                            $total_size += $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . 0];
                        else
                            $total_size += $child_group->get_max_number_of_members();
                    }

                    if ($parent_course_group->get_max_number_of_members() < $total_size)
                    {
                        $this->course_group->add_error(Translation :: get('MaxMembersTooBigForParentCourseGroup'));
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
                $course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $course_group->get_id());

                $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION . $counter]);
                $course_group->set_max_number_of_members(
                    $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter]);
                $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG . $counter]);
                $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG . $counter]);
                $course_group->set_max_number_of_course_group_per_member(
                    $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter]);

                // if the name changes, update the corresponding document
                // category and or forum if necessary
                if ($course_group->get_name() != $values[CourseGroup :: PROPERTY_NAME . $counter])
                {
                    $old_name = $course_group->get_name();
                    $course_group->set_name($values[CourseGroup :: PROPERTY_NAME . $counter]);
                    if ($this->course_group_name_exists($course_group))
                    {
                        $counter ++;
                        $this->course_group->add_error(
                            Translation :: get(
                                'CourseGroupNotUpdated',
                                array('NAME_OLD' => $old_name, 'NAME_NEW' => $course_group->get_name())));
                        continue;
                    }
                    $document_category_id = $course_group->get_optional_property(
                        CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID);
                    if ($document_category_id)
                    {
                        $document_category = DataManager :: retrieve_by_id(
                            ContentObjectPublicationCategory :: class_name(),
                            $document_category_id);
                        $document_category->set_name($values[CourseGroup :: PROPERTY_NAME . $counter]);
                        $document_category->update();
                    }
                    $forum_category_id = $course_group->get_optional_property(CourseGroup :: PROPERTY_FORUM_CATEGORY_ID);
                    if ($forum_category_id)
                    {
                        $forum_category = DataManager :: retrieve_by_id(
                            ContentObjectPublicationCategory :: class_name(),
                            $forum_category_id);
                        $forum_category->set_name($values[CourseGroup :: PROPERTY_NAME . $counter]);
                        $forum_category->update();

                        // TODO :: Should we rename the actual forum that was created by checking the checkbox too? Do
                        // note that it might not exist anymore or may have been renamed manually.
                    }
                }
                if (isset($values, $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter]))
                {
                    if ($course_group->get_document_category_id() == 0) // it
                                                                        // doesn't
                                                                        // exist
                                                                        // yet
                    {
                        $this->create_course_group_document_category(
                            $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter],
                            $course_group,
                            false); // After rework with parent group, this should always be false
                    }
                }
                else
                {
                    if ($course_group->get_document_category_id() != 0) // unlink
                                                                        // the
                                                                        // document
                                                                        // category
                    {
                        $document_category = DataManager :: retrieve_by_id(
                            ContentObjectPublicationCategory :: class_name(),
                            $course_group->get_document_category_id());
                        if ($document_category)
                        {
                            $document_category->set_allow_change(1);
                            // $document_category->update();
                            $document_category->delete();
                        }
                        $course_group->set_document_category_id(0);
                    }
                }
                if (isset($values, $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter]))
                {
                    if ($course_group->get_forum_category_id() == 0) // it
                                                                     // doesn't
                                                                     // exist yet
                    {
                        $course_group_forum_category = $this->create_course_group_forum_category(
                            $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter],
                            $course_group,
                            false); // After rework with parent group, this should always be false

                        $this->create_course_group_forum_in_category($course_group_forum_category, $course_group);
                    }
                }
                else
                {
                    if ($course_group->get_forum_category_id() != 0) // unlink
                                                                     // the
                                                                     // document
                                                                     // category
                    {
                        $forum_category = DataManager :: retrieve_by_id(
                            ContentObjectPublicationCategory :: class_name(),
                            $course_group->get_forum_category_id());
                        if ($forum_category)
                        {
                            $forum_category->set_allow_change(1);
                            $forum_category->delete();
                        }
                        $course_group->set_forum_category_id(0);
                    }
                }
                if (! $course_group->update())
                {
                    return false;
                }

                // Change the parent
                if ($course_group->get_parent_id() != $values[CourseGroup :: PROPERTY_PARENT_ID . $counter])
                {
                    if (! $course_group->move($values[CourseGroup :: PROPERTY_PARENT_ID . $counter]))
                    {
                        return false;
                    }
                }
                $counter ++;
            }
            return count($this->course_group->get_errors()) == 0;
        }
        else
        {
            return false;
        }
    }

    public function build_basic_editing_form()
    {
        $this->build_header($this->course_group->get_name());
        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_NAME,
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES),
            array("size" => "50"));

        $this->addElement(
            'select',
            CourseGroup :: PROPERTY_PARENT_ID,
            Translation :: get('GroupParent'),
            $this->get_groups());
        $this->addRule(
            CourseGroup :: PROPERTY_PARENT_ID,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS,
            Translation :: get('MaxNumberOfMembers'),
            'size="4"');
        $this->addRule(
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS,
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
            'regex',
            '/^[0-9]*$/');

        $this->addElement(
            'textarea',
            CourseGroup :: PROPERTY_DESCRIPTION,
            Translation :: get('Description'),
            'cols="50"');

        $this->addElement(
            'checkbox',
            CourseGroup :: PROPERTY_SELF_REG,
            Translation :: get('Registration'),
            Translation :: get('SelfRegAllowed'));
        $this->addElement('checkbox', CourseGroup :: PROPERTY_SELF_UNREG, null, Translation :: get('SelfUnRegAllowed'));

        $this->add_tools($this->course_group, null);
        $this->close_header();
    }

    /**
     * Adds the Documents and Forum checkboxes in the form, If the course group does not have the document and forum
     * tool activated, show the chechboxes to activate them
     *
     * @param $course_group CourseGroup
     * @param $i int Number to couple the element to the course group
     */
    public function add_tools($course_group, $counter)
    {
        if ($course_group->get_document_category_id() > 0)
        {
            // Editing form with linked document category
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter,
                Translation :: get('CreateGroupTools'),
                Translation :: get('Document'),
                array("value" => $course_group->get_document_category_id()));
            $this->addElement(
                'html',
                '<div id="tool_unchecked_warning_' . CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter .
                     '" class="row tool_unchecked_warning hidden"><div class="formw">' . '<div class="warning-message">' .
                     Translation :: get('DocumentToolUncheckedWarning') . '</div>' . '</div></div>');
        }
        else
        {
            // Creation form or editing form without linked document category
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter,
                Translation :: get('CreateGroupTools'),
                Translation :: get('Document'));
        }

        if ($course_group->get_forum_category_id() > 0)
        {
            // Editing form with linked forum
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter,
                null,
                Translation :: get('Forum'),
                array("value" => $course_group->get_forum_category_id()));
            $this->addElement(
                'html',
                '<div id="tool_unchecked_warning_' . CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter .
                     '" class="row tool_unchecked_warning hidden"><div class="formw">' . '<div class="warning-message">' .
                     Translation :: get('ForumToolUncheckedWarning') . '</div>' . '</div></div>');
        }

        else
        {
            // Creation form or editing form without linked forum
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter,
                null,
                Translation :: get('Forum'));
        }
    }

    /**
     * Extends the basic editing form when the the course group is chosen to be edited which has been created in the
     * group
     */
    public function build_extended_editing_form()
    {
        $counter = 1; // Index 1 means the first child, index 0 is the 'parent' course group.
        $data_set = $this->course_group->get_children(false);

        while ($next = $data_set->next_result())
        {
            $course_groups = $next;
            $this->build_header($course_groups->get_name());

            $this->addElement(
                'text',
                CourseGroup :: PROPERTY_NAME . $counter,
                Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES),
                array("size" => "50"));

            $this->addElement(
                'select',
                CourseGroup :: PROPERTY_PARENT_ID . $counter,
                Translation :: get('GroupParent'),
                $this->get_groups());
            $this->addRule(
                CourseGroup :: PROPERTY_PARENT_ID . $counter,
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
                'required');

            $this->addElement(
                'text',
                CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
                Translation :: get('MaxNumberOfMembers'),
                'size="4"');
            $this->addRule(
                CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter,
                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                'regex',
                '/^[0-9]*$/');

            $this->addElement(
                'textarea',
                CourseGroup :: PROPERTY_DESCRIPTION . $counter,
                Translation :: get('Description'),
                'cols="100"');
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_SELF_REG . $counter,
                Translation :: get('Registration'),
                Translation :: get('SelfRegAllowed'));
            $this->addElement(
                'checkbox',
                CourseGroup :: PROPERTY_SELF_UNREG . $counter,
                null,
                Translation :: get('SelfUnRegAllowed'));

            $this->add_tools($course_groups, $counter);

            $this->addElement('hidden', CourseGroup :: PROPERTY_ID . $counter);
            $this->addElement('hidden', CourseGroup :: PROPERTY_PARENT_ID . $counter . 'old');
            $this->addElement('hidden', CourseGroup :: PROPERTY_COURSE_CODE . $counter);

            $this->setDefaults_extended($counter, $course_groups);
            $this->close_header();
            $counter ++;
        }
        $this->close_header();
    }

    public function add_name_field($number = null)
    {
        $element = $this->createElement(
            'text',
            CourseGroup :: PROPERTY_NAME . $number,
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES),
            array("size" => "50"));
        return $element;
    }

    public function get_groups()
    {
        $course = new Course();
        $course->set_id($this->course_group->get_course_code());
        $menu = new CourseGroupMenu($course, 0);
        $renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    public function build_editing_form($counter)
    {
        if ($this->course_group->count_children(false) > 0)
        {
            $tabs_renderer = new DynamicFormTabsRenderer($this->form_name, $this);

            $tabs_renderer->add_tab(
                new DynamicFormTab('parent', $this->course_group->get_name(), null, 'build_parent_tab_form_elements'));

            $tabs_renderer->add_tab(
                new DynamicFormTab(
                    'children',
                    Translation :: get('CourseGroupChildren'),
                    null,
                    'build_children_tab_form_elements'));

            $tabs_renderer->render();
        }
        else
        {
            $this->build_parent_tab_form_elements();
        }

        $this->addElement('hidden', CourseGroup :: PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive update'));

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_parent_tab_form_elements()
    {
        $counter = 0; // Index 0 is the 'parent' course group.
        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup',
                    true) . 'CourseGroupEditForm.js'));

        $this->build_header($this->course_group->get_name());
        $this->addElement(
            'text',
            CourseGroup :: PROPERTY_NAME . $counter,
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES),
            array("size" => "50"));

        $this->build_basic_form($counter);
        // $this->close_header();
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
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }

        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 1;
        }

        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
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

        $defaults = array();

        $qty = $qty_groups;

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup',
                    true) . 'CourseGroupForm.js'));

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->add_name_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement(
                        'image',
                        'remove[' . $option_number . ']',
                        Theme :: getInstance()->getCommonImagePath('Action/ListRemove'),
                        array('style="border: 0px;"'));
                }
                // numbering of the titels
                if ($numbering < $qty_groups)
                {
                    $numbering ++;
                }

                $this->addGroup(
                    $group,
                    CourseGroup :: PROPERTY_NAME . $option_number,
                    Translation :: get('CountedTitle', array('COUNT' => $numbering)),
                    '',
                    false);
                // fill the title field automatically
                // $defaults[CourseGroup :: PROPERTY_NAME . $option_number] =
                // 'Group ' . $numbering;
                parent :: setDefaults($defaults);
                $this->addRule(
                    CourseGroup :: PROPERTY_NAME . $option_number,
                    Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
                    'required');
            }
        }
        // parent :: setDefaults($defaults);

        $this->addElement(
            'image',
            'add[]',
            Theme :: getInstance()->getCommonImagePath('Action/ListAdd'),
            array("title" => Translation :: get('AddGroupExplained')));
        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup',
                    true) . 'CourseGroupForm.js'));

        $this->build_header(Translation :: get('CourseGroupParent'));
        $this->build_parent_form_create();
        $this->close_header();

        $this->build_header(Translation :: get('CourseGroupOptions'));
        $this->build_options_form_create();
        $this->close_header();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * A course_group title should be unique per course as a document and forum category names correspond to the
     * course_group name This method checks if a course group witht the same title already exists for this course
     *
     * @param $course_group CourseGroup
     * @return boolean
     */
    public function course_group_name_exists($course_group)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME),
            new StaticConditionVariable($course_group->get_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE),
            new StaticConditionVariable($course_group->get_course_code()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
            new StaticConditionVariable($course_group->get_parent_id()));

        // If updating, the name will already exist in the database if the name has not been changed -
        // Exclude course group being updated.
        // If creating, course group will not yet have an id.
        if ($course_group->get_id())
        {
            $not_condition = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID),
                new StaticConditionVariable($course_group->get_id()));
            $conditions[] = new NotCondition($not_condition);
        }
        $condition = new AndCondition($conditions);

        $data_set = DataManager :: retrieves(CourseGroup :: class_name(), new DataClassRetrievesParameters($condition));

        return ($data_set->size() > 0);
    }

    /**
     * This methos creates one or several course_groups for the given course If checked document and forum publications
     * are created with the same name as the course_group titel.
     *
     * @param
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * @return boolean
     */
    public function create_course_group()
    {
        $this->rights = array();
        $this->rights[] = WeblcmsRights :: VIEW_RIGHT;
        $this->rights[] = WeblcmsRights :: ADD_RIGHT;

        $course_group = $this->course_group;
        $course_code = $course_group->get_course_code();
        $values = $this->exportValues();
        $new_titles = preg_grep('/^name*[0-9]*$/', array_keys($values));
        $qty = sizeof($new_titles);
        $groups = array();
        $errors = array();

        // check parent's max size >= combined total size of parent's children (could be more than just this group)
        // new children size

        $new_max_size = $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS] * $qty;

        $parent_group_select = $values[self :: PARENT_GROUP_SELECTION];
        $parent_group_id = null;
        $parent_group = null;

        switch ($parent_group_select)
        {
            case self :: OPTION_PARENT_GROUP_NONE :
                $parent_course_group = DataManager :: retrieve_course_group_root($course_code);
                if (! $this->check_parent_max_members($parent_course_group, $new_max_size))
                    return false;

                $parent_group_id = $parent_course_group->get_id();
                $values[CourseGroup :: PROPERTY_PARENT_ID] = $parent_group_id;
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
            case self :: OPTION_PARENT_GROUP_EXISTING :
                $parent_group_id = $values[CourseGroup :: PROPERTY_PARENT_ID];
                $parent_course_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $parent_group_id);
                if (! $this->check_parent_max_members($parent_course_group, $new_max_size))
                    return false;
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
            case self :: OPTION_PARENT_GROUP_NEW :

                $parent_values = array();
                $parent_values[CourseGroup :: PROPERTY_NAME] = $values['parent_' . CourseGroup :: PROPERTY_NAME];
                $parent_values[CourseGroup :: PROPERTY_DESCRIPTION] = $values['parent_' . CourseGroup :: PROPERTY_NAME];
                $parent_values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER] = $values['parent_' .
                     CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER];
                $parent_values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS] = 0;
                $parent_values[CourseGroup :: PROPERTY_SELF_REG] = false;
                $parent_values[CourseGroup :: PROPERTY_SELF_UNREG] = false;
                $parent_values[CourseGroup :: PROPERTY_PARENT_ID] = DataManager :: retrieve_course_group_root(
                    $course_code)->get_id();

                $parent_group = $this->construct_course_group(
                    CourseGroup :: PROPERTY_NAME,
                    $course_code,
                    $parent_values);

                if ($this->course_group_name_exists($parent_group))
                {
                    $this->course_group->add_error(
                        Translation :: get('CourseGroupTitleExists', array('NAME' => $parent_group->get_name())));
                }

                if (! $parent_group->create())
                {
                    $course_group->add_error(
                        Translation :: get('CannotCreateCourseGroup', array('NAME' => $parent_group->get_name())));
                }

                $parent_group_id = $parent_group->get_id();
                $values[CourseGroup :: PROPERTY_PARENT_ID] = $parent_group_id;
                $groups = $this->construct_course_groups($new_titles, $course_code, $values);
                break;
        }

        if ($parent_group_id)
        {
            $parent_group = DataManager :: retrieve_by_id(CourseGroup :: class_name(), $parent_group_id);
        }
        else
        {
            $parent_group = DataManager :: retrieve_course_group_root($course_code);
        }
        if (sizeof($groups) > 0)
        {
            foreach ($groups as $course_group)
            {
                if (! $this->course_group_name_exists($course_group))
                {
                    if ($course_group->create())
                    {
                        if (isset($values, $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID]))
                        {
                            $this->create_course_group_document_category(
                                $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID],
                                $course_group);
                        }
                        if (isset($values, $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID]))
                        {
                            $course_group_forum_category = $this->create_course_group_forum_category(
                                $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID],
                                $course_group);

                            $this->create_course_group_forum_in_category($course_group_forum_category, $course_group);
                        }
                        $course_group->update();
                    }
                    else
                    {
                        $course_group->add_error(
                            Translation :: get('CreationFailed', array('NAME' => $course_group->get_name())));
                    }
                }
                else
                {
                    $this->course_group->add_error(
                        Translation :: get('CourseGroupTitleExists', array('NAME' => $course_group->get_name())));
                }
            }
        }

        if (isset($values, $values[CourseGroup :: PROPERTY_RANDOM_REG]))
        {
            switch ($parent_group_select)
            {
                case self :: OPTION_PARENT_GROUP_NONE :
                    break;
                case self :: OPTION_PARENT_GROUP_EXISTING :
                    $this->random_user_subscription_to_course_groups($parent_group);
                    break;
                case self :: OPTION_PARENT_GROUP_NEW :
                    $this->random_user_subscription_to_course_groups($parent_group);
                    break;
            }
        }
        return ($this->course_group->get_errors());
    }

    /**
     *
     * @param \application\weblcms\tool\implementation\course_group\CourseGroup $parent_course_group
     * @param int $child_max_size
     * @return bool
     */
    private function check_parent_max_members($parent_course_group, $child_max_size)
    {
        if ($parent_course_group->get_max_number_of_members() == 0)
            return true;

            // existing groups size
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_course_group->get_id()));
        $course_groups = DataManager :: retrieves(
            CourseGroup :: class_name(),
            new DataClassRetrievesParameters($condition));

        $size_children = 0;
        while ($existing_course_group = $course_groups->next_result())
        {
            $size_children += $existing_course_group->get_max_number_of_members();
        }

        if ($size_children + $child_max_size > $parent_course_group->get_max_number_of_members())
        {
            $this->course_group->add_error(Translation :: get('MaxMembersTooBigForParentCourseGroup'));
            return false;
        }

        return true;
    }

    /**
     * Creates a set of new course groups based on the titles received.
     *
     * @param string[] $new_titles The titles for which new course groups are to be created.
     * @param string $course_code The course code.
     * @param mixed[] $values The form values.
     * @return CourseGroup[] The constructed, but not created CourseGroups.
     */
    private function construct_course_groups($new_titles, $course_code, $values)
    {
        $course_groups = array();
        foreach ($new_titles as $new_title)
        {
            $course_groups[] = $this->construct_course_group($new_title, $course_code, $values);
        }
        return $course_groups;
    }

    /**
     * Creates a new CourseGroup.
     *
     * @param string $new_title The title of the new CourseGroup.
     * @param string The course code.
     * @param mixed[] $values The form values.
     * @return CourseGroup The constructed, but not created CourseGroup.
     */
    private function construct_course_group($new_title, $course_code, $values)
    {
        $course_group = new CourseGroup();
        $course_group->set_name($values[$new_title]);
        $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
        $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
        $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
        $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
        $course_group->set_parent_id($values[CourseGroup :: PROPERTY_PARENT_ID]);
        if ($values[CourseGroup :: PROPERTY_RANDOM_REG])
        {
            $course_group->set_random_registration_done($values[CourseGroup :: PROPERTY_RANDOM_REG]);
        }
        if ($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER])
        {
            $course_group->set_max_number_of_course_group_per_member(
                $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER]);
        }
        $course_group->set_course_code($course_code);
        return $course_group;
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
        $qty_titles = (int) $values[CourseGroupForm :: COURSE_GROUP_QUANTITY];

        $_SESSION['mc_number_of_options'] = $qty_titles;
        $this->build_creation_form();

        // for ($counter=0; $){
        //
        // }
        // created titles in the loop
        // for ($option_number = 0; $option_number < $qty_titles;
        // $option_number++)
        // {
        // $group = array();
        // $group[] = $this->add_name_field($option_number);
        // // if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
        // // {
        // $group[] = $this->createElement('image', 'remove[' . $option_number .
        // ']', Theme :: getInstance()->getCommonImagePath('action_list_remove'),
        // array('style="border: 0px;"'));
        // // }
        // //numbering of the titels
        // if ($numbering < $qty_titles)
        // {
        // $numbering++;
        // }
        //
        // $this->addGroup($group, CourseGroup::PROPERTY_NAME . $option_number,
        // Translation :: get('Title') . $numbering, '', false);
        // }
    }

    /*
     * randomly selects the users from the course users and subscribes to course group takes into the account the max
     * number of members
     */
    public function random_user_subscription_to_course_group($course_group)
    {
        $course_code = $course_group->get_course_code();
        $max_num_members = $course_group->get_max_number_of_members();

        // randomize course_users
        $course_users_data_set = CourseDataManager :: retrieve_all_course_users($course_code, null, null, null);
        $course_users = array();
        while ($course_user = $course_users_data_set->next_result())
        {
            $course_users[] = $course_user;
        }
        shuffle($course_users);

        $members_to_add = array();
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
            $member = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_username(
                $course_users[$count]->get_username());
            $members_to_add[] = $member->get_id();

            $count ++;
        }
        $course_group->subscribe_users($members_to_add);
    }

    /*
     * Randomly selects the users from the course users and subscribes them in the course groups directly under the
     * given parent course group. It takes into the account the max number of members and the maximum number of
     * subscriptions allowed for a course user. This method front-loads (it will fill up spaces as soon as it gets them.
     * If there are no more users with unused subscriptions, all remaining groups will remain empty). @param CourseGroup
     * $parent_course_group The parent of the course groups to which the random subscription applies. @return bool True
     * if all the available spaces are filled. False otherwise.
     */
    public function random_user_subscription_to_course_groups($parent_course_group)
    {
        $course_users_drs = CourseDataManager :: retrieve_all_course_users(
            $parent_course_group->get_course_code(),
            null,
            null,
            null);
        $course_users = array();
        if ($course_users_drs)
        {
            while ($course_user = $course_users_drs->next_result())
            {
                $course_users[$course_user[User :: PROPERTY_ID]] = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    $course_user[User :: PROPERTY_ID]);
            }
        }

        $course_groups = $parent_course_group->get_children(false)->as_array();

        $max_number_subscriptions = $parent_course_group->get_max_number_of_course_group_per_member();
        $user_number_subscriptions = array();

        foreach ($course_groups as $course_group)
        {
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
            if (! $all_groups_filled)
            {
                break;
            }
            $subscribed_users_drs = $course_group->get_members(true, true);
            $subscribed_users = array();
            if ($subscribed_users_drs)
            {
                foreach ($subscribed_users_drs as $user_id)
                {
                    $subscribed_users[$user_id] = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                        User :: class_name(),
                        $user_id);
                }
            }
            $max_subscribed_users = $course_group->get_max_number_of_members();
            $new_users = array();
            while (count($new_users) < $max_subscribed_users - count($subscribed_users) && count($course_users) > 0)
            {
                $random_int = mt_rand(0, count($course_users) - 1);
                if (! array_key_exists($course_users[$random_int]->get_id(), $new_users) &&
                     ! array_key_exists($course_users[$random_int]->get_id(), $subscribed_users))
                {
                    $new_users[$course_users[$random_int]->get_id()] = $course_users[$random_int];
                    $user_number_subscriptions[$course_users[$random_int]->get_id()] = $user_number_subscriptions[$course_users[$random_int]->get_id()] +
                         1;

                    if ($user_number_subscriptions[$course_users[$random_int]->get_id()] >= $max_number_subscriptions)
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
            if ($course_group->count_members() < $max_subscribed_users)
            {
                // TODO :: Why was this needed? This means that only the first group will be filled ...
                // $all_groups_filled = false;
            }
        }
        return $all_groups_filled;
    }

    /*
     * Publish a forum for a course group and returns a new content_object_publication
     */
    public function publish_forum($tool, $category_id, $course_group)
    {
        $forum = new Forum();
        $forum->set_title($course_group->get_name());
        $forum->set_locked(0);
        $forum->set_description($course_group->get_name() . " forum");
        $user_id = $_SESSION['_uid'];
        $forum->set_owner_id($user_id);
        $forum->create();

        $content_object_publication = new ContentObjectPublication();
        $content_object_publication->set_category_id($category_id);
        $content_object_publication->set_tool($tool);
        $content_object_publication->set_course_id($course_group->get_course_code());
        $content_object_publication->set_parent_id('0');
        $content_object_publication->set_publisher_id($forum->get_owner_id());

        $content_object_publication->set_content_object($forum);
        $content_object_publication->set_content_object_id($forum->get_id());
        $content_object_publication->set_publication_date(time());
        $content_object_publication->set_modified_date(time());
        $content_object_publication->create();

        return $content_object_publication;
    }

    /*
     * Sets the the given rights to publication object
     */
    public function set_rights_content_object_publication($content_object_publication, $course_group, $rights)
    {
        $context = \Chamilo\Application\Weblcms\Manager :: context();

        $weblcms_rights = WeblcmsRights :: get_instance();
        $entity_id = $course_group->get_id();
        $entity_type = CourseGroupEntity :: ENTITY_TYPE;
        $course_id = $course_group->get_course_code();
        $category_id = $content_object_publication->get_id();

        $location = $weblcms_rights->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights :: TYPE_PUBLICATION,
            $category_id,
            $course_id);

        $location->disinherit();
        $location_id = $location->get_id();
        $location->update();

        foreach ($rights as $right)
        {
            $weblcms_rights->set_location_entity_right($context, $right, $entity_id, $entity_type, $location_id);
        }

        // publish fot the owner. Without it the forum is invisibale for the
        // publisher
        $entity_id = $content_object_publication->get_publisher_id();
        $entity_type = UserEntity :: ENTITY_TYPE;

        $weblcms_rights->set_location_entity_right(
            $context,
            WeblcmsRights :: VIEW_RIGHT,
            $entity_id,
            $entity_type,
            $location_id);
    }

    /*
     * sets the rights to publication category. For example to document category
     */
    public function set_rights_content_object_publication_category($content_object_publication_category, $course_group,
        $rights)
    {
        $context = \Chamilo\Application\Weblcms\Manager :: context();
        $weblcms_rights = WeblcmsRights :: get_instance();

        $entity_id = $course_group->get_id();
        $entity_type = CourseGroupEntity :: ENTITY_TYPE;
        $course_id = $course_group->get_course_code();
        $category_id = $content_object_publication_category->get_id();

        // get location object
        $location = $weblcms_rights->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights :: TYPE_COURSE_CATEGORY,
            $category_id,
            $course_id);

        $location->disinherit();
        $location_id = $location->get_id();
        $location->update();

        foreach ($rights as $right)
        {
            $weblcms_rights->set_location_entity_right($context, $right, $entity_id, $entity_type, $location_id);
        }
    }

    /*
     * creates a content object publication category. For example: document publication category
     */
    public function create_category($course_group, $tool, $parent)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_NAME),
            new StaticConditionVariable($course_group->get_name()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($course_group->get_course_code()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_TOOL),
            new StaticConditionVariable($tool));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($parent));
        $condition = new AndCondition($conditions);

        $data_set = DataManager :: retrieves(ContentObjectPublicationCategory :: class_name(), $condition);

        $category = $data_set->next_result();
        if ($category)
        {
            return $category->get_id();
        }
        else
        {
            $content_object_publication_category = new ContentObjectPublicationCategory();
            $content_object_publication_category->set_parent($parent);
            $content_object_publication_category->set_tool($tool);
            $content_object_publication_category->set_course($this->course_group->get_course_code());
            $content_object_publication_category->set_name($course_group->get_name());
            $content_object_publication_category->set_allow_change(0);
            $content_object_publication_category->set_display_order("1");
            $content_object_publication_category->create();
            return $content_object_publication_category;
        }
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array(), $counter = '')
    {
        $course_group = $this->course_group;
        $defaults[CourseGroup :: PROPERTY_NAME . $counter] = $course_group->get_name();
        $defaults[CourseGroup :: PROPERTY_DESCRIPTION . $counter] = $course_group->get_description();

        $defaults[self :: PARENT_GROUP_SELECTION . $counter] = $course_group->get_parent_id() == 0 ? self :: OPTION_PARENT_GROUP_NONE : self :: OPTION_PARENT_GROUP_EXISTING;

        if (is_null($course_group->get_max_number_of_members()))
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] = 20;
        }
        else
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] = $course_group->get_max_number_of_members();
        }

        if (is_null($course_group->get_max_number_of_course_group_per_member()))
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter] = 20;
        }
        else
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER . $counter] = $course_group->get_max_number_of_course_group_per_member();
        }

        $defaults[CourseGroup :: PROPERTY_SELF_REG . $counter] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup :: PROPERTY_SELF_UNREG . $counter] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup :: PROPERTY_RANDOM_REG . $counter] = $course_group->is_random_registration_done();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $counter] = $course_group->get_parent_id();
        $defaults[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter] = $course_group->get_document_category_id();
        $defaults[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter] = $course_group->get_forum_category_id();
        parent :: setDefaults($defaults);
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param $counternteger.
     * @param $course_group Object CourseGroup
     */
    public function setDefaults_extended($counter, $course_group)
    {
        $defaults[CourseGroup :: PROPERTY_NAME . $counter] = $course_group->get_name();
        $defaults[CourseGroup :: PROPERTY_ID . $counter] = $course_group->get_id();
        $defaults[CourseGroup :: PROPERTY_COURSE_CODE . $counter] = $course_group->get_course_code();
        $defaults[CourseGroup :: PROPERTY_DESCRIPTION . $counter] = $course_group->get_description();
        $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $counter] = $course_group->get_max_number_of_members();
        $defaults[CourseGroup :: PROPERTY_SELF_REG . $counter] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup :: PROPERTY_SELF_UNREG . $counter] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $counter] = $course_group->get_parent_id();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $counter . 'old'] = $course_group->get_parent_id();
        $defaults[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $counter] = $course_group->get_document_category_id();
        $defaults[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $counter] = $course_group->get_forum_category_id();
        parent :: setDefaults($defaults);
    }

    public function get_errors()
    {
        return $this->course_group->get_errors();
    }
}
