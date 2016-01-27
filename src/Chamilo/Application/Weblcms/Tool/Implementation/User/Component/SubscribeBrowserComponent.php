<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed\UnsubscribedUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: user_subscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */
class SubscribeBrowserComponent extends Manager implements TableSupport
{

    private $action_bar;

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->action_bar = $this->get_action_bar();
        $this->set_parameter(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, $this->action_bar->get_query());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $this->get_user_subscribe_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_user_subscribe_html()
    {
        $table = new UnsubscribedUserTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ViewUsers'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_BROWSER)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    public function get_condition()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ACTIVE),
            new StaticConditionVariable(1));

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = $this->action_bar->get_conditions(
                array(
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME),
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME)));
        }

        return new AndCondition($conditions);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_user_subscribe_browser');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_TAB, \Chamilo\Application\Weblcms\Manager :: PARAM_GROUP);
    }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }
}
