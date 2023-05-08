<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Table\CourseTableRenderer;
use Chamilo\Application\Weblcms\Menu\CourseCategoryMenu;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes a browser for the courses
 *
 * @package \application\weblcms\course
 * @author  Yannick & Tristan
 * @author  Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseComponent extends Manager
{
    public const PARAM_CATEGORY_ID = 'category_id';

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $this->checkComponentAuthorization();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->get_html();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkComponentAuthorization()
    {
        $this->checkAuthorization(\Chamilo\Application\Weblcms\Manager::CONTEXT, 'ManageCourses');
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            $translator = $this->getTranslator();

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_create_course_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
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
    public function getCourseCondition(): ?AndCondition
    {
        $conditions = [];

        $category_id = $this->getRequest()->query->get(self::PARAM_CATEGORY_ID);

        if ($category_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($category_id)
            );
        }

        $search_condition = $this->getButtonToolbarRenderer()->getConditions(
            [
                new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE),
                new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE)
            ]
        );

        if ($search_condition)
        {
            $conditions[] = $search_condition;
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    public function getCourseTableRenderer(): CourseTableRenderer
    {
        return $this->getService(CourseTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function get_html(): string
    {
        $html = [];

        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';

        $temp_replacement = '__CATEGORY_ID__';

        $url_format = $this->get_url([self::PARAM_CATEGORY_ID => $temp_replacement]);

        $category_menu = new CourseCategoryMenu($this->getRequest()->query->get(self::PARAM_CATEGORY_ID), $url_format);

        $html[] = '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">';
        $html[] = $category_menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $this->renderTable();
        $html[] = '</div>';

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function get_parameters(): array
    {
        $parameters = parent::get_parameters();
        $parameters[self::PARAM_CATEGORY_ID] = $this->getRequest()->query->get(self::PARAM_CATEGORY_ID);

        if (isset($this->buttonToolbarRenderer))
        {
            $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
                $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();
        }

        return $parameters;
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
        $totalNumberOfItems = DataManager::count_courses_with_course_type($this->getCourseCondition());
        $courseTableRenderer = $this->getCourseTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseTableRenderer->getParameterNames(), $courseTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $courses = DataManager::retrieve_courses_with_course_type(
            $this->getCourseCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $courseTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $courseTableRenderer->render($tableParameterValues, $courses);
    }
}
