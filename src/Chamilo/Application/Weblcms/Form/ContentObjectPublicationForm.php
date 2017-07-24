<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Publication\Publisher\Form\BasePublicationForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\FileLogger;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Mail\ValueObject\MailFile;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 * This class represents a form to allow a user to publish a learning object.
 * The form allows the user to set some
 * properties of the publication (publication dates, target users, visibility, ...)
 * 
 * @author Sven Vanpoucke
 */
class ContentObjectPublicationForm extends BasePublicationForm
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
    const PROPERTY_RIGHTS_SELECTOR = 'rights_selector';
    const RIGHTS_INHERIT = 0;
    const RIGHTS_FOR_ALL = 1;
    const RIGHTS_FOR_ME = 2;
    const RIGHTS_SELECT_SPECIFIC = 3;
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
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     * The tool context for the publication form
     * 
     * @var string
     */
    private $toolContext;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $form_type
     * @param ContentObjectPublication[] $publications
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $action
     * @param boolean $is_course_admin
     *
     * @throws NoObjectSelectedException
     */
    public function __construct($toolContext, User $user, $form_type, $publications, $course, $action, $is_course_admin, 
        $selectedContentObjects = array())
    {
        parent::__construct('content_object_publication_form', 'post', $action);
        
        if (count($publications) <= 0)
        {
            throw new NoObjectSelectedException(Translation::get('Publication'));
        }
        else
        {
            $repositoryRightsService = \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
            
            // set collaborate right for course admins if we are owner of each
            // content object to share
            $owner = true;
            foreach ($publications as $publication)
            {
                $owner &= $repositoryRightsService->isContentObjectOwner($user, $publication->get_content_object());
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
        
        $this->toolContext = $toolContext;
        $this->user = $user;
        $this->publications = $publications;
        $this->course = $course;
        $this->form_type = $form_type;
        $this->is_course_admin = $is_course_admin;
        $this->setSelectedContentObjects($selectedContentObjects);
        
        $this->entities = array();
        $this->entities[CourseUserEntity::ENTITY_TYPE] = new CourseUserEntity($course->get_id());
        $this->entities[CourseGroupEntity::ENTITY_TYPE] = new CourseGroupEntity($course->get_id());
        $this->entities[CoursePlatformGroupEntity::ENTITY_TYPE] = new CoursePlatformGroupEntity($course->get_id());
        
        switch ($form_type)
        {
            case self::TYPE_CREATE :
                $this->build_create_form();
                break;
            case self::TYPE_UPDATE :
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
        
        $defaults[ContentObjectPublication::PROPERTY_CATEGORY_ID] = Request::get(Manager::PARAM_CATEGORY);
        $defaults[self::PROPERTY_FOREVER] = 1;
        $defaults[self::PROPERTY_RIGHTS_SELECTOR] = self::RIGHTS_INHERIT;
        
        if (count($publications) == 1)
        {
            $first_publication = $publications[0];
            
            if ($first_publication->get_id())
            {
                $defaults[ContentObjectPublication::PROPERTY_CATEGORY_ID] = $first_publication->get_category_id();
                
                if ($first_publication->get_from_date() != 0)
                {
                    $defaults[self::PROPERTY_FOREVER] = 0;
                    $defaults[ContentObjectPublication::PROPERTY_FROM_DATE] = $first_publication->get_from_date();
                    $defaults[ContentObjectPublication::PROPERTY_TO_DATE] = $first_publication->get_to_date();
                }
                
                $defaults[ContentObjectPublication::PROPERTY_HIDDEN] = $first_publication->is_hidden();
                $defaults[ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE] = $first_publication->get_show_on_homepage();
            }
            
            $right_defaults = $this->set_right_defaults($first_publication);
            if (! empty($right_defaults))
            {
                $defaults = array_merge($defaults, $right_defaults);
            }
            
            $force_collaborate = Configuration::getInstance()->get_setting(
                array(Manager::package(), 'force_collaborate')) === 1 ? true : false;
            
            if ($this->collaborate_possible && ! $force_collaborate)
            {
                $collaborateDefault = LocalSetting:: getInstance()->get(
                    'collaborate_default',
                    Manager:: package()
                );

                $defaults[ContentObjectPublication :: PROPERTY_ALLOW_COLLABORATION] = $first_publication->is_identified() ?
                    $first_publication->get_allow_collaboration() : $collaborateDefault;
            }
            else
            {
                // when hide sharing is active content object is automatically shared with course admins
                $defaults[ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] = 1;
            }
        }
        
        parent::setDefaults($defaults);
    }

    /**
     * Sets the default values for the publish for rights
     * 
     * @param $publication ContentObjectPublication
     */
    public function set_right_defaults(ContentObjectPublication $publication)
    {
        $right_defaults = array();
        
        $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_PUBLICATION, 
            $publication->get_id(), 
            $publication->get_course_id());
        
        if (! $location)
        {
            return;
        }
        
        if ($location->inherits())
        {
            $right_defaults[self::PROPERTY_RIGHTS_SELECTOR] = self::RIGHTS_INHERIT;
        }
        else
        {
            $selected_entities = CourseManagementRights::retrieve_rights_location_rights_for_location(
                $location, 
                WeblcmsRights::VIEW_RIGHT)->as_array();
            
            if (count($selected_entities) == 1)
            {
                $selected_entity = $selected_entities[0];
                if ($selected_entity->get_entity_type() == 0 && $selected_entity->get_entity_id() == 0)
                {
                    $right_defaults[self::PROPERTY_RIGHTS_SELECTOR] = self::RIGHTS_FOR_ALL;
                    
                    return $right_defaults;
                }
                
                if ($selected_entity->get_entity_type() == 1 &&
                     $selected_entity->get_entity_id() == Session::get_user_id())
                {
                    $right_defaults[self::PROPERTY_RIGHTS_SELECTOR] = self::RIGHTS_FOR_ME;
                    
                    return $right_defaults;
                }
            }
            
            $right_defaults[self::PROPERTY_RIGHTS_SELECTOR] = self::RIGHTS_SELECT_SPECIFIC;
            
            $default_elements = new AdvancedElementFinderElements();
            
            foreach ($selected_entities as $selected_entity)
            {
                $entity = $this->entities[$selected_entity->get_entity_type()];
                
                $default_elements->add_element($entity->get_element_finder_element($selected_entity->get_entity_id()));
            }
            
            $element = $this->getElement(self::PROPERTY_TARGETS);
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
            self::PARAM_SUBMIT, 
            Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        
        if (count($this->publications) == 1)
        {
            $first_publication = $this->publications[0];
            $contentObject = $first_publication->get_content_object();
            
            $repositoryRightsService = \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
            $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();
            
            $canEditContentObject = $repositoryRightsService->canEditContentObject($this->user, $contentObject);
            $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
                $this->user, 
                $first_publication, 
                $this->course);
            
            if ($first_publication)
            {
                if ($contentObject instanceof ComplexContentObjectSupport && ! $first_publication->is_identified())
                {
                    if (\Chamilo\Core\Repository\Builder\Manager::exists($contentObject->package()) &&
                         ($canEditContentObject || $canEditPublicationContentObject))
                    {
                        $buttons[] = $this->createElement(
                            'style_submit_button', 
                            self::PROPERTY_PUBLISH_AND_BUILD, 
                            Translation::get('PublishAndBuild', null, Utilities::COMMON_LIBRARIES), 
                            null, 
                            null, 
                            'pencil');
                    }
                    
                    $buttons[] = $this->createElement(
                        'style_submit_button', 
                        self::PROPERTY_PUBLISH_AND_VIEW, 
                        Translation::get('PublishAndView', null, Utilities::COMMON_LIBRARIES), 
                        null, 
                        null, 
                        'search');
                }
            }
        }
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            self::PARAM_RESET, 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addCreateJavascript();
    }

    /**
     * Adds the mail check javascript
     */
    protected function addCreateJavascript()
    {
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->namespaceToFullPath(Manager::context(), true) .
                     'Resources/Javascript/ContentObjectPublicationForm.js'));
    }

    /**
     * Helper Function
     * 
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, Manager::context());
    }

    /**
     * Builds the basic create form (without buttons)
     */
    public function build_basic_create_form()
    {
        $this->build_basic_form();
        
        $this->addElement(
            'checkbox', 
            ContentObjectPublication::PROPERTY_EMAIL_SENT, 
            Translation::get('SendByEMail'), 
            null, 
            array('class' => 'send_by_email'));
        
        $this->addElement(
            'static', 
            null, 
            '', 
            '<div class="email-not-possible alert alert-info hidden">' .
                 $this->getTranslation('SendEmailNotPossibleForDelayedPublications') . '</div>');
        
        $this->addElement(
            'checkbox', 
            ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE, 
            Translation::get('ShowOnHomepage'));
    }

    /**
     * Builds the update form with the update button
     */
    public function build_update_form()
    {
        $this->build_basic_update_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            self::PARAM_SUBMIT, 
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            self::PARAM_RESET, 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the basic update form (without buttons)
     */
    public function build_basic_update_form()
    {
        $this->build_basic_form();
        
        $this->addElement(
            'checkbox', 
            ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE, 
            Translation::get('ShowOnHomepage'));
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    public function build_basic_form()
    {
        $this->addSelectedContentObjects($this->user);
        
        $tool = DataManager::retrieve_course_tool_by_name($this->get_tool());
        
        if ($this->is_course_admin || WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
            WeblcmsRights::ADD_RIGHT, 
            $tool->get_id(), 
            WeblcmsRights::TYPE_COURSE_MODULE, 
            $this->get_course_id(), 
            $this->user->getId()))
        {
            $course_title = $this->course->get_title();
            $root_title = Translation::get('TypeName', null, $this->toolContext) . ' ' . $course_title;
            
            $this->categories[0] = $root_title;
        }
        
        $this->get_categories(0);
        
        if (count($this->categories) > 1 || ! $this->categories[0])
        {
            // More than one category -> let user select one
            $this->addElement(
                'select', 
                ContentObjectPublication::PROPERTY_CATEGORY_ID, 
                Translation::get('Category', null, Utilities::COMMON_LIBRARIES), 
                $this->categories);
        }
        else
        {
            // Only root category -> store object in root category
            $this->addElement('hidden', ContentObjectPublication::PROPERTY_CATEGORY_ID, 0);
        }
        
        $this->build_rights_form();
        
        $this->add_forever_or_timewindow();
        $this->addElement(
            'checkbox', 
            ContentObjectPublication::PROPERTY_HIDDEN, 
            Translation::get('Hidden', null, Utilities::COMMON_LIBRARIES), 
            null, 
            array('class' => 'hidden_publication'));
        
        $force_collaborate = Configuration::getInstance()->get_setting(array(Manager::package(), 'force_collaborate')) ===
             1 ? true : false;
        
        // collaborate right for course admins if we are owner of each content
        // object to share
        if ($this->collaborate_possible && ! $force_collaborate)
        {
            $this->addElement(
                'checkbox', 
                ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION, 
                Translation::get('CourseAdminCollaborate'));
        }
        else
        {
            $this->addElement(
                'hidden', 
                ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION, 
                Translation::get('CourseAdminCollaborate'));
        }
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
                ContentObjectPublicationCategory::class_name(), 
                ContentObjectPublicationCategory::PROPERTY_COURSE), 
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), 
                ContentObjectPublicationCategory::PROPERTY_TOOL), 
            new StaticConditionVariable($this->get_tool()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(), 
                ContentObjectPublicationCategory::PROPERTY_PARENT), 
            new StaticConditionVariable($parent_id));
        $condition = new AndCondition($conditions);
        
        $cats = DataManager::retrieves(
            ContentObjectPublicationCategory::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        while ($cat = $cats->next_result())
        {
            if ($this->is_course_admin || WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                WeblcmsRights::ADD_RIGHT, 
                $cat->get_id(), 
                WeblcmsRights::TYPE_COURSE_CATEGORY, 
                $this->get_course_id(), 
                $this->user->getId()))
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
        $translator = Translation::getInstance();
        
        // Add the inheritance option
        $group = array();
        
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('InheritRights'), 
            self::RIGHTS_INHERIT, 
            array('class' => 'rights_selector inherit_rights_selector'));
        
        $group[] = $this->createElement(
            'button', 
            'show_inherited_rights', 
            'Show Inherited Rights', 
            array('class' => 'btn btn-info btn-inherited-rights'));
        
        $html = array();
        $html[] = '<div class="target-entities-container" data-course-id="' . $this->get_course_id() . '" data-tool="' .
             $this->get_tool() . '">';
        
        // $html[] = '<h5>' . $translator->getTranslation('EntitiesHaveViewRight', null, Manager :: context()) .
        // ':</h5>';
        $html[] = '<div class="panel panel-default target-entities-list">';
        $html[] = '<div class="panel-heading">';
        $html[] = $translator->getTranslation('Users', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = '<ul class="list-group target-entities-user-list">';
        $html[] = '<li class="list-group-item target-entities-default target-entities-nobody">';
        $html[] = $translator->getTranslation('NoUsers', null, Manager::context());
        $html[] = '</li>';
        $html[] = '<li class="list-group-item target-entities-default target-entities-everyone">';
        $html[] = $translator->getTranslation('AllUsers', null, Manager::context());
        $html[] = '</li>';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel panel-default target-entities-list">';
        $html[] = '<div class="panel-heading">';
        $html[] = $translator->getTranslation('PlatformGroups', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = '<ul class="list-group target-entities-platform-groups-list">';
        $html[] = '<li class="list-group-item target-entities-default target-entities-nobody">';
        $html[] = $translator->getTranslation('NoPlatformGroups', null, Manager::context());
        $html[] = '</li>';
        $html[] = '<li class="list-group-item target-entities-default target-entities-everyone">';
        $html[] = $translator->getTranslation('AllPlatformGroups', null, Manager::context());
        $html[] = '</li>';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel panel-default target-entities-list">';
        $html[] = '<div class="panel-heading">';
        $html[] = $translator->getTranslation('CourseGroups', null, Manager::context());
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = '<ul class="list-group target-entities-course-groups-list">';
        $html[] = '<li class="list-group-item target-entities-default target-entities-nobody">';
        $html[] = $translator->getTranslation('NoCourseGroups', null, Manager::context());
        $html[] = '</li>';
        $html[] = '<li class="list-group-item target-entities-default target-entities-everyone">';
        $html[] = $translator->getTranslation('AllCourseGroups', null, Manager::context());
        $html[] = '</li>';
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        
        $group[] = $this->createElement('static', '', '', implode(PHP_EOL, $html));
        
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('EveryoneCanView'), 
            self::RIGHTS_FOR_ALL, 
            array('class' => 'rights_selector'));
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('OnlyMeCanView'), 
            self::RIGHTS_FOR_ME, 
            array('class' => 'rights_selector'));
        
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('SelectSpecificEntitiesThatCanView'), 
            self::RIGHTS_SELECT_SPECIFIC, 
            array('class' => 'rights_selector specific_rights_selector'));
        
        $this->addElement('html', '<div class="right">');
        
        $this->addGroup(
            $group, 
            self::PROPERTY_RIGHTS_SELECTOR, 
            $translator->getTranslation('PublishFor', null, Manager::context()), 
            '');
        
        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();
        
        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }
        
        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS, null, $types);
        
        $this->addElement(
            'static', 
            '', 
            '', 
            '<div class="alert alert-info" style="margin-top: 10px;">' .
                 Translation::getInstance()->getTranslation('RightsInformationMessage', null, Manager::context()) .
                 '</div>');
        
        $this->addElement('html', '</div></div><div style="margin-bottom: 20px;"></div>');
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'RightsForm.js'));
        
        $this->addFormRule(array($this, 'validate_rights_settings'));
    }

    /**
     * Checks if the current combination of rights is allowed
     */
    public function validate_rights_settings($values)
    {
        $errors = array();
        
        if ($values[self::PROPERTY_RIGHTS_SELECTOR] == self::RIGHTS_SELECT_SPECIFIC &&
             empty($values['active_hidden_' . self::PROPERTY_TARGETS]))
        {
            $errors[self::PROPERTY_RIGHTS_SELECTOR] = Translation::get('InvalidRightsSelection');
        }
        
        if (count($errors) > 0)
        {
            return $errors;
        }
        
        return true;
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
                case self::TYPE_CREATE :
                    $succes &= $publication->create();
                    $this->set_publication_rights($publication);
                    break;
                case self::TYPE_UPDATE :
                    $succes &= $publication->update();
                    $this->set_publication_rights($publication, ($publication->get_category_id() != $old_category));
                    break;
            }
            
            // always mail publication last! we need the publication id and
            // rights created to get the targets...
            if ($this->form_type == self::TYPE_CREATE &&
                 $this->exportValue(ContentObjectPublication::PROPERTY_EMAIL_SENT))
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
        
        $category = $values[ContentObjectPublication::PROPERTY_CATEGORY_ID];
        if (! $category)
        {
            $category = 0;
        }
        
        if ($category > 0 && ! array_key_exists($category, $this->categories))
        {
            throw new UserException(Translation::get("PublicationInSelectedCategoryNotAllowed"));
        }
        
        if ($values[self::PROPERTY_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = DatetimeUtilities::time_from_datepicker($values[self::PROPERTY_FROM_DATE]);
            $to = DatetimeUtilities::time_from_datepicker($values[self::PROPERTY_TO_DATE]);
        }
        
        $publication->set_category_id($category);
        $publication->set_from_date($from);
        $publication->set_to_date($to);
        $publication->set_publication_date(time());
        $publication->set_modified_date(time());
        $publication->set_hidden($values[ContentObjectPublication::PROPERTY_HIDDEN] ? 1 : 0);
        $publication->set_show_on_homepage($values[ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
        $publication->set_allow_collaboration($values[ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] ? 1 : 0);
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
        
        $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_PUBLICATION, 
            $publication->get_id(), 
            $publication->get_course_id());
        
        if (! $location)
        {
            throw new ObjectNotExistException(Translation::get('RightsLocation'));
        }
        
        if ($category_changed)
        {
            $new_parent_id = WeblcmsRights::getInstance()->get_weblcms_location_id_by_identifier_from_courses_subtree(
                WeblcmsRights::TYPE_COURSE_CATEGORY, 
                $publication->get_category_id(), 
                $publication->get_course_id());
            $location->move($new_parent_id);
        }
        
        if (! $location->clear_right(WeblcmsRights::VIEW_RIGHT))
        {
            return false;
        }
        
        if ($values[self::PROPERTY_RIGHTS_SELECTOR] == self::RIGHTS_INHERIT)
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
            
            $option = $values[self::PROPERTY_RIGHTS_SELECTOR];
            $location_id = $location->get_id();
            
            $weblcms_rights = WeblcmsRights::getInstance();
            
            switch ($option)
            {
                case self::RIGHTS_FOR_ALL :
                    if (! $weblcms_rights->invert_location_entity_right(WeblcmsRights::VIEW_RIGHT, 0, 0, $location_id))
                    {
                        return false;
                    }
                    break;
                case self::RIGHTS_FOR_ME :
                    if (! $weblcms_rights->invert_location_entity_right(
                        WeblcmsRights::VIEW_RIGHT, 
                        Session::get_user_id(), 
                        CourseUserEntity::ENTITY_TYPE, 
                        $location_id))
                    {
                        return false;
                    }
                    break;
                case self::RIGHTS_SELECT_SPECIFIC :
                    foreach ($values[self::PROPERTY_TARGETS] as $entity_type => $target_ids)
                    {
                        foreach ($target_ids as $target_id)
                        {
                            if (! $weblcms_rights->invert_location_entity_right(
                                WeblcmsRights::VIEW_RIGHT, 
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
        
        $body = Translation::get('NewPublicationMailDescription') . ' ' . $this->course->get_title() . ' : <a href="' .
             $link . '" target="_blank">' . utf8_decode($content_object->get_title()) . '</a><br />--<br />';
        $body .= $content_object->get_description();
        $body .= '--<br />';
        $body .= $user->get_fullname() . ' - ' . $this->course->get_visual_code() . ' - ' . $this->course->get_title() .
             ' - ' . Translation::get('TypeName', null, 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool);
        
        // get targets
        $target_email = array();
        
        // Add the publisher to the email address
        $target_email[] = $user->get_email();
        
        $target_users = DataManager::get_publication_target_users($publication);
        
        foreach ($target_users as $target_user)
        {
            $target_email[] = $target_user[User::PROPERTY_EMAIL];
        }

        // safety check: filter any dubbles
        $unique_email = array_unique($target_email);
        
        $doc = new DOMDocument();
        $doc->loadHTML($body);
        $elements = $doc->getElementsByTagname('resource');
        
        $mailFiles = array();
        $index = 0;
        
        // replace image document resource tags with a html img tag with base64
        // data
        // remove all other resource tags
        foreach ($elements as $i => $element)
        {
            $type = $element->attributes->getNamedItem('type')->value;
            $id = $element->attributes->getNamedItem('source')->value;
            if ($type == self::TYPE_FILE)
            {
                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $id);
                
                if ($object->is_image())
                {
                    $mailFiles[] = new MailFile(
                        $object->get_filename(), 
                        $object->get_full_path(), 
                        $object->get_mime_type());
                    
                    $elem = $doc->createElement('img');
                    $elem->setAttribute('src', 'cid:' . $index);
                    $elem->setAttribute('alt', $object->get_filename());
                    $element->parentNode->replaceChild($elem, $element);
                    
                    $index ++;
                }
                else
                {
                    $element->parentNode->removeChild($element);
                }
            }
            else
            {
                $element->parentNode->removeChild($element);
            }
        }
        
        $body = $doc->saveHTML();
        
        if ($content_object->has_attachments())
        {
            $body .= '<br ><br >' . Translation::get('AttachmentWarning', array('LINK' => $link));
        }
        
        $log = '';
        
        $log .= "mail for publication " . $publication->get_id() . " in course ";
        $log .= $this->course->get_title();
        $log .= " to: \n";
        
        $log .= implode(', ', $unique_email);
        
        $subject = Translation::get(
            'NewPublicationMailSubject', 
            array('COURSE' => $this->course->get_title(), 'CONTENTOBJECT' => $content_object->get_title()));
        
        $mail = new Mail(
            $subject, 
            $body, 
            $unique_email, 
            true, 
            array(), 
            array(), 
            $user->get_fullname(), 
            $user->get_email(), 
            null, 
            null, 
            $mailFiles);
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        try
        {
            $mailer->sendMail($mail);
            
            $log .= " (successfull)\n";
        }
        catch (\Exception $ex)
        {
            $log .= " (unsuccessfull)\n";
        }
        
        $logMails = Configuration::getInstance()->get_setting(array(Manager::package(), 'log_mails'));
        
        if ($logMails)
        {
            $dir = Path::getInstance()->getLogPath() . 'mail';
            
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
     * Gets the tool parameter from the url
     * 
     * @return String
     */
    public function get_tool()
    {
        return Request::get(Manager::PARAM_TOOL);
    }

    /**
     * Gets the course parameter from the url
     * 
     * @return int
     */
    public function get_course_id()
    {
        return Request::get(Manager::PARAM_COURSE);
    }

    private function get_course_viewer_link($publication)
    {
        $parameters = array();
        
        $parameters[Manager::PARAM_CONTEXT] = Manager::package();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $this->course->get_id();
        $parameters[Manager::PARAM_TOOL] = $publication->get_tool();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $publication->get_id();
        
        $redirect = new Redirect($parameters);
        
        return $redirect->getUrl();
    }

    /**
     * Returns the publications
     * 
     * @return string
     */
    public function get_publications()
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
