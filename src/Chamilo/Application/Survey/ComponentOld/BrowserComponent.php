<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Table\Publication\PublicationTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport
{
    const TAB_PUBLICATIONS = 1;
    const TAB_MY_PUBLICATIONS = 2;

    private $actionBar;

    function run()
    {
        $this->actionBar = $this->getActionBar();
        
        $html = array();
        $html[] = $this->render_header();
        $html[] = $this->actionBar->as_html();
        $html[] = $this->getTabs();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    function getTabs()
    {
        $tabs = new DynamicVisualTabsRenderer(self :: class_name());
        
        if (Rights :: get_instance()->publication_is_allowed())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    self :: TAB_MY_PUBLICATIONS, 
                    Translation :: get('MyPublications'), 
                    Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'logo/16'), 
                    $this->get_url()));
        }
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_PUBLICATIONS, 
                Translation :: get('Publications'), 
                Theme :: getInstance()->getCommonImagePath('Action/Next'),
                $this->get_url()));
        
        $table = new PublicationTable($this);
        
        $tabs->set_content($table->as_html());
        
        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function getActionBar()
    {
        $actionBar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $actionBar->set_search_url($this->get_url());
        
        if (Rights :: get_instance()->publication_is_allowed())
        {
            $actionBar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Publish', array(), Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'), 
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISH)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->get_user()->is_platform_admin())
        {
            $actionBar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('RightsManager', array(), Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'), 
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPLICATION_RIGHTS)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        return $actionBar;
    }

    function getCondition()
    {
        $conditions = array();
//         $right = null;
        
//         switch ($this->get_table_type())
//         {
//             case self :: TAB_EXPORT :
//                 $right = Rights :: RIGHT_EXPORT_RESULT;
//                 break;
            
//             case self :: TAB_PARTICIPATE :
//                 $right = Rights :: PARTICIPATE_RIGHT;
//                 break;
            
//             case self :: TAB_REPORT :
//                 $right = Rights :: RIGHT_REPORTING;
//         }
        
//         if (isset($right))
//         {
//             $entities = array();
//             $entities[UserEntity :: ENTITY_TYPE] = new UserEntity();
//             $entities[PlatformGroupEntity :: ENTITY_TYPE] = new PlatformGroupEntity();
            
//             $publication_ids = Rights :: get_instance()->get_publication_ids_for_granted_right($right, $entities);
            
//             $conditions[] = new InCondition(
//                 new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_ID), 
//                 $publication_ids);
//         }
//         else
//         {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER_ID), 
                new StaticConditionVariable($this->get_user_id()));
//         }
        
        $query = $this->actionBar->get_query();
        
        if (isset($query) && $query != '')
        {
            $search_conditions = array();
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_TITLE), 
                '*' . $query . '*');
        }
        
        return new AndCondition($conditions);
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->getCondition();
    }
}
?>