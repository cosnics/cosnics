<?php
namespace Chamilo\Application\CasStorage\Service\Component;

use Chamilo\Application\CasStorage\Service\Manager;
use Chamilo\Application\CasStorage\Service\Storage\DataClass\Service;
use Chamilo\Application\CasStorage\Service\Table\Service\ServiceTable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add_help('cas_user general');
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = '<div id="action_bar_browser">';
        $table = new ServiceTable($this);
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($object_table_class_name)
    {
        $user = $this->get_user();
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = array();
        
        if (isset($query) && $query != '')
        {
            $query_conditions = array();
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Service::class_name(), Service::PROPERTY_NAME), 
                '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Service::class_name(), Service::PROPERTY_DESCRIPTION), 
                '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Service::class_name(), Service::PROPERTY_SERVICE_ID), 
                '*' . $query . '*');
            $conditions[] = new OrCondition($query_conditions);
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            
            if ($this->get_user()->is_platform_admin())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('CreateService'), 
                        Theme::getInstance()->getCommonImagePath('Action/Create'), 
                        $this->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_CREATE))));
            }
            $buttonToolbar->addButtonGroup($commonActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
