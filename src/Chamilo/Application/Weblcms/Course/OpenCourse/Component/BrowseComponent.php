<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Table\OpenCourseTableRenderer;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Course\OpenCourse\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
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

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            if ($this->isAuthorized(Manager::CONTEXT, 'ManageOpenCourses'))
            {
                $buttonToolbar->addItem(
                    new Button(
                        $this->getTranslator()->trans('AddOpenCourses', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('plus'), $this->getCreateOpenCourseUrl(),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \Exception
     */
    public function getOpenCourseCondition(): ?AndCondition
    {
        return $this->buttonToolbarRenderer->getConditions(
            [
                new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE),
                new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE),
                new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE)
            ]
        );
    }

    public function getOpenCourseTableRenderer(): OpenCourseTableRenderer
    {
        return $this->getService(OpenCourseTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
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
            $this->getOpenCourseService()->countOpenCourses($this->getUser(), $this->getOpenCourseCondition());
        $openCourseTableRenderer = $this->getOpenCourseTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $openCourseTableRenderer->getParameterNames(), $openCourseTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $courses = $this->getOpenCourseService()->getOpenCourses(
            $this->getUser(), $this->getOpenCourseCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $openCourseTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $openCourseTableRenderer->render($tableParameterValues, $courses);
    }
}