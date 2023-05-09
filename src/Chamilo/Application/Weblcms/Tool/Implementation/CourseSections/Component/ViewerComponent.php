<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Table\CourseSectionsTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<br />';
        $html[] = $this->getButtonToolbarRenderer()->render();

        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $this->renderTable();
        $html[] = '</div>';

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
                    $translator->trans('Create', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('share-square'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE_COURSE_SECTION]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
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

    public function getCourseSectionsCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id())
        );
    }

    public function getCourseSectionsTableRenderer(): CourseSectionsTableRenderer
    {
        return $this->getService(CourseSectionsTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count(
            CourseSection::class, new DataClassCountParameters($this->getCourseSectionsCondition())
        );

        $courseSectionsTableRenderer = $this->getCourseSectionsTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseSectionsTableRenderer->getParameterNames(),
            $courseSectionsTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $courseSections = DataManager::retrieves(
            CourseSection::class, new DataClassRetrievesParameters(
                $this->getCourseSectionsCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(),
                $courseSectionsTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $courseSectionsTableRenderer->render($tableParameterValues, $courseSections);
    }
}
