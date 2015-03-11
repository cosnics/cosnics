<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Menu\RequestsTreeRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Table\AdminRequest\AdminRequestTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * $Id: admin_course_type_browser.class.php 218 2010-03-11 14:21:26Z Yannick & Tristan $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the the platform admin to browse the request
 */
class AdminRequestBrowserComponent extends Manager implements TableSupport
{
    const PENDING_REQUEST_VIEW = 'pending_request_view';
    const ALLOWED_REQUEST_VIEW = 'allowed_request_view';
    const DENIED_REQUEST_VIEW = 'denied_request_view';

    private $action_bar;

    private $request_type;

    private $request_view;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        Page :: getInstance()->setSection('admin');

        $this->request_type = Request :: get(self :: PARAM_REQUEST_TYPE);
        $this->request_view = Request :: get(self :: PARAM_REQUEST_VIEW);

        if (is_null($this->request_type))
        {
            $this->request_type = CommonRequest :: SUBSCRIPTION_REQUEST;
        }
        if (is_null($this->request_view))
        {
            $this->request_view = self :: PENDING_REQUEST_VIEW;
        }

        if (! $this->get_user()->is_platform_admin())
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();
        $this->action_bar = $this->get_action_bar();
        $html[] = $this->get_request_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_request_html()
    {
        $html = array();
        $menu = new RequestsTreeRenderer($this);
        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">' .
             $menu->render_as_tree() . '</div>';
        $html[] = '<div style="float: right; width: 80%;">';
        if ($this->request_view && $this->request_type)
        {
            $html[] = $this->get_table_html();
        }
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        return implode($html, "\n");
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }

    public function get_table_html()
    {
        $parameters = array();
        $parameters[self :: PARAM_CONTEXT] = self :: context();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_ADMIN_REQUEST_BROWSER;
        $parameters[self :: PARAM_REQUEST_TYPE] = $this->request_type;

        $table = new AdminRequestTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    public function get_condition()
    {
        $query = $this->action_bar->get_query();

        $conditions = array();

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CommonRequest :: class_name(), CommonRequest :: PROPERTY_MOTIVATION),
                '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CommonRequest :: class_name(), CommonRequest :: PROPERTY_SUBJECT),
                '*' . $query . '*');

            $search_conditions = new OrCondition($conditions);
        }

        if (count($search_conditions))
        {
            $conditions[] = $search_conditions;
        }

        switch ($this->request_view)
        {
            case self :: PENDING_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CommonRequest :: class_name(), CommonRequest :: PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest :: NO_DECISION));
                break;
            case self :: ALLOWED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CommonRequest :: class_name(), CommonRequest :: PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest :: ALLOWED_DECISION));
                break;
            case self :: DENIED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CommonRequest :: class_name(), CommonRequest :: PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest :: DENIED_DECISION));
                break;
        }

        $condition = null;
        if (count($conditions) > 1)
        {
            $condition = new AndCondition($conditions);
        }
        else
        {
            if (count($conditions) == 1)
            {
                $condition = $conditions[0];
            }
        }

        return $condition;
    }

    public function get_request_type()
    {
        return $this->request_type;
    }

    public function get_request_view()
    {
        return $this->request_view;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->get_user()->is_platform_admin())
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    Redirect :: get_link(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                            \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_ADMIN_BROWSER),
                        array(),
                        false,
                        Redirect :: TYPE_CORE),
                    Translation :: get('TypeName', null, 'core\admin')));
            $breadcrumbtrail->add(
                new Breadcrumb(
                    Redirect :: get_link(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                            \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_ADMIN_BROWSER,
                            DynamicTabsRenderer :: PARAM_SELECTED_TAB => self :: APPLICATION_NAME),
                        array(),
                        false,
                        Redirect :: TYPE_CORE),
                    Translation :: get('Courses')));
        }

        if ($this->category)
        {
            $category = DataManager :: retrieve_by_id(CourseCategory :: class_name(), $this->category);
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(), $category->get_name()));
        }
    }

    public function get_additional_parameters()
    {
        return array();
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
