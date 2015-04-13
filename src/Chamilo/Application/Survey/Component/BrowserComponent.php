<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Table\Publication\PublicationTable;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport
{
    const TAB_MY_PUBLICATIONS = 1;
    const TAB_PARTICIPATE = 2;
    const TAB_EXPORT = 3;
    const TAB_REPORT = 4;
    const MY_PUBLICATIONS = - 1;
    const PARAM_TABLE_TYPE = 'table_type';

    private $table_type;

    private $action_bar;

    function run()
    {
        $this->table_type = Request :: get(self :: PARAM_TABLE_TYPE, self :: TAB_PARTICIPATE);
        $this->action_bar = $this->get_action_bar();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $this->get_tables();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    function get_tables()
    {
        $tabs = new DynamicVisualTabsRenderer(self :: class_name());
        
        $params = $this->get_parameters();
        $params[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        if (Rights :: get_instance()->publication_is_allowed())
        {
            $params[self :: PARAM_TABLE_TYPE] = self :: TAB_MY_PUBLICATIONS;
            $tabs->add_tab(
                new DynamicVisualTab(
                    self :: TAB_MY_PUBLICATIONS, 
                    Translation :: get('MyPublications'), 
                    Theme :: getInstance()->getImagePath('Chamilo\Application\Survey\\') . 'logo/16.png', 
                    $this->get_url($params), 
                    $this->get_table_type() == self :: TAB_MY_PUBLICATIONS));
        }
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_PARTICIPATE;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_PARTICIPATE, 
                Translation :: get('Participate'), 
                Theme :: getInstance()->getCommonImagePath() . 'action_next.png', 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_PARTICIPATE));
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_EXPORT;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_EXPORT, 
                Translation :: get('ExportResults'), 
                Theme :: getInstance()->getCommonImagePath() . 'action_export.png', 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_EXPORT));
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_REPORT;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_REPORT, 
                Translation :: get('Reporting'), 
                Theme :: getInstance()->getCommonImagePath() . 'action_view_results.png', 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_REPORT));
        
        $table = new PublicationTable($this);
        $tabs->set_content($table->as_html());
        
        $html[] = $tabs->render();
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_TABLE_TYPE => $this->get_table_type())));
        
        if (Rights :: get_instance()->publication_is_allowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Publish', array(), Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_publish.png', 
                    $this->get_create_survey_publication_url(), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->get_user()->is_platform_admin())
        {
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('RightsManager', array(), Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_rights.png', 
                    $this->get_application_rights_url(), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $action_bar;
    }

    function get_condition()
    {
        $conditions = array();
        $right = null;
        
        switch ($this->get_table_type())
        {
            case self :: TAB_EXPORT :
                $right = Rights :: RIGHT_EXPORT_RESULT;
                break;
            
            case self :: TAB_PARTICIPATE :
                $right = Rights :: PARTICIPATE_RIGHT;
                break;
            
            case self :: TAB_REPORT :
                $right = Rights :: RIGHT_REPORTING;
        }
        
        if (isset($right))
        {
            $entities = array();
            $entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
            $entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();
            
            $publication_ids = Rights :: get_instance()->get_publication_ids_for_granted_right($right, $entities);
            
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_ID), 
                $publication_ids);
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER), 
                new StaticConditionVariable($this->get_user_id()));
        }
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_TITLE), 
                '*' . $query . '*');
            // $search_conditions[] = new PatternMatchCondition(Publication :: PROPERTY_DESCRIPTION, '*' . $query .
            // '*');
            // $conditions[] = new OrCondition($search_conditions);
        }
        
        return new AndCondition($conditions);
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    public function get_table_type()
    {
        return $this->table_type;
    }
}
?>