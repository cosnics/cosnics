<?php
namespace Chamilo\Configuration\Form\Component;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Configuration\Form\Table\ElementTableRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Configuration\Form\Component
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BuilderComponent extends Manager
{

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->display_element_types();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function display_element_types(): string
    {
        $buttonToolbar = new ButtonToolBar();

        foreach (Element::get_types() as $typename => $typevalue)
        {
            $link = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_ADD_FORM_ELEMENT,
                    self::PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $typevalue
                ]
            );

            $buttonToolbar->addItem(
                new Button(
                    $typename, Element::getTypeGlyph($typevalue), $link, AbstractButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolbar);

        return $buttonToolBarRenderer->render();
    }

    public function getElementCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($this->get_form()->get_id())
        );
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(Element::class, new DataClassCountParameters($this->getElementCondition()));
        $elementTableRenderer = $this->getElementTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $elementTableRenderer->getParameterNames(), $elementTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $elements = DataManager::retrieves(
            Element::class, new DataClassRetrievesParameters(
                $this->getElementCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $elementTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $elementTableRenderer->render($tableParameterValues, $elements);
    }
}
