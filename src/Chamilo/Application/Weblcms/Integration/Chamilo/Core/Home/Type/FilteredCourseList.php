<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a block to show the course list filtered in a given course type and optionally a given category
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilteredCourseList extends Block
{
    /**
     * **************************************************************************************************************
     * Parameters *
     * **************************************************************************************************************
     */
    const PARAM_COURSE_TYPE = 'course_type';

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The cached course type id
     *
     * @var int
     */
    private $course_type_id;

    /**
     * The cached user course category id
     *
     * @var int
     */
    private $user_course_category_id;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructs the
     *
     * @param mixed $parent
     * @param Block $block_info
     * @param BlockConfiguration $configuration
     */
    public function __construct($parent, $block_info, $configuration = null)
    {
        parent :: __construct($parent, $block_info, $configuration);

        $this->load_settings();
    }

    /**
     * Displays the content
     *
     * @return string
     */
    public function display_content()
    {
        $configuration = $this->get_configuration();

        $html = array();

        $renderer = new \Chamilo\Application\Weblcms\Renderer\CourseList\Type\FilteredCourseListRenderer(
            $this,
            $this->get_link_target(),
            $this->get_course_type_id(),
            $this->get_user_course_category_id());

        if ($configuration['show_new_icons'])
        {
            $renderer->show_new_publication_icons();
        }

        $html[] = $renderer->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * We need to override this because else we would redirect to the home page
     *
     * @param $parameters
     */
    public function get_link($parameters)
    {
        return $this->get_parent()->get_link($parameters);
    }

    /**
     * Returns the title of this block Changes the default title of the block to the title of the course type and
     * (optionally) the title of the selected user course category
     *
     * @return string
     */
    public function get_title()
    {
        $course_type_id = $this->get_course_type_id();

        if ($course_type_id > 0)
        {
            $course_type = CourseTypeDataManager :: retrieve_by_id(CourseType :: class_name(), $course_type_id);
            if ($course_type)
            {
                $course_type_title = $course_type->get_title();
            }
            else
            {
                return Translation :: get('NoSuchCourseType');
            }
        }
        else
        {
            $course_type_title = Translation :: get('NoCourseType');
        }

        $user_course_category_id = $this->get_user_course_category_id();

        if ($user_course_category_id > 0)
        {

            $course_user_category = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory :: class_name(),
                $user_course_category_id);

            if ($course_user_category)
            {
                $course_user_category_title = ' - ' . $course_user_category->get_title();
            }
        }

        return $course_type_title . $course_user_category_title;
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the selected course type id
     *
     * @return int
     */
    public function get_course_type_id()
    {
        return $this->course_type_id;
    }

    /**
     * Returns the selected user course category id (if any)
     *
     * @return int
     */
    public function get_user_course_category_id()
    {
        return $this->user_course_category_id;
    }

    /**
     * Loads the settings of this block
     */
    private function load_settings()
    {
        $configuration = $this->get_configuration();

        $selected_course_type = $configuration[self :: PARAM_COURSE_TYPE];
        $exploded_value = json_decode($selected_course_type);

        $this->course_type_id = $exploded_value[0];
        $this->user_course_category_id = $exploded_value[1];
    }
}
