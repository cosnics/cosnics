<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequestTableRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Assignment browser component for ephorus tool.
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and displays it's output
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->as_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns this component as html
     *
     * @return string
     */
    protected function as_html()
    {
        $html = [];

        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the actionbar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \Exception
     */
    public function getEntryRequestCondition(): ?AndCondition
    {
        return $this->getButtonToolbarRenderer()->getConditions(
            [
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
            ]
        );
    }

    public function getEntryRequestTableRenderer(): EntryRequestTableRenderer
    {
        return $this->getService(EntryRequestTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function renderTable(): string
    {
        $totalNumberOfItems =
            $this->getDataProvider()->countAssignmentEntriesWithEphorusRequests($this->getEntryRequestCondition());
        $entryRequestTableRenderer = $this->getEntryRequestTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $entryRequestTableRenderer->getParameterNames(), $entryRequestTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $orderBy = $entryRequestTableRenderer->determineOrderBy($tableParameterValues);
        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)
            )
        );

        $entries = $this->getDataProvider()->findAssignmentEntriesWithEphorusRequests(
            new RecordRetrievesParameters(
                null, $this->getEntryRequestCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $orderBy
            )
        );

        return $entryRequestTableRenderer->render($tableParameterValues, $entries);
    }
}
