<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Table\UserApprovalTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package user.lib.user_manager.component
 */
class UserApprovalBrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar(
                $this->getUrlGenerator()->fromParameters(
                    [self::PARAM_CONTEXT => self::CONTEXT, self::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER]
                )
            );
            $commonActions = new ButtonGroup();
            $translator = $this->getTranslator();

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
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

    /**
     * @throws \Exception
     */
    public function getUserApprovalTableCondition(): ?Condition
    {
        $search_properties = [];
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $this->buttonToolbarRenderer->getConditions($search_properties);
    }

    public function getUserApprovalTableRenderer(): UserApprovalTableRenderer
    {
        return $this->getService(UserApprovalTableRenderer::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function get_user_html(): string
    {
        $html = [];
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $this->renderTable();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $this->getRequest()->query->set(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->getButtonToolbarRenderer()->getSearchForm()->getQuery()
        );

        $totalNumberOfItems =
            $this->getUserService()->countUsersWaitingForApproval($this->getUserApprovalTableCondition());
        $userApprovalTableRenderer = $this->getUserApprovalTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $userApprovalTableRenderer->getParameterNames(), $userApprovalTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsersWaitingForApproval(
            $this->getUserApprovalTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $userApprovalTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $userApprovalTableRenderer->render($tableParameterValues, $users);
    }
}
