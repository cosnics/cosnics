<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Table\OpenCourseTable;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Component to browse the open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BrowseComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        $buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $table = new OpenCourseTable($this);
        $table->setSearchForm($buttonToolbarRenderer->getSearchForm());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $buttonToolbarRenderer->render();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            if ($this->isAuthorized(Manager::context(), 'ManageOpenCourses'))
            {
                $buttonToolbar->addItem(
                    new Button(
                        Translation::getInstance()->getTranslation('AddOpenCourses', null, Manager::context()),
                        new FontAwesomeGlyph('plus'),
                        $this->getCreateOpenCourseUrl(),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->buttonToolbarRenderer->getConditions(
            array(
                new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_TITLE),
                new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_VISUAL_CODE),
                new PropertyConditionVariable(CourseType::class_name(), CourseType::PROPERTY_TITLE)));
    }
}