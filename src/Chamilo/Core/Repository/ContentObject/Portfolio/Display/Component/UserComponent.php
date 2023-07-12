<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\UserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Component that allows a user to emulate the rights another user has on his or her portfolio
 *
 * @package repository\content_object\portfolio\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserComponent extends ItemComponent
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function build()
    {
        $translator = $this->getTranslator();

        // Check whether portfolio rights are enabled and whether the user can actually set them
        if (!$this->get_parent() instanceof PortfolioComplexRights ||
            !$this->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $message = Display::warning_message($translator->trans('ComplexRightsNotSupported', [], Manager::CONTEXT));

            $html = [];

            $html[] = $this->render_header();
            $html[] = $message;
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        // If a virtual user is currently configured, clear it
        $virtual_user = $this->get_parent()->get_portfolio_virtual_user();

        if ($virtual_user instanceof User)
        {
            $this->get_parent()->clear_virtual_user_id();
            $this->redirectWithMessage(
                $translator->trans('BackInRegularView', [], Manager::CONTEXT), false,
                [self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]
            );
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $this->set_parameter(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->buttonToolbarRenderer->getSearchForm()->getQuery()
        );

        // Handle a virtual user selection
        $selected_virtual_user_id = $this->getRequest()->query->get(self::PARAM_VIRTUAL_USER_ID);

        if ($selected_virtual_user_id)
        {
            if (!$this->get_parent()->set_portfolio_virtual_user_id($selected_virtual_user_id))
            {
                $this->redirectWithMessage(
                    $translator->trans('ImpossibleToViewAsSelectedUser', [], Manager::CONTEXT), true
                );
            }
            else
            {
                $this->redirectWithMessage(
                    $translator->trans('ViewingPortfolioAsSelectedUser', [], Manager::CONTEXT), false,
                    [self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]
                );
            }
        }

        $html = [];
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderTable();

        $axtionBar = implode(PHP_EOL, $html);

        $html = [];

        $html[] = $this->render_header();
        $html[] = $axtionBar;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the component actionbar
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
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

    /**
     * @throws \Exception
     */
    public function getUserCondition(): AndCondition
    {
        $properties = [];
        $properties[] = new PropertyConditionVariable(
            User::class, User::PROPERTY_FIRSTNAME
        );
        $properties[] = new PropertyConditionVariable(
            User::class, User::PROPERTY_LASTNAME
        );
        $properties[] = new PropertyConditionVariable(
            User::class, User::PROPERTY_OFFICIAL_CODE
        );

        $searchConditions = $this->buttonToolbarRenderer->getConditions($properties);

        $conditions = [];

        if ($searchConditions instanceof Condition)
        {
            $conditions[] = $searchConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                User::class, User::PROPERTY_PLATFORMADMIN
            ), new StaticConditionVariable(0)
        );
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    User::class, User::PROPERTY_OFFICIAL_CODE
                ), new StaticConditionVariable('')
            )
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                User::class, User::PROPERTY_ACTIVE
            ), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    public function getUserTableRenderer(): UserTableRenderer
    {
        return $this->getService(UserTableRenderer::class);
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->get_parent()->count_portfolio_possible_view_users($this->getUserCondition());

        $userTableRenderer = $this->getUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $userTableRenderer->getParameterNames(), $userTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->get_parent()->retrieve_portfolio_possible_view_users(
            $this->getUserCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $userTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $userTableRenderer->render($tableParameterValues, $users);
    }
}
