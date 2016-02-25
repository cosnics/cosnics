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
use Chamilo\Core\Home\Architecture\ConfigurableInterface;

/**
 * This class represents a block that shows courses of a specific type
 */
class CourseTypeCourseList extends Block implements ConfigurableInterface
{
    const CONFIGURATION_SHOW_NEW_ICONS = 'show_new_icons';
    const CONFIGURATION_SHOW_WHEN_EMPTY = 'show_when_empty';
    const CONFIGURATION_COURSE_TYPE = 'course_type';

    private $courseRenderer;

    private $courseType;

//    public function toHtml($view = '')
//    {
//        if ($this->getCourseRenderer()->get_courses()->size() > 0)
//        {
//            return parent :: toHtml($view);
//        }
//        else
//        {
//            return '';
//        }
//    }

    function displayContent()
    {
        $html = array();
        $renderer = $this->getCourseRenderer();

        if ($this->getBlock()->getSetting(self :: CONFIGURATION_SHOW_NEW_ICONS))
        {
            $renderer->show_new_publication_icons();
        }

        $html[] = $renderer->as_html(false);

        if ($this->getBlock()->getSetting(self :: CONFIGURATION_SHOW_WHEN_EMPTY, true))
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
        $courseType = $this->courseType;
        if ($courseType && ! is_null($courseType))
        {
            return $courseType->get_title();
        }
        else
        {
            return parent :: get_title();
        }
    }

    function getCourseRenderer()
    {
        if ($this->courseRenderer == null)
        {
            // $this->renderer = new SelectedCourseTypeCourseListRenderer($this, $this->get_link_target(),
            // $courseType);
            $this->courseRenderer = new FilteredCourseListRenderer(
                $this,
                '_blank',
                $this->getCourseTypeId());
        }
        return $this->courseRenderer;
    }

    function getCourseType()
    {
        $courseTypeId = $this->getBlock()->getSetting(self :: CONFIGURATION_COURSE_TYPE);

        if ($courseTypeId)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(CourseType :: class_name(), CourseType :: PROPERTY_ID),
                new StaticConditionVariable($courseTypeId));
            $this->courseType = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve(
                CourseType :: class_name(),
                new DataClassRetrieveParameters($condition));
        }
        else
        {
            $this->courseType = null;
        }

        return $this->courseType;
    }

    /**
     * Returns the course type id of the selected course type or 0 if no course type is not selected
     *
     * @return int
     */
    protected function getCourseTypeId()
    {
        $courseType = $this->getCourseType();
        return $courseType ? $courseType->get_id() : 0;
    }

    function isVisible()
    {
        if ($this->getUser() instanceof \Chamilo\Core\User\Storage\DataClass\User)
        {
            if ($this->isEmpty() && ! $this->showWhenEmpty())
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

    function showWhenEmpty()
    {
        return $this->getBlock()->getSetting(self :: CONFIGURATION_SHOW_WHEN_EMPTY, true);
    }

    function isEmpty()
    {
        return $this->getCourseRenderer()->get_courses()->size() <= 0;
    }

    function showEmptyCourses()
    {
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(
            self :: CONFIGURATION_SHOW_NEW_ICONS,
            self :: CONFIGURATION_SHOW_WHEN_EMPTY,
            self :: CONFIGURATION_COURSE_TYPE);
    }
}