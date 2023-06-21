<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Table\AdminUserTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
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
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AdminUserBrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdminUserTableRenderer(): AdminUserTableRenderer
    {
        return $this->getService(AdminUserTableRenderer::class);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {

            $buttonToolbar = new ButtonToolBar(
                $this->getUrlGenerator()->fromParameters(
                    [self::PARAM_CONTEXT => self::CONTEXT, self::PARAM_ACTION => self::ACTION_BROWSE_USERS]
                )
            );

            $commonActions = new ButtonGroup();
            $translator = $this->getTranslator();

            if ($this->getUser()->isPlatformAdmin())
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                        $this->get_url([Application::PARAM_ACTION => self::ACTION_CREATE_USER]),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        $translator->trans('Reporting', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('chart-pie', [], null, 'fas'), $this->get_reporting_url(),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

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
    public function getUserTableCondition(): ?Condition
    {
        $search_properties = [];
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $this->getButtonToolbarRenderer()->getConditions($search_properties);
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $this->getRequest()->query->set(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->getButtonToolbarRenderer()->getSearchForm()->getQuery()
        );

        $totalNumberOfItems = $this->getUserService()->countUsers($this->getUserTableCondition());
        $adminUserTableRenderer = $this->getAdminUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $adminUserTableRenderer->getParameterNames(), $adminUserTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getUserTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $adminUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $adminUserTableRenderer->render($tableParameterValues, $users);
    }
}
