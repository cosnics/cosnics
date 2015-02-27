<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\SubscribeUser\SubscribeUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: subscribe_user_browser.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package group.lib.group_manager.component
 */
class SubscribeUserBrowserComponent extends Manager implements TableSupport
{

    private $group;

    private $ab;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $group_id = Request :: get(self :: PARAM_GROUP_ID);

        if (isset($group_id))
        {
            $this->group = $this->retrieve_group($group_id);
        }

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->ab = $this->get_action_bar();
        $output = $this->get_user_subscribe_html();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->ab->as_html() . '<br />';
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_user_subscribe_html()
    {
        $this->set_parameter(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $this->ab->get_query());

        $table = new SubscribeUserTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    public function get_table_condition($object_table_class_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_GROUP_ID),
            new StaticConditionVariable(Request :: get(GroupRelUser :: PROPERTY_GROUP_ID)));

        $users = $this->retrieve_group_rel_users($condition);

        $conditions = array();
        while ($user = $users->next_result())
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
                    new StaticConditionVariable($user->get_user_id())));
        }

        $query = $this->ab->get_query();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME),
                '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }

        if (count($conditions) == 0)
        {
            return null;
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    public function get_group()
    {
        return $this->group;
    }

    public function get_action_bar()
    {
        $group = $this->group;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(self :: PARAM_GROUP_ID => $group->get_id())));

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_browser.png',
                $this->get_url(array(self :: PARAM_GROUP_ID => $group->get_id())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS)),
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_VIEW_GROUP,
                        self :: PARAM_GROUP_ID => Request :: get(self :: PARAM_GROUP_ID))),
                Translation :: get('ViewerComponent')));
        $breadcrumbtrail->add_help('group general');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_GROUP_ID);
    }
}
