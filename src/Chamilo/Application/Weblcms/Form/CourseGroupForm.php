<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseGroupRelation;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseGroupGroupRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\CourseGroupMenu;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_form.class.php 216 2009-11-13 14:08:06Z kariboe $ updated by Shoira Mukhsinova
 * 
 * @author Anthony Hurst(Hogeschool Gent)
 * @package application.lib.weblcms.course_group
 * @todo add access modifiers to all functions
 * @todo move all non-'form' logic to other classes (component ?)
 */
class CourseGroupForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    // const TYPE_ADD_COURSE_GROUP_TITLES = 3;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    // const COURSE_GROUP_QUANTITY = 'course_group_quantity';
    const GROUP_GROUP_SELECTION = 'group_group_selection';
    const GROUP_GROUP_NONE = 'group_group_none';
    const GROUP_GROUP_EXISTING = 'group_group_existing';
    const GROUP_GROUP_NEW = 'group_group_new';
    const OPTION_GROUP_GROUP_NONE = 0;
    const OPTION_GROUP_GROUP_EXISTING = 1;
    const OPTION_GROUP_GROUP_NEW = 2;

    private $parent;

    private $course_group;

    private $form_type;

    private $course_group_group_relation;

    private $course_id;

    private $rights;

    /**
     *
     * @param int $form_type indicates the type of form (create, edit, ...)
     * @param unknown_type $course_group
     * @param string $action
     */
    public function __construct($form_type, $course_group, $action)
    {
        parent :: __construct('course_settings', 'post', $action);
        $this->form_type = $form_type;
        $this->course_group = $course_group;
        
        switch ($this->form_type)
        {
            case self :: TYPE_CREATE :
                $this->add_top_fields();
                $this->build_creation_form();
                $this->setDefaults();
                break;
            case self :: TYPE_EDIT :
                $i = 0; // what does $i mean?
                $this->build_editing_form($i);
                $this->setDefaults(array(), $i);
                break;
            /*
             * case self :: TYPE_ADD_COURSE_GROUP_TITLES: $this->add_top_fields(); $this->setDefaults(); break;
             */
        }
    }

    function add_top_fields()
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
    function build_header($header_title)
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement(
            'html', 
            '<span class="category">' . Translation :: get($header_title, null, Utilities :: COMMON_LIBRARIES) .
                 '</span>');
    }

    /**
     * Closes the divs of the healer of the form
     */
    function close_header()
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
    }

    /**
     * Builds a static part of the form which does not expand while creating several course groups refers to the
     * section: Properties
     */
    function build_basic_form($i = '')
    {
        $this->addElement(
            'text', 
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i, 
            Translation :: get('MaxNumberOfMembers'), 
            'size="4"');
        $this->addRule(
            CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i, 
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 
            'regex', 
            '/^[0-9]*$/');
        
        $this->addElement(
            'select', 
            CourseGroup :: PROPERTY_PARENT_ID . $i, 
            Translation :: get('GroupParent'), 
            $this->get_groups());
        $this->addRule(
            CourseGroup :: PROPERTY_PARENT_ID . $i, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'textarea', 
            CourseGroup :: PROPERTY_DESCRIPTION . $i, 
            Translation :: get('Description'), 
            'cols="50"');
        
        $this->addElement(
            'checkbox', 
            CourseGroup :: PROPERTY_SELF_REG . $i, 
            Translation :: get('Registration'), 
            Translation :: get('SelfRegAllowed'));
        $this->addElement(
            'checkbox', 
            CourseGroup :: PROPERTY_SELF_UNREG . $i, 
            null, 
            Translation :: get('SelfUnRegAllowed'));
        
        $this->add_tools($this->course_group, null);
        
        $this->close_header();
    }

    /**
     * Validates the filled in data of the form when the new course group is added
     */
    function validate()
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
     * @param $course_group_group_relation_created boolean true if the CourseGroupGroupRelation is created indicating
     *        there have been more than 1 course_groups being created
     * @return ContentObjectPublicationCategory
     */
    function create_course_group_document_category($element_value, &$course_group, $course_group_group_relation_created)
    {
        switch ($element_value)
        {
            case 1 :
                if ($course_group_group_relation_created == 1)
                {
                    $course_group_group_relation = $this->course_group_group_relation;
                    $parent_title = $course_group_group_relation->get_name();
                    $group_document_id = $course_group_group_relation->get_document_publication_category_id();
                }
                else 
                    if ($course_group_group_relation_created == null)
                    {
                        $group_document_id = 0;
                    }
                
                $tool = 'document';
                if ($course_group_group_relation_created == 1 && $group_document_id == 0)
                {
                    $content_object_publication_category = $course_group_group_relation->create_course_group_category(
                        $tool);
                    $group_document_id = $course_group_group_relation->get_document_publication_category_id();
                }
                
                $course_group_document_category = $this->create_category($course_group, $tool, $group_document_id);
                if (is_object($course_group_document_category))
                {
                    $course_group_document_category_id = $course_group_document_category->get_id();
                }
                else
                {
                    $course_group_document_category_id = $course_group_document_category;
                    $course_group_document_category = DataManager :: retrieve_content_object_publication_category(
                        $course_group_document_category_id);
                }
                if ($course_group_document_category_id)
                {
                    $course_group->set_document_category_id($course_group_document_category_id);
                    
                    // settings rights to publication_category
                    $rights = $this->rights;
                    $rights[] = WeblcmsRights :: VIEW_RIGHT;
                    $rights[] = WeblcmsRights :: ADD_RIGHT;
                    
                    if ($content_object_publication_category)
                    {
                        $this->set_rights_content_object_publication_category(
                            $content_object_publication_category, 
                            $course_group, 
                            $rights);
                    }
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
     * @param $course_group_group_relation_created boolean true if the CourseGroupGroupRelation is created indicating
     *        there have been more than 1 course_groups being created
     * @return ContentObjectPublicationCategory
     */
    function create_course_group_forum_category($element_value, &$course_group, $course_group_group_relation_created)
    {
        switch ($element_value)
        {
            case 1 :
                if ($course_group_group_relation_created == 1)
                {
                    $course_group_group_relation = $this->course_group_group_relation;
                    $parent_title = $course_group_group_relation->get_name();
                    $group_forum_id = $course_group_group_relation->get_forum_publication_category_id();
                }
                else 
                    if ($course_group_group_relation_created == null)
                    {
                        $group_forum_id = 0;
                    }
                
                $tool = 'forum';
                
                if ($course_group_group_relation_created == 1 && $group_forum_id == 0)
                {
                    $content_object_publication_category = $course_group_group_relation->create_course_group_category(
                        $tool);
                    $group_forum_id = $course_group_group_relation->get_forum_publication_category_id();
                }
                $course_group_forum_category = $this->create_category($course_group, $tool, $group_forum_id);
                if (is_object($course_group_forum_category))
                {
                    $course_group_forum_category_id = $course_group_forum_category->get_id();
                }
                else
                {
                    $course_group_forum_category_id = $course_group_forum_category;
                    $course_group_forum_category = DataManager :: retrieve_content_object_publication_category(
                        $course_group_forum_category_id);
                }
                if ($course_group_forum_category_id)
                {
                    $course_group->set_forum_category_id($course_group_forum_category_id);
                    // rights
                    $rights = array();
                    $rights[] = WeblcmsRights :: VIEW_RIGHT;
                    $rights[] = WeblcmsRights :: ADD_RIGHT;
                    if ($content_object_publication_category)
                    {
                        $this->set_rights_content_object_publication_category(
                            $course_group_forum_category, 
                            $course_group, 
                            $rights);
                    }
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

    /**
     * Updates the course group
     * 
     * @return boolean $result True when successful
     */
    function update_course_group()
    {
        $values = $this->exportValues();
        $course_group = $this->course_group;
        $group_id = $this->course_group->get_group_id();
        $course_id = $this->course_group->get_course_code();
        
        $i = 0;
        $course_group_group_relation_update_error = null;
        if ($group_id != null)
        {
            $this->course_group_group_relation = DataManager :: retrieve_course_group_group_relation(
                $course_group->get_group_id());
            $course_group_group_relation = $this->course_group_group_relation;
            $course_group_group_relation->set_name($values[CourseGroupGroupRelation :: PROPERTY_NAME]);
            if ($this->course_group_group_name_exists($course_group_group_relation) ||
                 $values[CourseGroupGroupRelation :: PROPERTY_NAME] == '')
            {
                $this->course_group->add_error(Translation :: get('CourseGroupGroupNotUpdated'));
                return false;
            }
            $course_group_group_relation->set_max_number_of_course_group_per_member(
                $values[CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER]);
            
            if ($course_group_group_relation->check_before_saving())
            {
                $course_group_group_relation->update();
                
                $data_set = DataManager :: retrieve_course_groups_by_course_group_group_relation_id(
                    $this->course_group->get_course_code(), 
                    $group_id);
                
                $course_group_group_relation_exist = true;
            }
            else
            {
                $this->course_group->add_error(Translation :: get('CourseGroupGroupNotUpdated'));
                return false;
            }
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME), 
                new StaticConditionVariable($this->course_group->get_name()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_COURSE_CODE), 
                new StaticConditionVariable($course_id));
            $condition = new AndCondition($conditions);
            
            $data_set = DataManager :: retrieve_course_groups($condition, null, null, null);
            $i = null;
            $course_group_group_relation_exist = false;
        }
        
        if ($this->course_group->get_errors() == null)
        {
            // group size check -> total size must not be greater than parent group's max size
            $course_groups = array();
            $parent_cgid = null;
            $total_size_diff = 0;
            while ($course_group = $data_set->next_result())
            {
                $course_groups[] = $course_group;
                $parent_cgid = $course_group->get_parent_id();
                $total_size_diff += $values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i];
                $total_size_diff -= $course_group->get_max_number_of_members();
                $i ++;
            }
            
            $parent_course_group = DataManager :: retrieve_course_group($parent_cgid);
            // existing groups size
            $total_size = $total_size_diff;
            $condition = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_PARENT_ID), 
                new StaticConditionVariable($parent_course_group->get_id()));
            $c_course_groups = DataManager :: retrieve_course_groups($condition, null, null, null);
            while ($course_group = $c_course_groups->next_result())
            {
                $total_size += $course_group->get_max_number_of_members();
            }
            
            if ($parent_course_group->get_max_number_of_members() > 0 &&
                 $total_size > $parent_course_group->get_max_number_of_members())
            {
                $this->course_group->add_error(Translation :: get('MaxMembersTooBigForParentCourseGroup'));
                return false;
            }
            $i = 0;
            foreach ($course_groups as $course_group)
            {
                $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION . $i]);
                $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i]);
                $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG . $i]);
                $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG . $i]);
                
                // if the name changes, update the corresponding document
                // category and or forum if necessary
                if ($course_group->get_name() != $values[CourseGroup :: PROPERTY_NAME . $i])
                {
                    $old_name = $course_group->get_name();
                    $course_group->set_name($values[CourseGroup :: PROPERTY_NAME . $i]);
                    if ($this->course_group_name_exists($course_group))
                    {
                        $i ++;
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
                        $document_category = DataManager :: retrieve_content_object_publication_category(
                            $document_category_id);
                        $document_category->set_name($values[CourseGroup :: PROPERTY_NAME . $i]);
                        $document_category->update();
                    }
                    $forum_category_id = $course_group->get_optional_property(CourseGroup :: PROPERTY_FORUM_CATEGORY_ID);
                    if ($forum_category_id)
                    {
                        $forum_category = DataManager :: retrieve_content_object_publication_category(
                            $forum_category_id);
                        $forum_category->set_name($values[CourseGroup :: PROPERTY_NAME . $i]);
                        $forum_category->update();
                    }
                }
                if (isset($values, $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i]))
                {
                    if ($course_group->get_document_category_id() == 0) // it
                                                                        // doesn't
                                                                        // exist
                                                                        // yet
                    {
                        $course_group_document_category = $this->create_course_group_document_category(
                            $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i], 
                            $course_group, 
                            $course_group_group_relation_exist);
                    }
                }
                else 
                    if ($course_group->get_document_category_id() != 0) // unlink
                                                                        // the
                                                                        // document
                                                                        // category
                    {
                        $document_category = DataManager :: retrieve_content_object_publication_category(
                            $course_group->get_document_category_id());
                        if ($document_category)
                        {
                            $document_category->set_allow_change(1);
                            $document_category->update();
                        }
                        $course_group->set_document_category_id(0);
                    }
                if (isset($values, $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i]))
                {
                    if ($course_group->get_forum_category_id() == 0) // it
                                                                     // doesn't
                                                                     // exist yet
                    {
                        $course_group_forum_category = $this->create_course_group_forum_category(
                            $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i], 
                            $course_group, 
                            $course_group_group_relation_exist);
                    }
                }
                else 
                    if ($course_group->get_forum_category_id() != 0) // unlink
                                                                     // the
                                                                     // document
                                                                     // category
                    {
                        $forum_category = DataManager :: retrieve_content_object_publication_category(
                            $course_group->get_forum_category_id());
                        if ($forum_category)
                        {
                            $forum_category->set_allow_change(1);
                            $forum_category->update();
                        }
                        $course_group->set_forum_category_id(0);
                    }
                if (! $course_group->update())
                {
                    return false;
                }
                
                // Change the parent
                if ($course_group->get_parent_id() != $values[CourseGroup :: PROPERTY_PARENT_ID . $i])
                {
                    if (! $course_group->move($values[CourseGroup :: PROPERTY_PARENT_ID . $i]))
                    {
                        return false;
                    }
                }
                $i ++;
            }
            return count($this->course_group->get_errors()) == 0;
        }
        else
        {
            return false;
        }
    }

    function build_basic_editing_form()
    {
        $this->build_header($this->course_group->get_name());
        $this->addElement(
            'text', 
            CourseGroup :: PROPERTY_NAME, 
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), 
            array("size" => "50"));
        
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
            'select', 
            CourseGroup :: PROPERTY_PARENT_ID, 
            Translation :: get('GroupParent'), 
            $this->get_groups());
        $this->addRule(
            CourseGroup :: PROPERTY_PARENT_ID, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', CourseGroup :: PROPERTY_DESCRIPTION, Translation :: get('Description'), 'cols="50"');
        
        $this->addRule(
            CourseGroup :: PROPERTY_PARENT_ID, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
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
    function add_tools($course_group, $i)
    {
        if ($course_group->get_document_category_id() > 0)
        {
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i, 
                Translation :: get('CreateGroupTools'), 
                Translation :: get('Document'), 
                array("value" => "1"));
        }
        else
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i, 
                Translation :: get('CreateGroupTools'), 
                Translation :: get('Document'));
        
        if ($course_group->get_forum_category_id() > 0)
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i, 
                null, 
                Translation :: get('Forum'), 
                array("value" => "1"));
        
        else
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i, 
                null, 
                Translation :: get('Forum'));
    }

    /**
     * Extends the basic editing form when the the course group is chosen to be edited which has been created in the
     * group
     */
    function build_extended_editing_form()
    {
        $group_id = $this->course_group->get_group_id();
        $i = 0;
        $this->course_group_group_relation = CourseGroupGroupRelation :: retrieve($group_id);
        $this->build_header("Group: " . $this->course_group_group_relation->get_name());
        $this->addElement(
            'text', 
            CourseGroupGroupRelation :: PROPERTY_NAME, 
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), 
            array('size' => '50'));
        $this->addElement(
            'text', 
            CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, 
            Translation :: get('MaximumGroupSubscriptionsPerMember'), 
            'size="4"');
        $this->addRule(
            CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER, 
            Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 
            'regex', 
            '/^[0-9]*$/');
        $data_set = DataManager :: retrieve_course_groups_by_course_group_group_relation_id(
            $this->course_group->get_course_code(), 
            $group_id);
        // $i++;
        
        while ($next = $data_set->next_result())
        {
            $course_groups = $next;
            $this->build_header($course_groups->get_name());
            
            $this->addElement(
                'text', 
                CourseGroup :: PROPERTY_NAME . $i, 
                Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), 
                array("size" => "50"));
            
            $this->addElement(
                'text', 
                CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i, 
                Translation :: get('MaxNumberOfMembers'), 
                'size="4"');
            $this->addRule(
                CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i, 
                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 
                'regex', 
                '/^[0-9]*$/');
            
            $this->addElement(
                'select', 
                CourseGroup :: PROPERTY_PARENT_ID . $i, 
                Translation :: get('GroupParent'), 
                $this->get_groups());
            $this->addRule(
                CourseGroup :: PROPERTY_PARENT_ID . $i, 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
            $this->addElement(
                'text', 
                CourseGroup :: PROPERTY_DESCRIPTION . $i, 
                Translation :: get('Description'), 
                'cols="100"');
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_SELF_REG . $i, 
                Translation :: get('Registration'), 
                Translation :: get('SelfRegAllowed'));
            $this->addElement(
                'checkbox', 
                CourseGroup :: PROPERTY_SELF_UNREG . $i, 
                null, 
                Translation :: get('SelfUnRegAllowed'));
            
            $this->add_tools($course_groups, $i);
            
            $this->addElement('hidden', CourseGroup :: PROPERTY_ID . $i);
            $this->addElement('hidden', CourseGroup :: PROPERTY_PARENT_ID . $i . 'old');
            $this->addElement('hidden', CourseGroup :: PROPERTY_COURSE_CODE . $i);
            
            $this->setDefaults_extended($i, $course_groups);
            $this->close_header();
            $i ++;
        }
        $this->close_header();
    }

    function add_name_field($number = null)
    {
        $element = $this->createElement(
            'text', 
            CourseGroup :: PROPERTY_NAME . $number, 
            Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), 
            array("size" => "50"));
        return $element;
    }

    function get_groups()
    {
        $course = new Course();
        $course->set_id($this->course_group->get_course_code());
        $menu = new CourseGroupMenu($course, 0);
        $renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    function build_editing_form($i)
    {
        $parent = $this->parent;
        
        if ($this->course_group->get_group_id() != null)
        {
            $this->build_extended_editing_form($i);
        }
        else
        {
            $this->build_header($this->course_group->get_name());
            $this->addElement(
                'text', 
                CourseGroup :: PROPERTY_NAME . $i, 
                Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), 
                array("size" => "50"));
            
            $this->build_basic_form($i);
            $this->close_header();
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

    /**
     * Course_group creation form
     */
    function build_creation_form()
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
                        Theme :: getInstance()->getCommonImagePath() . 'action_list_remove.png', 
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
                    Translation :: get('Title') . $numbering, 
                    '', 
                    false);
                // fill the title field automatically
                $num = $numbering - 1;
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
            Theme :: getInstance()->getCommonImagePath() . 'action_list_add.png', 
            array("title" => Translation :: get('AddGroupExplained')));
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration', true) .
                     'Resources/Javascript/course_group_form.js'));
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('CourseGroupOptions') . '</span>');
        $this->build_basic_form();
        $this->addElement('html', '</span></div>');
        
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
     * Gets the course groups registered for the current course in the format needed for a select list.
     * 
     * @author Anthony Hurst (Hogeschool Gent)
     * @return String[] Key is the course group's id. The value the display name of the course group.
     */
    public function get_existing_course_group_groups_as_select_list()
    {
        $course_group_groups = $this->get_existing_course_group_groups();
        $course_group_groups_list = array();
        foreach ($course_group_groups as $item)
        {
            $course_group_groups_list[$item->get_id()] = $item->get_name();
        }
        return $course_group_groups_list;
    }

    /**
     * Retrieves the course groups registered for the current course.
     * 
     * @author Anthony Hurst (Hogeschool Gent)
     * @return CourseGroup[] The course groups ordered by parent id and name.
     */
    public function get_existing_course_group_groups()
    {
        $course_id = $this->course_group->get_course_code();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupGroupRelation :: class_name(), 
                CourseGroupGroupRelation :: PROPERTY_COURSE_CODE), 
            new StaticConditionVariable($course_id));
        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(CourseGroupRelation :: class_name(), CourseGroupGroupRelation :: PROPERTY_NAME));
        return DataManager :: retrieve_course_group_group_relations($condition, null, null, $order_by)->as_array();
    }

    /**
     * A course_group title should be unique per course as a document and forum category names correspond to the
     * course_group name This method checks if a course group witht the same title already exists for this course
     * 
     * @param $course_group CourseGroup
     * @return boolean
     */
    function course_group_name_exists($course_group)
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
        
        // If updating, the name will already exist in the database if the name has not been changed - Exclude course
        // group being updated.
        // If creating, course group will not yet have an id.
        if ($course_group->get_id())
        {
            $not_condition = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID), 
                new StaticConditionVariable($course_group->get_id()));
            $conditions[] = new NotCondition($not_condition);
        }
        $condition = new AndCondition($conditions);
        
        $data_set = DataManager :: retrieve_course_groups($condition, null, null, null);
        
        while ($course_groups = $data_set->next_result())
        {
            return true;
        }
        return false;
    }

    /**
     * A course_group_group_relation title should be unique per course as document and forum category names correspond
     * to the course_group name This method checks if a course group with the same title already exists for this course
     * 
     * @param $course_group_group_relation CourseGroupGroupRelation
     * @return boolean
     */
    function course_group_group_name_exists($course_group_group_relation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupGroupRelation :: class_name(), 
                CourseGroupGroupRelation :: PROPERTY_NAME), 
            new StaticConditionVariable($course_group_group_relation->get_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseGroupGroupRelation :: class_name(), 
                CourseGroupGroupRelation :: PROPERTY_COURSE_CODE), 
            new StaticConditionVariable($course_group_group_relation->get_course_code()));
        
        // If updating, the name will already exist in the database if the name has not been changed - Exclude group
        // group being updated.
        // If creating, group group will not yet have an id.
        if ($course_group_group_relation->get_id())
        {
            $not_condition = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseGroupGroupRelation :: class_name(), 
                    CourseGroupGroupRelation :: PROPERTY_ID), 
                new StaticConditionVariable($course_group_group_relation->get_id()));
            $conditions[] = new NotCondition($not_condition);
        }
        $condition = new AndCondition($conditions);
        
        $data_set = DataManager :: retrieve_course_group_group_relations($condition, null, null, null);
        
        while ($course_groups = $data_set->next_result())
        {
            return true;
        }
        return false;
    }

    /**
     * This methos creates one or several course_groups for the given course If checked document and forum publications
     * are created with the same name as the course_group titel If there are several course_groups being created at the
     * same time, they are grouped in one course_group_group_relation
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
     * @return boolean
     */
    function create_course_group()
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
        
        $group_group_select = $values[self :: GROUP_GROUP_SELECTION];
        $group_group_id = null;
        $group_group_registered = false;
        switch ($group_group_select)
        {
            case self :: OPTION_GROUP_GROUP_NONE :
                $groups = $this->construct_course_groups($new_titles, $course_code, $values, $group_group_id);
                break;
            case self :: OPTION_GROUP_GROUP_EXISTING :
                $group_group_id = $values[CourseGroup :: PROPERTY_GROUP_ID];
                $groups = $this->construct_course_groups($new_titles, $course_code, $values, $group_group_id);
                $group_group_registered = true;
                break;
            case self :: OPTION_GROUP_GROUP_NEW :
                $group_group_id = $this->create_group_group(
                    $course_code, 
                    $values[CourseGroupGroupRelation :: PROPERTY_NAME], 
                    $qty, 
                    $values[CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER]);
                $group_group_registered = $group_group_id == null ? false : true;
                if ($group_group_registered)
                {
                    $groups = $this->construct_course_groups($new_titles, $course_code, $values, $group_group_id);
                }
                else
                {
                    $this->course_group->add_error(Translation :: get('CannotCreateCourseGroupGroup'));
                }
                break;
        }
        
        /*
         * if ($qty > 1 && $qty != 1) { $this->course_group_group_relation = new CourseGroupGroupRelation(array());
         * $this->course_group_group_relation->set_course_code($course_code);
         * $this->course_group_group_relation->set_name($values[CourseGroupGroupRelation :: PROPERTY_NAME]);
         * $max_subs_per_member = $values[CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER];
         * if ($max_subs_per_member > $qty) {
         * $this->course_group_group_relation->set_max_number_of_course_group_per_member($qty); } else
         * $this->course_group_group_relation->set_max_number_of_course_group_per_member($max_subs_per_member); if
         * ($this->course_group_group_relation->create()) { $course_group_group_relation_created = true; foreach
         * ($new_titles as $title) { $course_group = new CourseGroup(); $course_group->set_name($values[$title]);
         * $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
         * $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
         * $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
         * $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
         * $course_group->set_parent_id($values[CourseGroup :: PROPERTY_PARENT_ID]);
         * $course_group->set_group_id($this->course_group_group_relation->get_id());
         * $course_group->set_course_code($course_code); array_push($groups, $course_group); } } else {
         * $course_group_group_relation_created = false; $course_group->add_error(Translation ::
         * get('CannotCreateCourseGroupGroup')); } }
         */

        /*
         * When one course group is being created
         */
        /*else
            if ($qty == 1)
            {
                foreach ($new_titles as $title)
                {
                    $course_group = new CourseGroup();
                    $course_group->set_name($values[$title]);
                    $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
                    $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
                    $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
                    $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
                    $course_group->set_parent_id($values[CourseGroup :: PROPERTY_PARENT_ID]);
                    $course_group->set_course_code($course_code);
                    array_push($groups, $course_group);
                }
            }*/

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
                            $course_group_document_category = $this->create_course_group_document_category(
                                $values[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID], 
                                $course_group, 
                                $group_group_registered);
                        }
                        if (isset($values, $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID]))
                        {
                            $course_group_forum_category = $this->create_course_group_forum_category(
                                $values[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID], 
                                $course_group, 
                                $group_group_registered);
                            $tool = 'forum';
                            $content_object_publication = $this->publish_forum(
                                $tool, 
                                $course_group_forum_category->get_id(), 
                                $course_group);
                            // $course_group->set_forum_category_id($content_object_publication->get_id());
                            // //this is not the forum_category_id...
                            
                            $rights = array();
                            $rights[] = WeblcmsRights :: VIEW_RIGHT;
                            $rights[] = WeblcmsRights :: ADD_RIGHT;
                            
                            $this->set_rights_content_object_publication(
                                $content_object_publication, 
                                $course_group, 
                                $rights);
                        }
                        $course_group->update();
                        $result = true;
                    }
                    else
                    {
                        $course_group->add_error(Translation :: get('CreationFailed'));
                    }
                }
                else
                {
                    $this->course_group->add_error(Translation :: get('CourseGroupTitleExists'));
                }
            }
        }
        
        if (isset($values, $values[CourseGroup :: PROPERTY_RANDOM_REG]))
        {
            switch ($group_group_select)
            {
                case self :: OPTION_GROUP_GROUP_NONE :
                    break;
                case self :: OPTION_GROUP_GROUP_EXISTING :
                    $this->random_user_subscription_to_course_groups(
                        DataManager :: retrieve_course_group_group_relation($group_group_id));
                    break;
                case self :: OPTION_GROUP_GROUP_NEW :
                    $this->random_user_subscription_to_course_groups($this->course_group_group_relation);
                    break;
            }
            // if ($this->course_group_group_relation)
            // {
            // $this->random_user_subscription_to_course_groups($this->course_group_group_relation);
            // }
            // else
            // {
            // // random user subscription is not done when there is only one
            // // course group beeing created
            // // $this->random_user_subscription_to_course_group($course_group);
            // }
        }
        
        if ($this->course_group->get_errors())
        {
            return false;
        }
        return true;
    }

    /**
     * Creates a new CourseGroupGroupRelation.
     * 
     * @param type $course_code The course code.
     * @param type $name The name of the CourseGroupGroupRelation
     * @param type $qty The number of course groups being created.
     * @param type $max_subs_per_member The maximum number of subscriptions a user may have.
     * @return type The id of the created CoureGroupGroupRelation or null if not created.
     */
    private function create_group_group($course_code, $name, $qty, $max_subs_per_member)
    {
        $this->course_group_group_relation = new CourseGroupGroupRelation(array());
        $this->course_group_group_relation->set_course_code($course_code);
        $this->course_group_group_relation->set_name($name);
        if ($this->course_group_group_name_exists($this->course_group_group_relation) || $name == "")
        {
            return false;
        }
        if ($max_subs_per_member > $qty)
        {
            $this->course_group_group_relation->set_max_number_of_course_group_per_member($qty);
        }
        else
        {
            $this->course_group_group_relation->set_max_number_of_course_group_per_member($max_subs_per_member);
        }
        $success = $this->course_group_group_relation->create();
        return $success ? $this->course_group_group_relation->get_id() : null;
    }

    /**
     * Creates a set of new course groups based on the titles received.
     * 
     * @param string[] $new_titles The titles for which new course groups are to be created.
     * @param string $course_code The course code.
     * @param mixed[] $values The form values.
     * @param string $group_group_id The id of the CourseGroupGroupRelation that is its parent, or null if none
     *        required.
     * @return CourseGroup[] The constructed, but not created CourseGroups.
     */
    private function construct_course_groups($new_titles, $course_code, $values, $group_group_id = null)
    {
        $course_groups = array();
        foreach ($new_titles as $new_title)
        {
            $course_groups[] = $this->construct_course_group($new_title, $course_code, $values, $group_group_id);
        }
        return $course_groups;
    }

    /**
     * Creates a new CourseGroup.
     * 
     * @param string $new_title The title of the new CourseGroup.
     * @param string The course code.
     * @param mixed[] $values The form values.
     * @param string $group_group_id The id of the CourseGroupGroupRelation that is its parent or null if none required.
     * @return CourseGroup The constructed, but not created CourseGroup.
     */
    private function construct_course_group($new_title, $course_code, $values, $group_group_id = null)
    {
        $course_group = new CourseGroup();
        $course_group->set_name($values[$new_title]);
        $course_group->set_description($values[CourseGroup :: PROPERTY_DESCRIPTION]);
        $course_group->set_max_number_of_members($values[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS]);
        $course_group->set_self_registration_allowed($values[CourseGroup :: PROPERTY_SELF_REG]);
        $course_group->set_self_unregistration_allowed($values[CourseGroup :: PROPERTY_SELF_UNREG]);
        $course_group->set_parent_id($values[CourseGroup :: PROPERTY_PARENT_ID]);
        if ($group_group_id != null)
        {
            $course_group->set_group_id($group_group_id);
        }
        $course_group->set_course_code($course_code);
        return $course_group;
    }

    function add_titles()
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
        
        // for ($i=0; $){
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
        // ']', Theme :: getInstance()->getCommonImagePath() . 'action_list_remove.png',
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
    function random_user_subscription_to_course_group($course_group)
    {
        $course_code = $course_group->get_course_code();
        $max_num_members = $course_group->get_max_number_of_members();
        
        // randomize course_users
        $course_users_data_set = DataManager :: retrieve_all_course_users($course_code, null, null, null);
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
            $qty_members_to_add = $qty_course_users;
        
        $i = 0;
        
        while (sizeof($members_to_add) < $qty_members_to_add)
        {
            if ($course_users[$i])
            {
                $member = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_username(
                    $course_users[$i]->get_username());
                $members_to_add[] = $member->get_id();
                unset($course_users[$i]);
            }
            else 
                if (sizeof($course_users) != 0)
                {
                    $i ++;
                }
                else
                    break;
        }
        $course_group->subscribe_users($members_to_add);
    }
    
    /*
     * Randomly selects the users from the course users and subscribes them in the course groups within the
     * course_group_group_relation. It takes into the account the max number of members and the maximum number of
     * subscriptions allowed for a course user. This method front-loads (it will fill up spaces as soon as it gets them.
     * If there are no more users with unused subscriptions, all remaining groups will remain empty). @param
     * CourseGroupGroupRelation $course_group_group_relation The CourseGroupGroupRelation to which the random
     */
    function random_user_subscription_to_course_groups($course_group_group_relation)
    {
        // $qty_course_users = sizeof($course_users);
        
        // if ($course_group_group_relation)
        // {
        $course_users_drs = DataManager :: retrieve_all_course_users(
            $course_group_group_relation->get_course_code(), 
            null, 
            null, 
            null);
        $course_users = array();
        if ($course_users_drs)
        {
            while ($course_user = $course_users_drs->next_result())
            {
                $course_users[$course_user->get_id()] = $course_user;
            }
        }
        
        $course_groups = $course_group_group_relation->get_course_groups_by_group_id()->as_array();
        
        $max_number_subscriptions = $course_group_group_relation->get_max_number_of_course_group_per_member();
        $user_number_subscriptions = array();
        
        foreach ($course_groups as $course_group)
        {
            $subscribed_users = $course_group->get_members(true, true);
            if ($subscribed_users)
            {
                while ($subscribed_user = $subscribed_users->next_result())
                {
                    $user_number_subscriptions[$subscribed_user->get_id()] = $user_number_subscriptions[$subscribed_user->get_id()] +
                         1;
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
                while ($subscribed_user = $subscribed_users_drs->next_result())
                {
                    $subscribed_users[$subscribed_user->get_id()] = $subscribed_user;
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
                $all_groups_filled = false;
            }
        }
        return $all_groups_filled;
        // }
        // else
        // {
        // $course_code = $this->course_id;
        // $max_num_members = $course_group->get_max_number_of_members();
        // }
    }
    
    /*
     * Publish a forum for a course group and returns a new content_object_publication
     */
    function publish_forum($tool, $category_id, $course_group)
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
        $id = $content_object_publication->get_id();
        
        return $content_object_publication;
    }
    
    /*
     * Sets the the given rights to publication object
     */
    function set_rights_content_object_publication($content_object_publication, $course_group, $rights)
    {
        $context = Manager :: APPLICATION_NAME;
        
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
        $update = $location->update();
        
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
    function set_rights_content_object_publication_category($content_object_publication_category, $course_group, $rights)
    {
        $context = Manager :: APPLICATION_NAME;
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
        $update = $location->update();
        
        foreach ($rights as $right)
        {
            $weblcms_rights->set_location_entity_right($context, $right, $entity_id, $entity_type, $location_id);
        }
    }
    
    /*
     * creates a content object publication category. For example: document publication category
     */
    function create_category($course_group, $tool, $parent)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(), 
                ContentObjectPublicationCategory :: PROPERTY_NAME), 
            new StaticConditionVariable($course_group->get_name()));
        $conditions[] = new EqualityCondition(
            ContentObjectPublicationCategory :: PROPERTY_COURSE, 
            $course_group->get_course_code());
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
        
        $data_set = array();
        $data_set = DataManager :: retrieve_content_object_publication_categories($condition, null, null, null);
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
    function setDefaults($defaults = array(), $i = '')
    {
        $course_group = $this->course_group;
        $defaults[CourseGroup :: PROPERTY_NAME . $i] = $course_group->get_name();
        $defaults[CourseGroup :: PROPERTY_DESCRIPTION . $i] = $course_group->get_description();
        $defaults[self :: GROUP_GROUP_SELECTION . $i] = 0;
        if (is_null($course_group->get_max_number_of_members()))
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i] = 20;
        }
        else
        {
            $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i] = $course_group->get_max_number_of_members();
        }
        $defaults[CourseGroup :: PROPERTY_SELF_REG . $i] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup :: PROPERTY_SELF_UNREG . $i] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup :: PROPERTY_RANDOM_REG . $i] = $course_group->is_random_registration_done();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $i] = $course_group->get_parent_id();
        $defaults[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i] = $course_group->get_document_category_id();
        $defaults[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i] = $course_group->get_forum_category_id();
        parent :: setDefaults($defaults);
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param $integer.
     * @param $course_group Object CourseGroup
     */
    function setDefaults_extended($i, $course_group)
    {
        $defaults[CourseGroup :: PROPERTY_NAME . $i] = $course_group->get_name();
        $defaults[CourseGroup :: PROPERTY_ID . $i] = $course_group->get_id();
        $defaults[CourseGroup :: PROPERTY_COURSE_CODE . $i] = $course_group->get_course_code();
        $defaults[CourseGroup :: PROPERTY_DESCRIPTION . $i] = $course_group->get_description();
        $defaults[CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS . $i] = $course_group->get_max_number_of_members();
        $defaults[CourseGroup :: PROPERTY_SELF_REG . $i] = $course_group->is_self_registration_allowed();
        $defaults[CourseGroup :: PROPERTY_SELF_UNREG . $i] = $course_group->is_self_unregistration_allowed();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $i] = $course_group->get_parent_id();
        $defaults[CourseGroup :: PROPERTY_PARENT_ID . $i . 'old'] = $course_group->get_parent_id();
        $defaults[CourseGroupGroupRelation :: PROPERTY_NAME] = $this->course_group_group_relation->get_name();
        $defaults[CourseGroupGroupRelation :: PROPERTY_MAX_NUMBER_OF_COURSE_GROUP_PER_MEMBER] = $this->course_group_group_relation->get_max_number_of_course_group_per_member();
        $defaults[CourseGroup :: PROPERTY_DOCUMENT_CATEGORY_ID . $i] = $course_group->get_document_category_id();
        $defaults[CourseGroup :: PROPERTY_FORUM_CATEGORY_ID . $i] = $course_group->get_forum_category_id();
        parent :: setDefaults($defaults);
    }

    function get_errors()
    {
        return $this->course_group->get_errors();
    }
}

?>