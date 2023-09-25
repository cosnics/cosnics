<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Application\Weblcms\Request\Table\ManagementRequestTableRenderer;
use Chamilo\Application\Weblcms\Request\Table\Request\RequestTable;
use Chamilo\Application\Weblcms\Request\Table\UserRequestTableRenderer;
use Chamilo\Core\Repository\Quota\Table\RequestTableRenderer;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \TableException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Exception
     */
    public function run()
    {
        $translator = $this->getTranslator();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $user_requests = DataManager::count(Request::class, new DataClassCountParameters($condition));

        $tabs = new TabsCollection();

        if ($user_requests > 0 || Rights::getInstance()->request_is_allowed())
        {

            if ($user_requests > 0)
            {
                $totalNumberOfItems = \Chamilo\Core\Repository\Quota\Storage\DataManager::count(
                    \Chamilo\Core\Repository\Quota\Storage\DataClass\Request::class,
                    new DataClassCountParameters($this->getRequestCondition(RequestTableRenderer::TYPE_PERSONAL))
                );
                $requestTableRenderer = $this->getUserRequestTableRenderer();

                $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
                    $requestTableRenderer->getParameterNames(), $requestTableRenderer->getDefaultParameterValues(),
                    $totalNumberOfItems
                );

                $requests = DataManager::retrieves(
                    Request::class, new DataClassRetrievesParameters(
                        $this->getRequestCondition(RequestTableRenderer::TYPE_PERSONAL),
                        $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
                        $requestTableRenderer->determineOrderBy($tableParameterValues)
                    )
                );

                $tabs->add(
                    new ContentTab(
                        'personal_request', $translator->trans('YourRequests', [], Manager::CONTEXT),
                        $requestTableRenderer->render($tableParameterValues, $requests),
                        new FontAwesomeGlyph('inbox', ['fa-lg'], null, 'fas')
                    )
                );
            }

            if (Rights::getInstance()->request_is_allowed())
            {
                $target_users = Rights::getInstance()->get_target_users(
                    $this->getUser()
                );

                if (count($target_users) > 0)
                {
                    $target_condition = new InCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                    );
                }
                else
                {
                    $target_condition = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                        new StaticConditionVariable(- 1)
                    );
                }

                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                if (!$this->getUser()->isPlatformAdmin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $tabs->add(
                        new ContentTab(
                            (string) RequestTableRenderer::TYPE_PENDING,
                            $translator->trans('PendingRequests', [], Manager::CONTEXT),
                            $this->renderManagementRequestTable(RequestTableRenderer::TYPE_PENDING),
                            new FontAwesomeGlyph('pause-circle', ['fa-lg'], null, 'fas')
                        )
                    );
                }

                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                if (!$this->getUser()->isPlatformAdmin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $tabs->add(
                        new ContentTab(
                            (string) RequestTableRenderer::TYPE_GRANTED,
                            $translator->trans('GrantedRequests', [], Manager::CONTEXT),
                            $this->renderManagementRequestTable(RequestTableRenderer::TYPE_GRANTED),
                            new FontAwesomeGlyph('check-circle', ['fa-lg', 'text-success'], null, 'fas')
                        )
                    );
                }

                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                if (!$this->getUser()->isPlatformAdmin())
                {
                    $conditions[] = $target_condition;
                }
                $condition = new AndCondition($conditions);

                if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                {
                    $tabs->add(
                        new ContentTab(
                            (string) RequestTableRenderer::TYPE_DENIED,
                            $translator->trans('DeniedRequests', [], Manager::CONTEXT),
                            $this->renderManagementRequestTable(RequestTableRenderer::TYPE_DENIED),
                            new FontAwesomeGlyph('minus-square', ['fa-lg', 'text-danger'], null, 'fas')
                        )
                    );
                }
            }
        }

        if ($user_requests > 0 || (Rights::getInstance()->request_is_allowed() && $tabs->count() > 0) ||
            $this->getUser()->isPlatformAdmin())
        {
            $html = [];

            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
            $html[] = $this->renderHeader();
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $this->getTabsRenderer()->render('request', $tabs);
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirectWithMessage(
                $translator->trans('NoRequestsFormDirectly'), false, [self::PARAM_ACTION => self::ACTION_CREATE]
            );
        }
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            $translator = $this->getTranslator();

            if ($this->request_allowed())
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('RequestCourse', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('question-circle', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE])
                    )
                );
            }

            if ($this->getUser()->isPlatformAdmin())
            {
                $toolActions->addButton(
                    new Button(
                        $translator->trans('ConfigureManagementRights', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('lock', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_RIGHTS])
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getManagementRequestTableRenderer(): ManagementRequestTableRenderer
    {
        return $this->getService(ManagementRequestTableRenderer::class);
    }

    public function getRequestCondition(int $requestType): AndCondition
    {
        $conditions = [];

        switch ($requestType)
        {
            case RequestTable::TYPE_PENDING :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                break;
            case RequestTable::TYPE_PERSONAL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable($this->getUser()->getId())
                );
                break;
            case RequestTable::TYPE_GRANTED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                break;
            case RequestTable::TYPE_DENIED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                break;
        }

        if (!$this->getUser()->isPlatformAdmin() && Rights::getInstance()->request_is_allowed() &&
            $requestType != RequestTable::TYPE_PERSONAL)
        {
            $target_users = Rights::getInstance()->get_target_users(
                $this->getUser()
            );

            if (count($target_users) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable(- 1)
                );
            }
        }

        return new AndCondition($conditions);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    protected function getUserRequestTableRenderer(): UserRequestTableRenderer
    {
        return $this->getService(UserRequestTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderManagementRequestTable(int $requestType): string
    {
        $totalNumberOfItems = \Chamilo\Core\Repository\Quota\Storage\DataManager::count(
            \Chamilo\Core\Repository\Quota\Storage\DataClass\Request::class,
            new DataClassCountParameters($this->getRequestCondition($requestType))
        );
        $requestTableRenderer = $this->getUserRequestTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $requestTableRenderer->getParameterNames(), $requestTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $requests = DataManager::retrieves(
            Request::class, new DataClassRetrievesParameters(
                $this->getRequestCondition($requestType), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $requestTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $requestTableRenderer->render($tableParameterValues, $requests);
    }
}