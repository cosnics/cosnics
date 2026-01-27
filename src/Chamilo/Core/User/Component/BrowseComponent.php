<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Table\UserTableRenderer;
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
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getAdminUserTableRenderer(): UserTableRenderer
    {
        return $this->getService(UserTableRenderer::class);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {

            $buttonToolbar = new ButtonToolBar(
                $this->getUrlGenerator()->fromParameters(
                    [self::PARAM_CONTEXT => self::CONTEXT, self::PARAM_ACTION => self::ACTION_BROWSE]
                )
            );

            $commonActions = new ButtonGroup();
            $translator = $this->getTranslator();

            if ($this->getUser()->isPlatformAdministrator())
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                        $this->getUrlGenerator()->fromParameters(
                            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => self::ACTION_CREATE]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
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

    public function getUserTableCondition(): ?Condition
    {
        $search_properties = [];
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $this->getButtonToolbarRenderer()->getConditions($search_properties);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

        $html = [];

        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $adminUserTableRenderer->render($tableParameterValues, $users);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
