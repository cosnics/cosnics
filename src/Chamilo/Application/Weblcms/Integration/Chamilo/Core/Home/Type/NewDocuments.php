<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * A notification block for new document submissions (document tool) based on new_assignments.class.php
 *
 * @author Parcifal Aertssen (Howest)
 */
class NewDocuments extends NewBlock
{

    public function displayNewItem($publication)
    {
        $html = array();

        $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
        $title = $publication[ContentObject :: PROPERTY_TITLE];
        $link = $this->getCourseViewerLink($this->getCourseById($course_id), $publication);

        $html[] = '<a href="' . $link . '" class="list-group-item">';
        $html[] = $this->getBadgeContent($publication);
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
        return array(File :: class_name(), Webpage :: class_name());
    }

    /**
     *
     * @see \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock::getToolName()
     */
    public function getToolName()
    {
        return 'Document';
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
            \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => 'document',
            \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager :: ACTION_VIEW_DOCUMENTS,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE,
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID]);

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }
}
