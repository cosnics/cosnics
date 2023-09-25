<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Table\UserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Portfolio\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends TabComponent
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function build()
    {
        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->$this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
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
        $conditions = [];

        $searchConditions = $this->getButtonToolbarRenderer()->getConditions(
            [
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE)
            ]
        );

        if ($searchConditions)
        {
            $conditions[] = $searchConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_ACTIVE), new StaticConditionVariable(1)
        );

        return new AndCondition($conditions);
    }

    public function getUserTableRenderer(): UserTableRenderer
    {
        return $this->getService(UserTableRenderer::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getUserCondition());
        $userTableRenderer = $this->getUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $userTableRenderer->getParameterNames(), $userTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getUserCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $userTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $userTableRenderer->render($tableParameterValues, $users);
    }
}