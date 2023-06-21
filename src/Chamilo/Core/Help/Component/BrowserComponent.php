<?php
namespace Chamilo\Core\Help\Component;

use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Table\ItemTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Help\Component
 */
class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \ReflectionException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $output = $this->get_user_html();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<br />' . $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $output;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar(
                $this->get_url([Manager::PARAM_HELP_ITEM => $this->get_help_item()])
            );
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url([Manager::PARAM_HELP_ITEM => $this->get_help_item()]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getItemCondition(): ?ContainsCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            return new ContainsCondition(
                new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER), $query
            );
        }

        return null;
    }

    public function getItemTableRenderer(): ItemTableRenderer
    {
        return $this->getService(ItemTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function get_help_item()
    {
        return $this->getRequest()->query->get(Manager::PARAM_HELP_ITEM, 0);
    }

    public function get_user_html(): string
    {
        $parameters = $this->get_parameters();
        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $html = [];
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $this->renderItemTable();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderItemTable(): string
    {
        $totalNumberOfItems = $this->count_help_items($this->getItemCondition());
        $itemTableRenderer = $this->getItemTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $itemTableRenderer->getParameterNames(), $itemTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $items = $this->retrieve_help_items(
            $this->getItemCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $itemTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $itemTableRenderer->render($tableParameterValues, $items);
    }
}
