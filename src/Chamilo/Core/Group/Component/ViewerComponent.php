<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\GroupRelUser\GroupRelUserTable;
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
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: viewer.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package group.lib.group_manager.component
 */
class ViewerComponent extends Manager implements TableSupport
{

    private $group;

    private $ab;

    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_GROUP_ID);
        if ($id)
        {
            $this->group = $this->retrieve_group($id);
            $this->root_group = $this->retrieve_groups(
                new EqualityCondition(
                    new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)))->next_result();
            $group = $this->group;

            if (! $this->get_user()->is_platform_admin())
            {
                throw new NotAllowedException();
            }

            $html = array();

            $html[] = $this->render_header();
            $this->ab = $this->get_action_bar();
            $html[] = $this->ab->as_html() . '<br />';

            $html[] = '<div class="clear"></div><div class="content_object" style="background-image: url(' .
                 Theme :: getInstance()->getCommonImagePath() . 'place_group.png);">';
            $html[] = '<div class="title">' . Translation :: get('Details') . '</div>';
            $html[] = '<b>' . Translation :: get('Code') . '</b>: ' . $group->get_code();
            $html[] = '<br /><b>' . Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES) . '</b>: ' .
                 $group->get_description();
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';

            $html[] = '<div class="content_object" style="background-image: url(' .
                 Theme :: getInstance()->getCommonImagePath() . 'place_users.png);">';
            $html[] = '<div class="title">' . Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context()) .
                 '</div>';

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_GROUP_ID] = $id;
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();
            $table = new GroupRelUserTable($this);
            $html[] = $table->as_html();
            $html[] = '</div>';

            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function get_condition()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_GROUP_ID),
            new StaticConditionVariable(Request :: get(self :: PARAM_GROUP_ID)));

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
            $condition = new OrCondition($or_conditions);

            $users = \Chamilo\Core\User\Storage\DataManager :: retrieves(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                new DataClassRetrievesParameters($condition));

            while ($user = $users->next_result())
            {
                $userconditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user->get_id());
            }

            if (count($userconditions))
                $conditions[] = new OrCondition($userconditions);
            else
                $conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, 0);
        }

        $condition = new AndCondition($conditions);

        return $condition;
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
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_edit.png',
                $this->get_group_editing_url($group),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->group != $this->root_group)
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath() . 'action_delete.png',
                    $this->get_group_delete_url($group),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('AddUsers', null, \Chamilo\Core\User\Manager :: context()),
                Theme :: getInstance()->getCommonImagePath() . 'action_subscribe.png',
                $this->get_group_suscribe_user_browser_url($group),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->get_id()));
        $users = $this->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if ($visible)
        {
            $toolbar_data[] = array(
                'href' => $this->get_group_emptying_url($group),
                'label' => Translation :: get('Truncate'),
                'img' => Theme :: getInstance()->getCommonImagePath() . 'action_recycle_bin.png',
                'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);

            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('Truncate'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_recycle_bin.png',
                    $this->get_group_emptying_url($group),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $toolbar_data[] = array(
                'label' => Translation :: get('TruncateNA'),
                'img' => Theme :: getInstance()->getCommonImagePath() . 'action_recycle_bin_na.png',
                'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('TruncateNA'),
                    Theme :: getInstance()->getCommonImagePath() . 'action_recycle_bin_na.png',
                    null,
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('Metadata', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath() . 'action_metadata.png',
                $this->get_group_metadata_url($group),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS)),
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add_help('group general');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_GROUP_ID);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
