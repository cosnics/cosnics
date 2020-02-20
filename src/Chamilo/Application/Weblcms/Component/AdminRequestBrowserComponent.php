<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Menu\RequestsTreeRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Table\AdminRequest\AdminRequestTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
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

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $request_type;

    private $request_view;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageCourses');

        $this->request_type = Request::get(self::PARAM_REQUEST_TYPE);
        $this->request_view = Request::get(self::PARAM_REQUEST_VIEW);

        if (is_null($this->request_type))
        {
            $this->request_type = CommonRequest::SUBSCRIPTION_REQUEST;
        }
        if (is_null($this->request_view))
        {
            $this->request_view = self::PENDING_REQUEST_VIEW;
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_request_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_request_html()
    {
        $html = array();
        $menu = new RequestsTreeRenderer($this);
        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
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

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_table_html()
    {
        $parameters = array();
        $parameters[self::PARAM_CONTEXT] = self::context();
        $parameters[self::PARAM_ACTION] = self::ACTION_ADMIN_REQUEST_BROWSER;
        $parameters[self::PARAM_REQUEST_TYPE] = $this->request_type;

        $table = new AdminRequestTable($this);

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    public function get_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $conditions = array();

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_MOTIVATION),
                '*' . $query . '*'
            );
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_SUBJECT),
                '*' . $query . '*'
            );

            $search_conditions = new OrCondition($conditions);
        }

        if (count($search_conditions))
        {
            $conditions[] = $search_conditions;
        }

        switch ($this->request_view)
        {
            case self::PENDING_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CourseRequest::NO_DECISION)
                );
                break;
            case self::ALLOWED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CourseRequest::ALLOWED_DECISION)
                );
                break;
            case self::DENIED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class_name(), CourseRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CourseRequest::DENIED_DECISION)
                );
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
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                )
            );

            $breadcrumbtrail->add(
                new Breadcrumb($redirect->getUrl(), Translation::get('TypeName', null, 'Chamilo\Core\Admin'))
            );

            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                    \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                    DynamicTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                        self::package()
                    )
                )
            );

            $breadcrumbtrail->add(new Breadcrumb($redirect->getUrl(), Translation::get('Courses')));
        }

        if ($this->category)
        {
            $category = DataManager::retrieve_by_id(CourseCategory::class_name(), $this->category);
            $breadcrumbtrail->add(new Breadcrumb($this->get_url(), $category->get_name()));
        }
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
