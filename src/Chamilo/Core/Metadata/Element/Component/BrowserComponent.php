<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Element\Table\Element\ElementTable;
use Chamilo\Core\Metadata\Element\Table\ElementTableRenderer;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        if (!$this->getSchemaId())
        {
            throw new NoObjectSelectedException(Translation::get('Schema', null, 'Chamilo\Core\Metadata\Schema'));
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $table = new ElementTable($this);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Create', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_CREATE,
                            \Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID => $this->getSchemaId()
                        ]
                    )
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getElementTableCondition(): AndCondition
    {
        $conditions = [];

        $searchCondition = $this->getButtonToolbarRenderer()->getConditions(
            [new PropertyConditionVariable(Element::class, Element::PROPERTY_NAME)]
        );

        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_SCHEMA_ID), ComparisonCondition::EQUAL,
            new StaticConditionVariable($this->getSchemaId())
        );

        return new AndCondition($conditions);
    }

    public function getElementTableRenderer(): ElementTableRenderer
    {
        return $this->getService(ElementTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(Element::class, new StorageParameters(condition: $this->getElementTableCondition()));
        $elementTableRenderer = $this->getElementTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $elementTableRenderer->getParameterNames(), $elementTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $orderBy = $elementTableRenderer->determineOrderBy($tableParameterValues);
        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(Element::class, Element::PROPERTY_DISPLAY_ORDER)
            )
        );

        $elements = DataManager::retrieves(
            Element::class, new StorageParameters(
                condition: $this->getElementTableCondition(), orderBy: $orderBy,
                count: $tableParameterValues->getOffset(), offset: $tableParameterValues->getNumberOfItemsPerPage()
            )
        );

        return $elementTableRenderer->render($tableParameterValues, $elements);
    }
}
