<?php
namespace Chamilo\Application\CasStorage\Component;

use Chamilo\Application\CasStorage\Manager;
use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Application\CasStorage\Table\Request\RequestTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class BrowserComponent extends Manager implements DelegateComponent, TableSupport
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
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('cas_user general');
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = '<div id="action_bar_browser">';
        $table = new RequestTable($this);
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_object_table_condition($object_table_class_name)
    {
        $user = $this->get_user();
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = array();
        
        if (isset($query) && $query != '')
        {
            $query_conditions = array();
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(AccountRequest :: class_name(), AccountRequest :: PROPERTY_FIRST_NAME), 
                '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(AccountRequest :: class_name(), AccountRequest :: PROPERTY_LAST_NAME), 
                '*' . $query . '*');
            $query_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(AccountRequest :: class_name(), AccountRequest :: PROPERTY_EMAIL), 
                '*' . $query . '*');
            $conditions[] = new OrCondition($query_conditions);
        }
        
        if (! $user->is_platform_admin())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(AccountRequest :: class_name(), AccountRequest :: PROPERTY_REQUESTER_ID), 
                new StaticConditionVariable($user->get_id()));
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
            if (! isset($this->buttonToolbarRenderer))
            {
                $buttonToolbar = new ButtonToolBar($this->get_url());
                $commonActions = new ButtonGroup();
                $toolActions = new ButtonGroup();
                
                $commonActions->addButton(
                    new Button(
                        Translation :: get('RequestAccount'), 
                        Theme :: getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/Request'), 
                        $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_CREATE))));
                
                if ($this->get_user()->is_platform_admin())
                {
                    $toolActions->addButton(
                        new Button(
                            Translation :: get('ManageAccounts'), 
                            Theme :: getInstance()->getImagePath(
                                'Chamilo\Application\CasStorage', 
                                'Action/ManageAccounts'), 
                            $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_ACCOUNT))));
                    $toolActions->addButton(
                        new Button(
                            Translation :: get('ManageServices'), 
                            Theme :: getInstance()->getImagePath(
                                'Chamilo\Application\CasStorage', 
                                'Action/ManageServices'), 
                            $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_SERVICE))));
                    $toolActions->addButton(
                        new Button(
                            Translation :: get('ConfigureManagementRights'), 
                            Theme :: getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/Rights'), 
                            $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_RIGHTS))));
                }
            }
            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /*
     * (non-PHPdoc) @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        // TODO Auto-generated method stub
    }
}
