<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\RequestTableRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 * @author  Tom Goethals - Hogeschool Gent
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->validateAccess();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->as_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function as_html(): string
    {
        $html = [];

        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans(
                        'AddDocument', [], $this::CONTEXT
                    ), new FontAwesomeGlyph('plus'), $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_PUBLISH_DOCUMENT
                    ]
                ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \Exception
     */
    public function getRequestCondition(): Condition
    {
        $search_conditions = $this->getButtonToolbarRenderer()->getConditions(
            [
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
            ]
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id())
        );

        if ($search_conditions != null)
        {
            $condition = new AndCondition([$condition, $search_conditions]);
        }

        return $condition;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRequestTableRenderer(): RequestTableRenderer
    {
        return $this->getService(RequestTableRenderer::class);
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
        $totalNumberOfItems = $this->getRequestManager()->countRequestsWithContentObjects($this->getRequestCondition());
        $requestTableRenderer = $this->getRequestTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $requestTableRenderer->getParameterNames(), $requestTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $orderBy = $requestTableRenderer->determineOrderBy($tableParameterValues);
        $orderBy->add(new OrderProperty(new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME)));

        $requests = $this->getRequestManager()->findRequestsWithContentObjects(
            new RecordRetrievesParameters(
                null, $this->getRequestCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $orderBy
            )
        );

        return $requestTableRenderer->legacyRender($this, $tableParameterValues, $requests);
    }
}
