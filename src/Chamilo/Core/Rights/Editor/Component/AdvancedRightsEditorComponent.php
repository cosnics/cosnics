<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityBrowserTreeMenu;
use Chamilo\Core\Rights\Entity\NestedRightsEntity;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ConditionProperty;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Simple interface to edit rights
 * 
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 * @deprecated Should not be needed anymore
 */
class AdvancedRightsEditorComponent extends RightsEditorComponent implements TableSupport
{
    const TABS_NAME = 'rights_editor';
    const TABS_NAME_CATEGORY = 'rights_editor_category';
    const TAB_ENTITY = 'entity';
    const TAB_SUB_ENTITIES = 'sub_entities';

    private $table_conditions;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs the component
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<br />';
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->display_entities();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the entities tabs
     */
    public function display_entities()
    {
        $entities = $this->get_entities();
        $selected_entity = $this->get_selected_entity();
        
        $tabs = new DynamicVisualTabsRenderer(self::TABS_NAME, $this->display_entity($selected_entity));
        
        foreach ($entities as $entity_type => $entity)
        {
            $name = $entity->get_entity_translated_name();
            $link = $this->get_entity_url($entity_type);
            $selected = ($selected_entity->get_entity_type() == $entity_type);
            
            $tab = new DynamicVisualTab($entity_type, $name, $entity->get_entity_icon(), $link, $selected);
            $tabs->add_tab($tab);
        }
        
        return $tabs->render();
    }

    /**
     * Displays the content of the selected entity
     */
    public function display_entity($selected_entity)
    {
        $html = array();
        
        if ($selected_entity instanceof NestedRightsEntity)
        {
            $html[] = '<div style="float: left; width: 18%; overflow: auto;">';
            $html[] = $this->display_entity_menu();
            $html[] = '</div>';
            
            $html[] = '<div style="float: right; width: 80%; overflow:auto;">';
            $html[] = $this->display_entity_table($selected_entity);
            $html[] = '</div>';
        }
        else
        {
            $html[] = $this->display_entity_table($selected_entity);
        }
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the location entity table of the selected entity
     */
    public function display_entity_table($selected_entity)
    {
        $html = array();
        
        $search_query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        $html[] = '<div style="overflow: auto;">';
        
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($selected_entity);
        $class = ClassnameUtilities::getInstance()->getClassnameFromObject($selected_entity);
        $location_entity_table = $namespace . '\\' . $class . '\\' . $class . 'Table';
        
        if ($selected_entity instanceof NestedRightsEntity)
        {
            $selected_id = $this->get_selected_entity_id();
            
            $tabs = new DynamicTabsRenderer(self::TABS_NAME_CATEGORY);
            
            // Get the rights table for the current entity item
            $this->table_conditions[self::TAB_ENTITY] = $this->get_condition(
                $selected_entity, 
                $search_query, 
                $selected_id);
            $table = new $location_entity_table($this, self::TAB_ENTITY);
            
            $tabs->add_tab(
                new DynamicContentTab(self::TAB_ENTITY, Translation::get('Rights'), null, $table->as_html()));
            
            // Get the rights table for the children entity items
            $this->table_conditions[self::TAB_SUB_ENTITIES] = $this->get_condition(
                $selected_entity, 
                $search_query, 
                null, 
                $selected_id);
            $table = new $location_entity_table($this, self::TAB_SUB_ENTITIES);
            
            $tabs->add_tab(
                new DynamicContentTab(self::TAB_SUB_ENTITIES, Translation::get('Children'), null, $table->as_html()));
            
            $html[] = $tabs->render();
        }
        else
        {
            $this->table_conditions['no_tab'] = $this->get_condition($selected_entity, $search_query);
            $table = new $location_entity_table($this, 'no_tab');
            
            $html[] = $table->as_html();
        }
        
        $location_ids = array();
        foreach ($this->get_locations() as $location)
        {
            $location_ids[] = $location->get_id();
        }
        
        $html[] = '</div>';
        $html[] = '<script type="text/javascript">';
        $html[] = '  var locations = \'' . json_encode($location_ids) . '\';';
        $html[] = '</script>';
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Rights\Editor', true) . 'ConfigureEntity.js');
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the location entity tree menu of the selected entity
     */
    public function display_entity_menu()
    {
        $html = array();
        
        $tree = new LocationEntityBrowserTreeMenu($this, $this->get_selected_entity());
        $html[] = $tree->render_as_tree();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the actionbar;
     * 
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            
            // Add the simple rights editor button
            $commonActions->addButton(
                new Button(
                    Translation::get('SimpleRightsEditor'), 
                    Theme::getInstance()->getCommonImagePath('Action/Config'), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EDIT_SIMPLE_RIGHTS)), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            // Add the show all button
            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_url(), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            // Add the inherit button
            $locations = $this->get_locations();
            if (count($locations) == 1)
            {
                $location = $locations[0];
                if ($location != null && $location->get_parent())
                {
                    $url = $this->get_url(array(self::PARAM_ACTION => self::ACTION_CHANGE_INHERIT));
                    
                    if ($location->inherits())
                    {
                        // Disable inherit
                        $commonActions->addButton(
                            new Button(
                                Translation::get('NoInherit'), 
                                Theme::getInstance()->getCommonImagePath('Action/SettingFalseInherit'), 
                                $url, 
                                ToolbarItem::DISPLAY_ICON_AND_LABEL));
                    }
                    else
                    {
                        // Enable the inherit
                        $commonActions->addButton(
                            new Button(
                                Translation::get('Inherit'), 
                                Theme::getInstance()->getCommonImagePath('Action/SettingTrueInherit'), 
                                $url, 
                                ToolbarItem::DISPLAY_ICON_AND_LABEL));
                    }
                }
            }
            
            $buttonToolbar->addButtonGroup($commonActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Builds the condition for the entity table
     * 
     * @param RightsEntity selected_entity
     * @param String $search_query
     * @param int $entity_id
     * @param int $parent_id
     *
     * @return Condition
     */
    public function get_condition($selected_entity, $search_query, $entity_id = null, $parent_id = null)
    {
        $conditions = array();
        
        if (isset($entity_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $selected_entity->data_class_class_name(), 
                    $selected_entity->get_id_property()), 
                new StaticConditionVariable($entity_id));
        }
        
        if (isset($parent_id))
        {
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $selected_entity->data_class_class_name(), 
                    $selected_entity->get_parent_property()), 
                new StaticConditionVariable($parent_id));
        }
        
        if (isset($search_query) && $search_query != '')
        {
            $condition_properties = array();
            
            foreach ($selected_entity->get_search_properties() as $property)
            {
                $condition_properties[] = new ConditionProperty($property);
            }
            
            $conditions[] = $this->buttonToolbarRenderer->getConditions($condition_properties);
        }
        
        $count = count($conditions);
        if ($count > 1)
        {
            return new AndCondition($conditions);
        }
        elseif ($count == 1)
        {
            return $conditions[0];
        }
    }

    /**
     * Register additional parameters for the breadcrumbs etc
     * 
     * @return Array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_ENTITY_TYPE, self::PARAM_ENTITY_ID);
    }

    public function get_selected_entity_id()
    {
        $current_node_id = $this->get_parameter(self::PARAM_ENTITY_ID);
        
        if (! $current_node_id)
        {
            $root_ids = $this->get_selected_entity()->get_root_ids();
            if (empty($root_ids))
            {
                return 0;
            }
            
            return $root_ids[0];
        }
        
        return $current_node_id;
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->table_conditions;
    }
}
