<?php
namespace Chamilo\Application\CasUser\Component;

use Chamilo\Application\CasUser\Storage\DataClass\AccountRequest;
use Chamilo\Application\CasUser\Manager;
use Chamilo\Application\CasUser\Table\Request\RequestTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
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

    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('cas_user general');

        $this->action_bar = $this->get_action_bar();

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        $html[] = $this->action_bar->as_html();
        $html[] = '<div id="action_bar_browser">';
        $table = new RequestTable($this);
        $html[] = $table->as_html();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode("\n", $html);
    }

    public function get_object_table_condition($object_table_class_name)
    {
        $user = $this->get_user();
        $query = $this->action_bar->get_query();
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

    public function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->set_search_url($this->get_url());

            $this->action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('RequestAccount'),
                    Theme :: getInstance()->getImagePath() . 'action/request.png',
                    $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_CREATE))));

            if ($this->get_user()->is_platform_admin())
            {
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('ManageAccounts'),
                        Theme :: getInstance()->getImagePath() . 'action/manage_accounts.png',
                        $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_ACCOUNT))));
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('ManageServices'),
                        Theme :: getInstance()->getImagePath() . 'action/manage_services.png',
                        $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_SERVICE))));
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('ConfigureManagementRights'),
                        Theme :: getInstance()->getImagePath() . 'action/rights.png',
                        $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_RIGHTS))));
            }
        }
        return $this->action_bar;
    }

    /*
     * (non-PHPdoc) @see \Chamilo\Libraries\Format\Table\Interfaces\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        // TODO Auto-generated method stub
    }
}
