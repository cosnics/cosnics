<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PersonalInstance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PlatformInstance;
use Chamilo\Core\Repository\Instance\Table\Instance\InstanceTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
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

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $type;

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        $tabs = new DynamicTabsRenderer('instances');
        
        $tabs->add_tab(
            new DynamicContentTab(
                'personal_instance', 
                Translation :: get('PersonalInstance'), 
                null, 
                $this->get_table(PersonalInstance :: class_name())));
        
        if ($this->get_user()->is_platform_admin())
        {
            $tabs->add_tab(
                new DynamicContentTab(
                    'platform_instance', 
                    Translation :: get('PlatformInstance'), 
                    null, 
                    $this->get_table(PlatformInstance :: class_name())));
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $tabs->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table($type)
    {
        $this->type = $type;
        $table = new InstanceTable($this);
        
        return $table->as_html();
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation :: get('AddExternalInstance'), 
                    Theme :: getInstance()->getCommonImagePath('Action/Create'), 
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $commonActions->addButton(
                new Button(
                    Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_url(), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $commonActions->addButton(
                new Button(
                    Translation :: get('ManageRights', null, \Chamilo\Core\Rights\Manager :: package()), 
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'), 
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RIGHTS)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     *
     * @see \common\libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TYPE), 
            new StaticConditionVariable($this->type));
        
        if ($this->type == PersonalInstance :: class_name())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(PersonalInstance :: class_name(), PersonalInstance :: PROPERTY_USER_ID), 
                new StaticConditionVariable($this->get_user_id()));
        }
        
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TITLE), 
                '*' . $query . '*');
        }
        
        return new AndCondition($conditions);
    }

    public function get_type()
    {
        return $this->type;
    }
}
