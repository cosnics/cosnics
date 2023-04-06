<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Form\CourseSettingsXmlFormParser\CourseSettingsXmlFormParser;
use Chamilo\Application\Weblcms\Interfaces\CourseSettingsXmlFormParserSupport;
use Chamilo\Application\Weblcms\Interfaces\FormLockedSettingsSupport;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\DynamicContentMenu\DynamicContentMenuItem;
use Chamilo\Libraries\Format\Menu\DynamicContentMenu\FormDynamicContentMenu;
use Chamilo\Libraries\Format\Menu\DynamicContentMenu\FormDynamicContentMenuItem;
use Chamilo\Libraries\Format\Structure\IdentRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes a form for the course object
 * 
 * @package \application\weblcms
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class CommonCourseForm extends FormValidator implements CourseSettingsXmlFormParserSupport
{
    const PROPERTY_LOCKED_PREFIX = 'locked_';

    /**
     * The name for this form (used for tabs / menus)
     * 
     * @var String
     */
    private $form_name;

    /**
     * The base object (course / course type) for this form
     * 
     * @var DataClass
     */
    private $base_object;

    /**
     * Caching of the entities for default values
     * 
     * @var RightsEntity[]
     */
    private $entities;
    
    /**
     * ***************************************************************************************************************
     * Tabs *
     * **************************************************************************************************************
     */
    const TAB_GENERAL = 'general';
    const TAB_SETTINGS = 'settings';
    const TAB_TOOLS = 'tools';
    const TAB_RIGHTS = 'rights';

    /**
     * ***************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param String $action
     * @param DataClass $base_object
     */
    public function __construct($action, DataClass $base_object)
    {
        $this->form_name = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($this), true);
        $this->base_object = $base_object;
        
        parent::__construct($this->form_name, self::FORM_METHOD_POST, $action);
        
        $this->create_tabs();
        
        $buttons = array();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', array(), Utilities::COMMON_LIBRARIES));
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', array(), Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->set_default_values();
    }

    /**
     * ***************************************************************************************************************
     * Defaults Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Sets the default values If the selected base object identified than we use the values for that base object
     * 
     * @param string[] $default_values
     */
    public function set_default_values()
    {
        $this->setDefaults($this->get_base_object_default_values($this->base_object));
        $this->set_default_values_for_rights_form();
        
        $this->setDefaults(
            $this->multi_dimensional_array_to_single_dimensional_array(
                CourseSettingsController::getInstance()->convert_course_settings_to_values_array($this->base_object)));
    }

    /**
     * Sets the default values for the rights form (based on the simple rights form)
     */
    public function set_default_values_for_rights_form()
    {
        $defaults = [];

        $available_rights = $this->get_available_rights();
        
        if ($this->base_object->is_identified())
        {
            $location = $this->base_object->get_rights_location();
        }
        else
        {
            $location = $this->base_object->get_parent_rights_location();
        }
        
        if (! $location)
        {
            return;
        }
        
        $selected_entities = CourseManagementRights::getInstance()->retrieve_rights_location_rights_for_location(
            $location, 
            $available_rights);
        
        $selected_entities_per_right = array();
        while ($selected_entity = $selected_entities->next_result())
        {
            $selected_entities_per_right[$selected_entity->get_right_id()][] = $selected_entity;
        }
        
        foreach ($available_rights as $right_id)
        {
            $option_name = CourseManagementRights::PARAM_RIGHT_OPTION . '[' . $right_id . ']';
            
            if (count($selected_entities_per_right[$right_id]) == 0)
            {
                $defaults[$option_name] = CourseManagementRights::RIGHT_OPTION_NOBODY;
                continue;
            }
            
            if (count($selected_entities_per_right[$right_id]) == 1)
            {
                $selected_entity = $selected_entities_per_right[$right_id][0];
                
                if ($selected_entity->get_entity_type() == 0 && $selected_entity->get_entity_id() == 0)
                {
                    $defaults[$option_name] = CourseManagementRights::RIGHT_OPTION_ALL;
                    continue;
                }
                
                if ($selected_entity->get_entity_type() == 1 && $selected_entity->get_entity_id() ==
                     Session::get_user_id())
                {
                    $defaults[$option_name] = CourseManagementRights::RIGHT_OTPION_ME;
                    continue;
                }
            }
            
            $targets_name = CourseManagementRights::PARAM_RIGHT_TARGETS . '[' . $right_id . ']';
            
            $defaults[$option_name] = CourseManagementRights::RIGHT_OPTION_SELECT;
            
            $default_elements = new AdvancedElementFinderElements();
            
            foreach ($selected_entities_per_right[$right_id] as $selected_entity)
            {
                $entity = $this->entities[$selected_entity->get_entity_type()];
                if ($entity)
                {
                    $elementFinderElement = $entity->get_element_finder_element($selected_entity->get_entity_id());
                    
                    if ($elementFinderElement instanceof AdvancedElementFinderElement)
                    {
                        $default_elements->add_element($elementFinderElement);
                    }
                }
            }
            
            $element = $this->getElement($targets_name);
            $element->setDefaultValues($default_elements);
        }

        $this->setDefaults($defaults);
    }

    /**
     * **************************************************************************************************************
     * Tabs Building Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Creates the dynamic tabs for the form
     * 
     * @param String $form_name
     */
    private function create_tabs()
    {
        $tabs_renderer = new DynamicFormTabsRenderer($this->form_name, $this);
        
        $tabs_renderer->add_tab(
            new DynamicFormTab(
                self::TAB_GENERAL, 
                (string) StringUtilities::getInstance()->createString(self::TAB_GENERAL)->upperCamelize(), 
                null, 
                'build_general_tab_form_elements'));
        
        $tabs_renderer->add_tab(
            new DynamicFormTab(
                self::TAB_SETTINGS, 
                (string) StringUtilities::getInstance()->createString(self::TAB_SETTINGS)->upperCamelize(), 
                null, 
                'build_settings_tab_form_elements'));
        
        $tabs_renderer->add_tab(
            new DynamicFormTab(
                self::TAB_TOOLS, 
                (string) StringUtilities::getInstance()->createString(self::TAB_TOOLS)->upperCamelize(), 
                null, 
                'build_tools_tab_form_elements'));
        
        $tabs_renderer->add_tab(
            new DynamicFormTab(
                self::TAB_RIGHTS, 
                (string) StringUtilities::getInstance()->createString(self::TAB_RIGHTS)->upperCamelize(), 
                null, 
                'build_rights_tab_form_elements'));
        
        $tabs_renderer->render();
    }

    /**
     * **************************************************************************************************************
     * Tabs Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Builds the elements for the settings tab
     */
    public function build_settings_tab_form_elements()
    {
        $this->add_settings_from_xml(
            Path::getInstance()->namespaceToFullPath('Chamilo\Application\Weblcms') .
                 join(DIRECTORY_SEPARATOR, array('Resources', 'Settings', 'course_settings.xml')), 
                Manager::context(), 
                new \Chamilo\Application\Weblcms\CourseSettingsConnector(), 
                CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS);
    }

    /**
     * Builds the elements for the tools tab
     */
    public function build_tools_tab_form_elements()
    {
        $dynamic_content_menu = new FormDynamicContentMenu($this->form_name);
        
        $dynamic_content_menu->add_menu_item(
            new FormDynamicContentMenuItem(
                self::TAB_GENERAL, 
                (string) StringUtilities::getInstance()->createString(self::TAB_GENERAL)->upperCamelize(), 
                null, 
                'build_tools_tab_general_content_form_elements', 
                true));
        
        $settings_conditions = array();
        
        $settings_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_GLOBAL_SETTING), 
            new StaticConditionVariable(0));
        
        $settings_conditions[] = new InCondition(
            new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_NAME), 
            CourseSetting::get_static_tool_settings());
        
        $settings_condition = new NotCondition(new AndCondition($settings_conditions));
        
        $tools_condition = new SubselectCondition(
            new PropertyConditionVariable(CourseTool::class_name(), CourseTool::PROPERTY_ID), 
            new PropertyConditionVariable(CourseSetting::class_name(), CourseSetting::PROPERTY_TOOL_ID), 
            CourseSetting::get_table_name(), 
            $settings_condition);
        
        $tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters($tools_condition));
        
        $toolsArray = $tools->as_array();
        usort(
            $toolsArray, 
            function ($toolA, $toolB)
            {
                $toolANamespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($toolA->get_name());
                $toolBNamespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($toolB->get_name());
                
                $toolATranslation = Translation::getInstance()->getTranslation('TypeName', null, $toolANamespace);
                $toolBTranslation = Translation::getInstance()->getTranslation('TypeName', null, $toolBNamespace);
                
                return strcmp($toolATranslation, $toolBTranslation);
            });
        
        foreach ($toolsArray as $tool)
        {
            $tool_namespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool->get_name());
            $tool_title = Translation::get('TypeName', null, $tool_namespace);
            
            $dynamic_content_menu->add_menu_item(
                new FormDynamicContentMenuItem(
                    $tool->get_name() . '-' . $tool->get_id(), 
                    $tool_title, 
                    null, 
                    'build_tools_tab_detail_content_form_elements'));
        }
        
        $dynamic_content_menu->add_to_form($this);
    }

    /**
     * Builds the elements for the rights tab
     */
    public function build_rights_tab_form_elements()
    {
        $available_rights = $this->get_available_rights();
        
        $this->addElement('html', '<div class="specific_rights_selector_box">');
        
        $rights_form_added = false;
        
        foreach ($available_rights as $right_name => $right_id)
        {
            if ($this->base_object->can_change_course_management_right($right_id))
            {
                $this->build_right_form($right_name, $right_id);
                $rights_form_added = true;
            }
        }
        
        if (! $rights_form_added)
        {
            $this->addElement('html', '<div class="normal-message">' . Translation::get('NoRightsAvailable') . '</div>');
        }
        
        $this->addElement('html', '</div>');
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'RightsForm.js'));
    }

    /**
     * **************************************************************************************************************
     * Tool Tab - Menu Content Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Builds the elements for the tools tab - general menu item
     * 
     * @param DynamicContentMenuItem $menu_item
     */
    public function build_tools_tab_general_content_form_elements(DynamicContentMenuItem $menu_item)
    {
        $locked_settings_supported = ($this instanceof FormLockedSettingsSupport);
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-responsive">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="column_icon"></th>';
        $table_header[] = '<th>' . Translation::get('Tool') . '</th>';
        $table_header[] = '<th class="tools_column_toggle">' . Translation::get('IsToolAvailable') . '</th>';
        
        if ($locked_settings_supported)
        {
            $table_header[] = '<th class="tools_column_toggle">' . Translation::get('ToolAvailableLocked') . '</th>';
        }
        
        $table_header[] = '<th class="tools_column_toggle">' . Translation::get('IsToolVisible') . '</th>';
        
        if ($locked_settings_supported)
        {
            $table_header[] = '<th class="tools_column_toggle">' . Translation::get('ToolVisibleLocked') . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        $tools_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(CourseTool::class_name(), CourseTool::PROPERTY_SECTION_TYPE), 
                new StaticConditionVariable(CourseSection::TYPE_CUSTOM)));
        
        $tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters($tools_condition));
        
        $toolsArray = $tools->as_array();
        usort(
            $toolsArray, 
            function ($toolA, $toolB)
            {
                $toolATranslation = Translation::getInstance()->getTranslation('TypeName', null, $toolA->getContext());
                $toolBTranslation = Translation::getInstance()->getTranslation('TypeName', null, $toolB->getContext());
                
                return strcmp($toolATranslation, $toolBTranslation);
            });
        
        foreach ($toolsArray as $tool)
        {
            $tool_name = $tool->get_name();
            
            // Filter out the course settings so this can not be disabled (would cause a deadlock otherwise)
            if ($tool_name == 'CourseSettings')
            {
                continue;
            }
            
            $tool_namespace = $tool->getContext();
            $tool_title = Translation::get('TypeName', null, $tool_namespace);
            $tool_image_src = Theme::getInstance()->getImagePath($tool_namespace, 'Logo/' . Theme::ICON_MINI);
            $tool_image = $tool_name . "_image";
            
            $table_body = array();
            $table_body[] = '<tr>';
            $table_body[] = '<td class="column_icon">';
            $identRenderer = new IdentRenderer($tool_namespace, false, false, IdentRenderer::SIZE_SM);
            $table_body[] = $identRenderer->render();
            $table_body[] = '</td>';
            
            $table_body[] = '<td class="column_tool_title">' . $tool_title . '</td>';
            
            $this->addElement('html', implode(PHP_EOL, $table_body));
            
            $tool_element_name = CourseSettingsController::SETTING_PARAM_TOOL_SETTINGS . '[' . $tool_name . ']';
            $active_element_name = $tool_element_name . '[' . CourseSetting::COURSE_SETTING_TOOL_ACTIVE . ']';
            $visible_element_name = $tool_element_name . '[' . CourseSetting::COURSE_SETTING_TOOL_VISIBLE . ']';
            $locked_prefix = CourseSettingsController::SETTING_PARAM_LOCKED_PREFIX;
            
            $this->addElement('html', '<td class="tools_column_toggle">');
            $active_element = $this->addElement(
                'toggle', 
                $active_element_name, 
                Translation::get('Active'), 
                '', 
                null, 
                '1', 
                '0');
            
            if (! $this->can_change_course_setting(CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $tool->get_id()))
            {
                $active_element->freeze();
            }
            
            $this->addElement('html', '</div></td>');
            
            if ($locked_settings_supported)
            {
                $this->addElement('html', '<td class="tools_column_toggle">');
                $this->addElement('checkbox', $locked_prefix . $active_element_name, '', '', array(), '1', '0');
                $this->addElement('html', '</div></td>');
            }
            
            $this->addElement('html', '<td class="tools_column_toggle">');
            $visible_element = $this->addElement(
                'toggle', 
                $visible_element_name, 
                Translation::get('Visible'), 
                '', 
                null, 
                '1', 
                '0');
            
            if (! $this->can_change_course_setting(CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()))
            {
                $visible_element->freeze();
            }
            
            $this->addElement('html', '</div></td>');
            
            if ($locked_settings_supported)
            {
                $this->addElement('html', '<td class="tools_column_toggle">');
                $this->addElement('checkbox', $locked_prefix . $visible_element_name, '', '', array(), '1', '0');
                $this->addElement('html', '</div></td>');
            }
            
            $this->addElement('html', '</tr>');
            
            $this->get_renderer()->setElementTemplate('{element}', $active_element_name);
            $this->get_renderer()->setElementTemplate('{element}', $visible_element_name);
        }
        
        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        
        $this->addElement('html', implode(PHP_EOL, $table_footer));
    }

    /**
     * Builds the elements for the tools tab - specific menu item (settings form for tool)
     * 
     * @param DynamicContentMenuItem $menu_item
     */
    public function build_tools_tab_detail_content_form_elements(DynamicContentMenuItem $menu_item)
    {
        $menu_item_id = $menu_item->get_id();
        
        $menu_item_id = explode('-', $menu_item_id);
        $tool = $menu_item_id[0];
        $tool_id = $menu_item_id[1];
        
        $tool_namespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool);
        $tool_path = Path::getInstance()->namespaceToFullPath($tool_namespace);
        $settings_xml_path = $tool_path . 'Resources/Settings/course_settings.xml';
        
        $this->add_settings_from_xml(
            $settings_xml_path, 
            $tool_namespace, 
            null, 
            CourseSettingsController::SETTING_PARAM_TOOL_SETTINGS . '[' . $tool . ']', 
            $tool_id);
    }

    /**
     * **************************************************************************************************************
     * Single Right Form Builder *
     * **************************************************************************************************************
     */
    
    /**
     * Builds the form for a given right
     * 
     * @param String $right_name
     * @param int $right_id
     */
    private function build_right_form($right_name, $right_id)
    {
        $name = CourseManagementRights::PARAM_RIGHT_OPTION . '[' . $right_id . ']';
        $targets_name = CourseManagementRights::PARAM_RIGHT_TARGETS . '[' . $right_id . ']';
        $locked_name = CourseManagementRights::PARAM_RIGHT_LOCKED . '[' . $right_id . ']';
        
        $this->addElement('category', $right_name);
        $this->addElement('html', '<div class="right">');
        
        $group = array();
        
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('Nobody'), 
            CourseManagementRights::RIGHT_OPTION_NOBODY, 
            array('class' => 'rights_selector'));
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('Everyone'), 
            CourseManagementRights::RIGHT_OPTION_ALL, 
            array('class' => 'rights_selector'));
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('OnlyForMe'), 
            CourseManagementRights::RIGHT_OTPION_ME, 
            array('class' => 'rights_selector'));
        $group[] = $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('SelectSpecificEntities'), 
            CourseManagementRights::RIGHT_OPTION_SELECT, 
            array('class' => 'rights_selector specific_rights_selector'));
        
        $this->addGroup($group, $name, Translation::get('Target'), '');
        
        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();
        
        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();
        
        foreach ($entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }
        
        $this->entities = $entities;
        
        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', $targets_name, null, $types);
        
        $this->addElement('html', '</div></div>');
        
        $defaults = array();
        $defaults[$name] = CourseManagementRights::RIGHT_OPTION_NOBODY;
        
        if ($this instanceof FormLockedSettingsSupport)
        {
            $this->addElement('checkbox', $locked_name, Translation::get('Locked'));
            
            if (CourseManagementRights::getInstance()->is_right_locked_for_base_object($this->base_object, $right_id))
            {
                $defaults[$locked_name] = 1;
            }
        }
        
        $this->addElement('category');
        
        $this->setDefaults($defaults);
    }

    /**
     * **************************************************************************************************************
     * Form Generation Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Checks whether or not the base object can change a course setting
     * 
     * @param string $name
     * @param int $tool_id
     *
     * @return boolean
     */
    public function can_change_course_setting($name, $tool_id = 0)
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        
        $course_setting = $course_settings_controller->get_course_setting_object_from_name_and_tool($name, $tool_id);
        
        if (! $course_setting)
        {
            return false;
        }
        
        return $this->base_object->can_change_course_setting($course_setting);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds xml defined settings to the form
     * 
     * @param String $xml_file_path
     * @param String $context - [OPTIONAL] The context - Default common\libraries
     * @param Object $connector_class - [OPTIONAL] The connector class to retrieve the dynamic options
     * @param String $prefix - [OPTIONAL] The prefix for the elements
     */
    protected function add_settings_from_xml($xml_file_path, $context = null, $connector_class = null, $prefix = null, $tool_id = 0)
    {
        $xml_form_parser = new CourseSettingsXmlFormParser($this, $tool_id);
        $xml_form_parser_result = $xml_form_parser->build_elements($xml_file_path, $context, $connector_class, $prefix);
        $xml_form_parser_result->add_result_to_form($this);
    }

    /**
     * Returns the available rights for this form
     * 
     * @return string[]
     */
    protected function get_available_rights()
    {
        return $this->base_object->get_available_course_management_rights();
    }

    /**
     * **************************************************************************************************************
     * Getters and setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the base object for this form
     * 
     * @return DataClass
     */
    public function get_base_object()
    {
        return $this->base_object;
    }

    /**
     * Sets the base object for this form
     * 
     * @param DataClass $base_object
     */
    public function set_base_object(DataClass $base_object)
    {
        $this->base_object = $base_object;
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Builds the elements for the general tab
     */
    abstract public function build_general_tab_form_elements();

    /**
     * Returns the defaults for the selected base object (course_type)
     * 
     * @param DataClass $base_object
     *
     * @return string[]
     */
    abstract public function get_base_object_default_values(DataClass $base_object);
}
