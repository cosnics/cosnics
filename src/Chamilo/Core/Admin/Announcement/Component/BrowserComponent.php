<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Table\Publication\PublicationTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport, DelegateComponent
{
    const PARAM_FILTER = 'filter';
    const PARAM_PUBLICATION_TYPE = 'publication_type';
    const TYPE_ALL = 1;
    const TYPE_FOR_ME = 2;
    const TYPE_FROM_ME = 3;
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    private $action_bar;

    public function run()
    {
        $user = $this->get_user();
        $this->action_bar = $this->get_action_bar();

        $publications_table = $this->get_publications_html();
        $toolbar = $this->get_action_bar();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $toolbar->as_html();
        $html[] = '<div id="action_bar_browser">';
        $html[] = $publications_table;
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

        $type = $this->get_type();

        $tabs = new DynamicVisualTabsRenderer('browser');

        if ($this->get_user()->is_platform_admin())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    self :: TYPE_ALL,
                    Translation :: get('AllPublications'),
                    Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
                    $this->get_url(array(self :: PARAM_PUBLICATION_TYPE => self :: TYPE_ALL)),
                    $type == self :: TYPE_ALL));
        }

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TYPE_FROM_ME,
                Translation :: get('PublishedForMe'),
                Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
                $this->get_url(array(self :: PARAM_PUBLICATION_TYPE => self :: TYPE_FOR_ME)),
                $type == self :: TYPE_FOR_ME));

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TYPE_FROM_ME,
                Translation :: get('MyPublications'),
                Theme :: getInstance()->getCommonImagePath('Treemenu/Publication'),
                $this->get_url(array(self :: PARAM_PUBLICATION_TYPE => self :: TYPE_FROM_ME)),
                $type == self :: TYPE_FROM_ME));

        $table = new PublicationTable($this);
        $tabs->set_content($table->as_html());

        return $tabs->render();
    }

    public function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->get_user()->get_platformadmin())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Publish', array(), Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', array(), Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('ShowToday', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Day'),
                $this->get_url(array(self :: PARAM_FILTER => self :: FILTER_TODAY)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('ShowThisWeek', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Week'),
                $this->get_url(array(self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('ShowThisMonth', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Month'),
                $this->get_url(array(self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $type = $this->get_type();
        switch ($type)
        {
            // Begin with the publisher condition when FROM_ME and add the
            // remaining conditions. Skip the publisher
            // condition when ALL.
            case self :: TYPE_FROM_ME :
                $publisher_id = Session :: get_user_id();

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($publisher_id));
            case self :: TYPE_ALL :
                break;
            default :
                $from_date_variables = new PropertyConditionVariable(
                    Publication :: class_name(),
                    Publication :: PROPERTY_FROM_DATE);

                $to_date_variable = new PropertyConditionVariable(
                    Publication :: class_name(),
                    Publication :: PROPERTY_TO_DATE);

                $time_conditions = array();
                $time_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_HIDDEN),
                    new StaticConditionVariable(0));

                $forever_conditions = array();
                $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));
                $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));
                $forever_condition = new AndCondition($forever_conditions);

                $between_conditions = array();
                $between_conditions[] = new InequalityCondition(
                    $from_date_variables,
                    InequalityCondition :: LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(time()));
                $between_conditions[] = new InequalityCondition(
                    $to_date_variable,
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable(time()));
                $between_condition = new AndCondition($between_conditions);

                $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

                $conditions[] = new AndCondition($time_conditions);
                break;
        }

        if ($this->get_search_condition())
        {
            $conditions[] = $this->get_search_condition();
        }

        $filter = Request :: get(self :: PARAM_FILTER);

        switch ($filter)
        {
            case self :: FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_MODIFICATION_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));
                break;
            case self :: FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_MODIFICATION_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));
                break;
            case self :: FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_MODIFICATION_DATE),
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($time));
                break;
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    public function get_search_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE),
                '*' . $query . '*');

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION),
                '*' . $query . '*');

            return new OrCondition($conditions);
        }

        return null;
    }

    public function get_type()
    {
        $type = Request :: get(self :: PARAM_PUBLICATION_TYPE);
        if (! $type)
        {
            if ($this->get_user()->is_platform_admin())
            {
                $type = self :: TYPE_ALL;
            }
            else
            {
                $type = self :: TYPE_FOR_ME;
            }
        }

        return $type;
    }
}
