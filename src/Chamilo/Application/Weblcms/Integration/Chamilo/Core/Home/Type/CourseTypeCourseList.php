<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\FilteredCourseListRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a block that shows courses of a specific type
 */
class CourseTypeCourseList extends Block
{

    private $renderer;

    private $course_type;

    function display_content()
    {
        $configuration = $this->get_configuration();

        $html = array();
        $renderer = $this->get_renderer();

        if ($configuration['show_new_icons'])
        {
            $renderer->show_new_publication_icons();
        }

        $html[] = $renderer->as_html(false);

        if (! $configuration['show_new_icons'])
        {
            $courseTypeLink = new Redirect(array());

            $html[] = '<div style="margin-top: 5px;">';
            $html[] = Translation :: get('CheckWhatsNew', array('URL' => $courseTypeLink->getUrl()));
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    function get_title()
    {
        $course_type = $this->course_type;
        if ($course_type && ! is_null($course_type))
        {
            return $course_type->get_title();
        }
        else
        {
            return parent :: get_title();
        }
    }

    function get_renderer()
    {
        if ($this->renderer == null)
        {
            // $this->renderer = new SelectedCourseTypeCourseListRenderer($this, $this->get_link_target(),
            // $course_type);
            $this->renderer = new FilteredCourseListRenderer(
                $this,
                $this->get_link_target(),
                $this->get_course_type_id());
        }
        return $this->renderer;
    }

    function get_course_type()
    {
        $configuration = $this->get_configuration();
        if (isset($configuration['course_type']))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(CourseType :: class_name(), CourseType :: PROPERTY_ID),
                new StaticConditionVariable($configuration['course_type']));
            $this->course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve(
                CourseType :: class_name(),
                new DataClassRetrieveParameters($condition));
        }
        else
        {
            $this->course_type = null;
        }

        return $this->course_type;
    }

    /**
     * Returns the course type id of the selected course type or 0 if no course type is not selected
     *
     * @return int
     */
    protected function get_course_type_id()
    {
        $course_type = $this->get_course_type();
        return $course_type ? $course_type->get_id() : 0;
    }

    /**
     * We need to override this because else we would redirect to the home page
     *
     * @param $parameters
     */
    function get_link($parameters)
    {
        return $this->get_parent()->get_link($parameters);
    }

    function is_visible()
    {
        if ($this->get_user() instanceof \Chamilo\Core\User\Storage\DataClass\User)
        {
            if ($this->is_empty() && ! $this->show_when_empty())
            {
                return false;
            }

            return true; // i.e.display on homepage when anonymous
        }
        else
        {
            return false;
        }
    }

    function show_when_empty()
    {
        $configuration = $this->get_configuration();

        $result = isset($configuration['show_when_empty']) ? $configuration['show_when_empty'] : true;
        $result = (bool) $result;
        return $result;
    }

    function is_empty()
    {
        return $this->get_renderer()->get_courses()->size() <= 0;
    }

    function show_empty_courses()
    {
        return true;
    }
}

?>