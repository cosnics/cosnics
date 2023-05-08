<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Menu\RequestsTreeRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Table\AdminRequestTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which allows the the platform admin to browse the request
 */
class AdminRequestBrowserComponent extends Manager
{
    public const ALLOWED_REQUEST_VIEW = 'allowed_request_view';
    public const DENIED_REQUEST_VIEW = 'denied_request_view';
    public const PENDING_REQUEST_VIEW = 'pending_request_view';

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private $requestType;

    private $requestView;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageCourses');

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderComponent();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        if ($this->getUser()->is_platform_admin())
        {
            $urlGenerator = $this->getUrlGenerator();
            $translator = $this->getTranslator();

            $browseUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
                ]
            );

            $breadcrumbtrail->add(
                new Breadcrumb($browseUrl, $translator->trans('TypeName', [], 'Chamilo\Core\Admin'))
            );

            $browseTabUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                    GenericTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                        Manager::CONTEXT
                    )
                ]
            );

            $breadcrumbtrail->add(new Breadcrumb($browseTabUrl, $translator->trans('Courses', [], Manager::CONTEXT)));
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getAdminRequestCondition(): AndCondition
    {
        $query = $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();

        $conditions = [];

        if (isset($query) && $query != '')
        {
            $searchConditions = [];

            $searchConditions[] = new ContainsCondition(
                new PropertyConditionVariable(CourseRequest::class, CommonRequest::PROPERTY_MOTIVATION), $query
            );
            $searchConditions[] = new ContainsCondition(
                new PropertyConditionVariable(CourseRequest::class, CommonRequest::PROPERTY_SUBJECT), $query
            );

            $conditions[] = new OrCondition($searchConditions);
        }

        switch ($this->getRequestView())
        {
            case self::PENDING_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class, CommonRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest::NO_DECISION)
                );
                break;
            case self::ALLOWED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class, CommonRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest::ALLOWED_DECISION)
                );
                break;
            case self::DENIED_REQUEST_VIEW :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(CourseRequest::class, CommonRequest::PROPERTY_DECISION),
                    new StaticConditionVariable(CommonRequest::DENIED_DECISION)
                );
                break;
        }

        return new AndCondition($conditions);
    }

    public function getAdminRequestTableRenderer(): AdminRequestTableRenderer
    {
        return $this->getService(AdminRequestTableRenderer::class);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('ShowAll', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('folder'), $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRequestType()
    {
        if (!isset($this->requestType))
        {
            $this->requestType =
                $this->getRequest()->query->get(self::PARAM_REQUEST_TYPE, CommonRequest::SUBSCRIPTION_REQUEST);
        }

        return $this->requestType;
    }

    public function getRequestView()
    {
        if (!isset($this->requestView))
        {
            $this->requestView = $this->getRequest()->query->get(self::PARAM_REQUEST_VIEW, self::PENDING_REQUEST_VIEW);
        }

        return $this->requestView;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function renderComponent(): string
    {
        $menu = new RequestsTreeRenderer($this);

        $html = [];

        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = '<br />';

        $html[] = '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">';
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $this->renderTable();
        $html[] = '</div>';

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    public function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(CourseRequest::class, new DataClassCountParameters($this->getAdminRequestCondition()));
        $adminRequestTableRenderer = $this->getAdminRequestTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $adminRequestTableRenderer->getParameterNames(), $adminRequestTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $requests = DataManager::retrieves(
            CourseRequest::class, new DataClassRetrievesParameters(
                $this->getAdminRequestCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $adminRequestTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $adminRequestTableRenderer->render($tableParameterValues, $requests);
    }
}
