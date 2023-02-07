<?php
namespace Chamilo\Core\Repository\UserView\Component;

use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Table\UserViewTableRenderer;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\UserView\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE]), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
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
     * @throws \QuickformException
     */
    public function getUserViewTableCondition(): AndCondition
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView::class, UserView::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );

        $query = $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $or_conditions = [];
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(UserView::class, UserView::PROPERTY_NAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(UserView::class, UserView::PROPERTY_DESCRIPTION), $query
            );
            $or_condition = new OrCondition($or_conditions);

            $and_conditions = [];
            $and_conditions[] = $condition;
            $and_conditions[] = $or_condition;

            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    public function getUserViewTableRenderer(): UserViewTableRenderer
    {
        return $this->getService(UserViewTableRenderer::class);
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
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getUserViewTableCondition());
        $userViewTableRenderer = $this->getUserViewTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $userViewTableRenderer->getParameterNames(), $userViewTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getUserViewTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $userViewTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $userViewTableRenderer->render($tableParameterValues, $users);
    }
}
