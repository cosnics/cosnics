<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Application\Weblcms\CourseType\Table\CourseTypeTableRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes a browser for the course types
 *
 * @package \application\weblcms\course_type
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->get_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            $translator = $this->getTranslator();

            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_create_course_type_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
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

    /**
     * @throws \Exception
     */
    public function getCourseTypeCondition(): ?AndCondition
    {
        return $this->buttonToolbarRenderer->getConditions(
            [CourseType::PROPERTY_TITLE, CourseType::PROPERTY_DESCRIPTION]
        );
    }

    public function getCourseTypeTableRenderer(): CourseTypeTableRenderer
    {
        return $this->getService(CourseTypeTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function get_html(): string
    {
        $html = [];

        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
        $html[] = $this->renderTable();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(CourseType::class, new DataClassCountParameters($this->getCourseTypeCondition()));
        $courseTypeTableRenderer = $this->getCourseTypeTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseTypeTableRenderer->getParameterNames(), $courseTypeTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $orderBy = $courseTypeTableRenderer->determineOrderBy($tableParameterValues);

        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER)
            )
        );

        $parameters = new DataClassRetrievesParameters(
            $this->getCourseTypeCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $orderBy
        );

        $courseTypes = DataManager::retrieves(CourseType::class, $parameters);

        return $courseTypeTableRenderer->legacyRender($this, $tableParameterValues, $courseTypes);
    }
}
