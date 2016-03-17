<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class NewAssignments extends NewBlock
{

    public function displayNewItem($publication)
    {
        if ($publication[ContentObject :: PROPERTY_TYPE] != Assignment :: class_name())
        {
            return;
        }

        $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            Assignment :: class_name(),
            $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

        $html = array();

        $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
        $title = htmlspecialchars($publication[ContentObject :: PROPERTY_TITLE]);
        $link = $this->getCourseViewerLink($this->getCourseById($course_id), $publication);

        $html[] = '<a href="' . $link . '" class="list-group-item">';
        $html[] = '<span class="badge badge-date">' . date('j M Y', $content_object->get_start_time()) . ' - ' .
             date('j M Y', $content_object->get_end_time()) . '</span>';
        $html[] = '<p class="list-group-item-text">' . $title . '</p>';
        $html[] = '<h5 class="list-group-item-heading">' . $this->getCourseById($course_id)->get_title() . '</h5>';

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock::getContentObjectTypes()
     */
    public function getContentObjectTypes()
    {
        return array(Assignment :: class_name());
    }

    /**
     *
     * @see \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock::getToolName()
     */
    public function getToolName()
    {
        return 'Assignment';
    }

    /**
     *
     * @see \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock::getCourseViewerLink()
     */
    public function getCourseViewerLink($course, $publication)
    {
        $parameters = array(
            \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $course->get_id(),
            Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
            Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(),
            \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => NewBlock :: TOOL_ASSIGNMENT,
            \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: ACTION_BROWSE_SUBMITTERS,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID]);

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}
