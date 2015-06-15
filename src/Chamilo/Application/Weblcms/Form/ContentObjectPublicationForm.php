<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Mail\MailEmbeddedObject;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use DOMDocument;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Core\Repository\Workspace\Service\RightsService;

/**
 * This class represents a form to allow a user to publish a learning object.
 * The form allows the user to set some
 * properties of the publication (publication dates, target users, visibility, ...)
 *
 * @author Sven Vanpoucke
 */
class ContentObjectPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const PROPERTY_TARGETS = 'targets';
    const PROPERTY_FOREVER = 'forever';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISH_AND_BUILD = 'publish_and_build';
    const PROPERTY_PUBLISH_AND_VIEW = 'publish_and_view';

    // Rights
    const PROPERTY_INHERIT = 'inherit';
    const PROPERTY_RIGHT_OPTION = 'right_option';
    const PROPERTY_COLLABORATE = 'collaborate';
    const INHERIT_TRUE = 0;
    const INHERIT_FALSE = 1;
    const RIGHT_OPTION_ALL = 0;
    const RIGHT_OPTION_ME = 1;
    const RIGHT_OPTION_SELECT = 2;
    const TYPE_FILE = 'file';

    /**
     * The type of the form (create or edit)
     *
     * @var int
     */
    private $form_type;

    /**
     * The publications that will be created / edited
     */
    private $publications;

    /**
     * Caching of the course so that it can be used in email publication
     *
     * @var Course
     */
    private $course;

    /**
     * Available entities for the view rights
     *
     * @var Array
     */
    private $entities;

    /**
     * Is it possible to collaborate the selected objects?
     *
     * @var boolean
     */
    private $collaborate_possible;


    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $form_type
     * @param ContentObjectPublication[] $publications
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $action
     * @param boolean $is_course_admin
     * @throws NoObjectSelectedException
     */
    public function __construct(User $user, $form_type, $publications, $course, $action, $is_course_admin)
    {
        parent :: __construct('content_object_publication_form', 'post', $action);

        if (count($publications) <= 0)
        {
            throw new NoObjectSelectedException(Translation :: get('Publication'));
        }
        else
        {
            // set collaborate right for course admins if we are owner of each
            // content object to share
            $owner = true;
            foreach ($publications as $publication)
            {
                $owner_id = $publication->get_content_object()->get_owner_id();
                $owner &= ($owner_id == Session :: get_user_id());
            }
            if ($owner)
            {
                $this->collaborate_possible = true;
            }
            else
            {
                $this->collaborate_possible = false;
            }
        }

        $this->user = $user;
        $this->publications = $publications;
        $this->course = $course;
        $this->form_type = $form_type;
        $this->is_course_admin = $is_course_admin;

        $this->entities = array();
        $this->entities[CourseUserEntity :: ENTITY_TYPE] = new CourseUserEntity($course->get_id());
        $this->entities[CourseGroupEntity :: ENTITY_TYPE] = new CourseGroupEntity($course->get_id());
        $this->entities[CoursePlatformGroupEntity :: ENTITY_TYPE] = new CoursePlatformGroupEntity($course->get_id());

        switch ($form_type)
        {
            case self :: TYPE_CREATE :
                $this->build_create_form();
                break;
            case self :: TYPE_UPDATE :
                $this->build_update_form();
                break;
        }

        $this->setDefaults();
    }

    /**
     * Sets the default values of the form.
     * By default the publication is for everybody who has access to the tool and
     * the publication will be available forever.
     */
    public function setDefaults($defaults = array())
    {
        $publications = $this->publications;

        $defaults[ContentObjectPublication :: PROPERTY_CATEGORY_ID] = Request :: get(Manager :: PARAM_CATEGORY);
        $defaults[self :: PROPERTY_FOREVER] = 1;
        $defaults[self :: PROPERTY_INHERIT] = self :: INHERIT_TRUE;
        $defaults[self :: PROPERTY_RIGHT_OPTION] = self :: RIGHT_OPTION_ALL;

        if (count($publications) == 1)
        {
            $first_publication = $publications[0];

            if ($first_publication->get_id())
            {
                $defaults[ContentObjectPublication :: PROPERTY_CATEGORY_ID] = $first_publication->get_category_id();

                if ($first_publication->get_from_date() != 0)
                {
                    $defaults[self :: PROPERTY_FOREVER] = 0;
                    $defaults[ContentObjectPublication :: PROPERTY_FROM_DATE] = $first_publication->get_from_date();
                    $defaults[ContentObjectPublication :: PROPERTY_TO_DATE] = $first_publication->get_to_date();
                }

                $defaults[ContentObjectPublication :: PROPERTY_HIDDEN] = $first_publication->is_hidden();
                $defaults[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] = $first_publication->get_show_on_homepage();
            }

            $right_defaults = $this->set_right_defaults($first_publication);
            if (! empty($right_defaults))
            {
                $defaults = array_merge($defaults, $right_defaults);
            }
        }

        $force_collaborate = PlatformSetting :: get('force_collaborate', __NAMESPACE__) === 1 ? true : false;

        if ($this->collaborate_possible && ! $force_collaborate)
        {
            $defaults[self :: PROPERTY_COLLABORATE] = 0;
        }
        else
        {
            // when hide sharing is active content object is automatically shared with course admins
            $defaults[self :: PROPERTY_COLLABORATE] = 1;
        }

        parent :: setDefaults($defaults);
    }

    /**
     * Sets the default values for the publish for rights
     *
     * @param $publication ContentObjectPublication
     */
    public function set_right_defaults(ContentObjectPublication $publication)
    {
        $right_defaults = array();

        $location = WeblcmsRights :: get_instance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights :: TYPE_PUBLICATION,
            $publication->get_id(),
            $publication->get_course_id());

        if (! $location)
        {
            return;
        }

        if ($location->inherits())
        {
            $right_defaults[self :: PROPERTY_INHERIT] = self :: INHERIT_TRUE;
            $right_defaults[self :: PROPERTY_RIGHT_OPTION] = self :: RIGHT_OPTION_ALL;
        }
        else
        {
            $right_defaults[self :: PROPERTY_INHERIT] = self :: INHERIT_FALSE;

            $selected_entities = CourseManagementRights :: retrieve_rights_location_rights_for_location(
                $location,
                WeblcmsRights :: VIEW_RIGHT)->as_array();

            if (count($selected_entities) == 1)
            {
                $selected_entity = $selected_entities[0];
                if ($selected_entity->get_entity_type() == 0 && $selected_entity->get_entity_id() == 0)
                {
                    $right_defaults[self :: PROPERTY_RIGHT_OPTION] = self :: RIGHT_OPTION_ALL;
                    return $right_defaults;
                }

                if ($selected_entity->get_entity_type() == 1 &&
                     $selected_entity->get_entity_id() == Session :: get_user_id())
                {
                    $right_defaults[self :: PROPERTY_RIGHT_OPTION] = self :: RIGHT_OPTION_ME;
                    return $right_defaults;
                }
            }

            $right_defaults[self :: PROPERTY_RIGHT_OPTION] = self :: RIGHT_OPTION_SELECT;

            $default_elements = new AdvancedElementFinderElements();

            foreach ($selected_entities as $selected_entity)
            {
                $entity = $this->entities[$selected_entity->get_entity_type()];

                $default_elements->add_element($entity->get_element_finder_element($selected_entity->get_entity_id()));
            }

            $element = $this->getElement(self :: PROPERTY_TARGETS);
            $element->setDefaultValues($default_elements);
        }

        return $right_defaults;
    }

    /**
     * Builds the create form with the publish button
     */
    public function build_create_form()
    {
        $this->build_basic_create_form();

        // TODO: check email rights
        $buttons[] = $this->createElement(
            'style_submit_button',
            self :: PARAM_SUBMIT,
            Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive publish'));

        if (count($this->publications) == 1)
        {
            $first_publication = $this->publications[0];
            
            if ($first_publication && RightsService :: getInstance()->canEditContentObject(
                $this->user,
                $first_publication->get_content_object()))
            {
                $contentObject = $first_publication->get_content_object();

                if ($contentObject instanceof ComplexContentObjectSupport && ! $first_publication->is_identified())
                {
                    if (\Chamilo\Core\Repository\Builder\Manager :: exists($contentObject->package()))
                    {
                        $buttons[] = $this->createElement(
                            'style_submit_button',
                            self :: PROPERTY_PUBLISH_AND_BUILD,
                            Translation :: get('PublishAndBuild', null, Utilities :: COMMON_LIBRARIES),
                            array('class' => 'positive build'));
                    }

                    $buttons[] = $this->createElement(
                        'style_submit_button',
                        self :: PROPERTY_PUBLISH_AND_VIEW,
                        Translation :: get('PublishAndView', null, Utilities :: COMMON_LIBRARIES),
                        array('class' => 'positive preview'));
                }
            }
        }

        $buttons[] = $this->createElement(
            'style_reset_button',
            self :: PARAM_RESET,
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the basic create form (without buttons)
     */
    public function build_basic_create_form()
    {
        $this->build_basic_form();

        $this->addElement(
            'checkbox',
            ContentObjectPublication :: PROPERTY_EMAIL_SENT,
            Translation :: get('SendByEMail'));

        $force_collaborate = PlatformSetting :: get('force_collaborate', __NAMESPACE__) === 1 ? true : false;

        // collaborate right for course admins if we are owner of each content
        // object to share
        if ($this->collaborate_possible && ! $force_collaborate)
        {
            $this->addElement('checkbox', self :: PROPERTY_COLLABORATE, Translation :: get('CourseAdminCollaborate'));
        }
        else
        {
            $this->addElement('hidden', self :: PROPERTY_COLLABORATE, Translation :: get('CourseAdminCollaborate'));
        }
    }

    /**
     * Builds the update form with the update button
     */
    public function build_update_form()
    {
        $this->build_basic_update_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            self :: PARAM_SUBMIT,
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            self :: PARAM_RESET,
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the basic update form (without buttons)
     */
    public function build_basic_update_form()
    {
        $this->build_basic_form();
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    public function build_basic_form()
    {
        $tool = DataManager :: retrieve_course_tool_by_name($this->get_tool());

        if ($this->is_course_admin || WeblcmsRights :: get_instance()->is_allowed_in_courses_subtree(
            WeblcmsRights :: ADD_RIGHT,
            $tool->get_id(),
            WeblcmsRights :: TYPE_COURSE_MODULE,
            $this->get_course_id()))
        {
            $this->categories[0] = Translation :: get('Root', null, Utilities :: COMMON_LIBRARIES);
        }

        $this->get_categories(0);

        if (count($this->categories) > 1 || ! $this->categories[0])
        {
            // More than one category -> let user select one
            $this->addElement(
                'select',
                ContentObjectPublication :: PROPERTY_CATEGORY_ID,
                Translation :: get('Category', null, Utilities :: COMMON_LIBRARIES),
                $this->categories);
        }
        else
        {
            // Only root category -> store object in root category
            $this->addElement('hidden', ContentObjectPublication :: PROPERTY_CATEGORY_ID, 0);
        }

        $this->build_rights_form();

        $this->add_forever_or_timewindow();
        $this->addElement(
            'checkbox',
            ContentObjectPublication :: PROPERTY_HIDDEN,
            Translation :: get('Hidden', null, Utilities :: COMMON_LIBRARIES));

        $this->addElement(
            'checkbox',
            ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE,
            Translation :: get('ShowOnHomepage'));
    }

    private $categories;

    private $level = 1;

    /**
     * Gets the categories for the current tool and course recursively
     *
     * @param $parent_id int
     */
    public function get_categories($parent_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_TOOL),
            new StaticConditionVariable($this->get_tool()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($parent_id));
        $condition = new AndCondition($conditions);

        $cats = DataManager :: retrieves(ContentObjectPublicationCategory :: class_name(), $condition);

        while ($cat = $cats->next_result())
        {
            if ($this->is_course_admin || WeblcmsRights :: get_instance()->is_allowed_in_courses_subtree(
                WeblcmsRights :: ADD_RIGHT,
                $cat->get_id(),
                WeblcmsRights :: TYPE_COURSE_CATEGORY,
                $this->get_course_id()))
            {
                $this->categories[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
                $this->level ++;
                $this->level --;
            }

            $this->get_categories($cat->get_id());
        }
    }

    /**
     * Builds the form to set the view rights
     */
    public function build_rights_form()
    {
        // Add the inheritance option
        $group = array();

        $group[] = & $this->createElement(
            'radio',
            null,
            null,
            Translation :: get('InheritRights'),
            self :: INHERIT_TRUE,
            array('class' => 'inherit_rights_selector'));
        // $inherit_group[] = & $this->createElement('html',;

        $group[] = & $this->createElement(
            'radio',
            null,
            null,
            Translation :: get('UseSpecificRights'),
            self :: INHERIT_FALSE,
            array('class' => 'specific_rights_selector'));

        $this->addGroup(
            $group,
            self :: PROPERTY_INHERIT,
            Translation :: get('PublishFor', null, Utilities :: COMMON_LIBRARIES),
            '<br />');

        $this->addElement('html', '<div class="right">');

        // Add the rights options
        $group = array();

        $group[] = & $this->createElement(
            'radio',
            null,
            null,
            Translation :: get('Everyone'),
            self :: RIGHT_OPTION_ALL,
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio',
            null,
            null,
            Translation :: get('OnlyForMe'),
            self :: RIGHT_OPTION_ME,
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio',
            null,
            null,
            Translation :: get('SelectSpecificEntities'),
            self :: RIGHT_OPTION_SELECT,
            array('class' => 'entity_option_selected'));

        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="specific_rights_selector_box">');
        $this->addGroup($group, self :: PROPERTY_RIGHT_OPTION, '', '<br />');

        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }

        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', self :: PROPERTY_TARGETS, null, $types);

        $this->addElement('html', '</div></div></div>');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'RightsForm.js'));
    }

    /**
     * Handles the submit of the form for both create and edit
     *
     * @return boolean
     */
    public function handle_form_submit()
    {
        
        $publications = $this->publications;
        $succes = true;
      
        foreach ($publications as $publication)
        {
            $old_category = $publication->get_category_id();
            $this->set_publication_values($publication);

            switch ($this->form_type)
            {
                case self :: TYPE_CREATE :
                    $succes &= $publication->create();
                    $this->set_publication_rights($publication);
                    break;
                case self :: TYPE_UPDATE :
                    $succes &= $publication->update();
                    $this->set_publication_rights($publication, ($publication->get_category_id() != $old_category));
                    break;
            }

            // add collabate right for course admins
            if ($this->exportValue(self :: PROPERTY_COLLABORATE))
            {
                $this->collaborate_content_object_with_course_admins($publication->get_content_object());
            }

            // always mail publication last! we need the publication id and
            // rights created to get the targets...
            if ($this->form_type == self :: TYPE_CREATE &&
                 $this->exportValue(ContentObjectPublication :: PROPERTY_EMAIL_SENT))
            {
                $this->mail_publication($publication);
            }
        }
        return $succes;
    }

    /**
     * Sets the values for the content object publication
     *
     * @param $publication ContentObjectPublication
     */
    public function set_publication_values(ContentObjectPublication $publication)
    {
        $values = $this->exportValues();

        $category = $values[ContentObjectPublication :: PROPERTY_CATEGORY_ID];
        if (! $category)
        {
            $category = 0;
        }

        if ($category > 0 && ! array_key_exists($category, $this->categories))
        {
            throw new \Exception(Translation :: get("PublicationInSelectedCategoryNotAllowed"));
        }

        if ($values[self :: PROPERTY_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = DatetimeUtilities :: time_from_datepicker($values[self :: PROPERTY_FROM_DATE]);
            $to = DatetimeUtilities :: time_from_datepicker($values[self :: PROPERTY_TO_DATE]);
        }

        $publication->set_category_id($category);
        $publication->set_from_date($from);
        $publication->set_to_date($to);
        $publication->set_publication_date(time());
        $publication->set_modified_date(time());
        $publication->set_hidden($values[ContentObjectPublication :: PROPERTY_HIDDEN] ? 1 : 0);
        $publication->set_show_on_homepage($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
    }

    /**
     * Sets the rights for the given content object publication
     *
     * @param $publication ContentObjectPublication
     * @param $category_changed boolean
     */
    public function set_publication_rights(ContentObjectPublication $publication, $category_changed = false)
    {
        $values = $this->exportValues();

        $location = WeblcmsRights :: get_instance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights :: TYPE_PUBLICATION,
            $publication->get_id(),
            $publication->get_course_id());

        if (! $location)
        {
            throw new ObjectNotExistException(Translation :: get('RightsLocation'));
        }

        if ($category_changed)
        {
            $new_parent_id = WeblcmsRights :: get_instance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                WeblcmsRights :: TYPE_COURSE_CATEGORY,
                $publication->get_category_id(),
                $publication->get_course_id());
            $location->move($new_parent_id);
        }

        if (! $location->clear_right(WeblcmsRights :: VIEW_RIGHT))
        {
            return false;
        }

        if ($values[self :: PROPERTY_INHERIT] == self :: INHERIT_TRUE)
        {
            if (! $location->inherits())
            {
                $location->inherit();
                if (! $location->update())
                {
                    return false;
                }
            }
        }
        else
        {
            if ($location->inherits())
            {
                $location->disinherit();
                if (! $location->update())
                {
                    return false;
                }
            }

            $option = $values[self :: PROPERTY_RIGHT_OPTION];
            $location_id = $location->get_id();

            $weblcms_rights = WeblcmsRights :: get_instance();

            switch ($option)
            {
                case self :: RIGHT_OPTION_ALL :
                    if (! $weblcms_rights->invert_location_entity_right(WeblcmsRights :: VIEW_RIGHT, 0, 0, $location_id))
                    {
                        return false;
                    }
                    break;
                case self :: RIGHT_OPTION_ME :
                    if (! $weblcms_rights->invert_location_entity_right(
                        WeblcmsRights :: VIEW_RIGHT,
                        Session :: get_user_id(),
                        CourseUserEntity :: ENTITY_TYPE,
                        $location_id))
                    {
                        return false;
                    }
                    break;
                case self :: RIGHT_OPTION_SELECT :
                    foreach ($values[self :: PROPERTY_TARGETS] as $entity_type => $target_ids)
                    {
                        foreach ($target_ids as $target_id)
                        {
                            if (! $weblcms_rights->invert_location_entity_right(
                                WeblcmsRights :: VIEW_RIGHT,
                                $target_id,
                                $entity_type,
                                $location_id))
                            {
                                return false;
                            }
                        }
                    }
            }
        }
    }

    /**
     * Sets collaboration right for course admins for the given publication
     *
     * @param $publication ContentObjectPublication
     */
    public function collaborate_content_object_with_course_admins(ContentObject $content_object)
    {
        $succes = false;

        if ($content_object && ($content_object->get_owner_id() == Session :: get_user_id()))
        {
            $succes = true;

            // prepare data
            $admin_users = $this->course->get_course_admin_users()->as_array();
            $admin_groups = $this->course->get_course_admin_groups()->as_array();

            // loop users
            foreach ($admin_users as $admin_user)
            {
                // exclude myself
                if ($admin_user[User :: PROPERTY_ID] != Session :: get_user_id())
                {
                    // TODO: WORKSPACES - Share with the user via a workspace?
                    $succes = true;
                }
            }

            // loop groups
            foreach ($admin_groups as $admin_group)
            {
                // TODO: WORKSPACES - Share with the group via a workspace?
                $succes = true;
            }
        }

        return $succes;
    }

    /**
     * Mails the given publication to the target users
     *
     * @param $publication ContentObjectPublication
     */
    public function mail_publication(ContentObjectPublication $publication)
    {
        set_time_limit(3600);

        // prepare mail
        $user = $publication->get_publication_publisher();
        $content_object = $publication->get_content_object();
        $tool = $publication->get_tool();
        $link = $this->get_course_viewer_link($publication);

        $body = Translation :: get('NewPublicationMailDescription') . ' ' . $this->course->get_title() . ' : <a href="' .
             $link . '" target="_blank">' . utf8_decode($content_object->get_title()) . '</a><br />--<br />';
        $body .= $content_object->get_description();
        $body .= '--<br />';
        $body .= $user->get_fullname() . ' - ' . $this->course->get_visual_code() . ' - ' . $this->course->get_title() .
             ' - ' . Translation :: get('TypeName', null, 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool);

        // get targets
        $target_email = array();

        // Add the publisher to the email address
        $target_email[] = $user->get_email();

        $target_users = DataManager :: get_publication_target_users($publication);

        $target_users = $this->filter_out_excluded_email_recipients($target_users);

        foreach ($target_users as $target_user)
        {
            $target_email[] = $target_user[User :: PROPERTY_EMAIL];
        }

        // safety check: filter any dubbles
        $unique_email = array_unique($target_email);

        $site_name = PlatformSetting :: get('site_name');

        $mail = Mail :: factory(
            '[' . $site_name . '] ' . Translation :: get(
                'NewPublicationMailSubject',
                array('COURSE' => $this->course->get_title(), 'CONTENTOBJECT' => $content_object->get_title())),
            '',
            '',
            array(Mail :: NAME => $user->get_fullname(), Mail :: EMAIL => $user->get_email()));

        $doc = new DOMDocument();
        $doc->loadHTML($body);
        $elements = $doc->getElementsByTagname('resource');

        // replace image document resource tags with a html img tag with base64
        // data
        // remove all other resource tags
        foreach ($elements as $i => $element)
        {
            $type = $element->attributes->getNamedItem('type')->value;
            $id = $element->attributes->getNamedItem('source')->value;
            if ($type == self :: TYPE_FILE)
            {
                $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $id);

                if ($object->is_image())
                {
                    $mail_embedded_object = new MailEmbeddedObject(
                        $object->get_filename(),
                        $object->get_mime_type(),
                        $object->get_full_path());

                    $index = $mail->add_embedded_image($mail_embedded_object);

                    $elem = $doc->createElement('img');
                    $elem->setAttribute('src', 'cid:' . $index);
                    $elem->setAttribute('alt', $object->get_filename());
                    $element->parentNode->replaceChild($elem, $element);
                }
                else
                    $element->parentNode->removeChild($element);
            }
            else
                $element->parentNode->removeChild($element);
        }

        $body = $doc->saveHTML();

        if ($content_object->has_attachments())
        {
            $body .= '<br ><br >' . Translation :: get('AttachmentWarning', array('LINK' => $link));
        }

        $mail->set_message($body);

        $log .= "mail for publication " . $publication->get_id() . " in course ";
        $log .= $this->course->get_title();
        $log .= " to: \n";

        // send mail
        foreach ($unique_email as $mail_to)
        {

            $mail->set_to($mail_to);
            $success = $mail->send();

            $log .= $mail_to;
            if ($success)
            {
                $log .= " (successfull)\n";
            }
            else
            {
                $log .= " (unsuccessfull)\n";
            }
        }

        if (PlatformSetting :: get('log_mails', __NAMESPACE__))
        {
            $dir = Path :: getInstance()->getLogPath() . 'mail';

            if (! file_exists($dir) and ! is_dir($dir))
            {
                mkdir($dir);
            }

            $today = date("Ymd", mktime());
            $logfile = $dir . '//' . "mails_sent_$today" . ".log";
            $mail_log = new FileLogger($logfile, true);
            $mail_log->log_message($log, true);
        }

        $publication->set_email_sent(true);
        $publication->update();
    }

    /**
     * filters out target users using hook function in other registered apps implement [application]Manager ::
     * weblcms_exclude_email_recipients($target_users) in any application to apply filtering from context
     *
     * @param array $target_users containing User objects
     * @return array containing User objects
     */
    function filter_out_excluded_email_recipients(array $target_users)
    {
        // retrieve all applications
        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_registrations();

        foreach ($registrations[\Chamilo\Configuration\Storage\DataManager :: REGISTRATION_TYPE] as $type)
        {
            foreach ($type as $registration)
            {
                if ($registration->is_active())
                {
                    // see if app has method implemented
                    $classname = $registration->get_context() . '\\' . $registration->get_name() . 'Manager';

                    if (class_exists($classname))
                    {
                        $method_name = 'weblcms_filter_out_excluded__email_recipients';
                        if (method_exists($classname, $method_name))
                        {
                            // filter out users
                            $target_users = $classname :: $method_name($target_users);
                        }
                    }
                }
            }
        }
        return $target_users;
    }

    /**
     * Gets the tool parameter from the url
     *
     * @return String
     */
    public function get_tool()
    {
        return Request :: get(Manager :: PARAM_TOOL);
    }

    /**
     * Gets the course parameter from the url
     *
     * @return int
     */
    public function get_course_id()
    {
        return Request :: get(Manager :: PARAM_COURSE);
    }

    private function get_course_viewer_link($publication)
    {
        $parameters = array();

        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_COURSE;
        $parameters[Manager :: PARAM_COURSE] = $this->course->get_id();
        $parameters[Manager :: PARAM_TOOL] = $publication->get_tool();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $publication->get_id();

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    /**
     * Returns the publications
     *
     * @return string
     */
    protected function get_publications()
    {
        return $this->publications;
    }

    /**
     * Returns the form type
     *
     * @return int string
     */
    protected function get_form_type()
    {
        return $this->form_type;
    }
}
