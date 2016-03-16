<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * A notification block for new document submissions (document tool) based on new_assignments.class.php
 *
 * @author Parcifal Aertssen (Howest)
 */
class NewDocuments extends NewBlock
{

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
    }

    public function displayContent()
    {
        $publications = $this->getContent(self :: TOOL_DOCUMENT);

        if (count($publications) == 0)
        {
            $html = array();

            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation :: get('NoNewAnnouncementsSinceLastVisit');
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return $this->displayNewItems($publications);
    }

    public function displayNewItems($publications)
    {
        usort($publications, array($this, 'sortDocuments'));

        $html = array();

        $html[] = '<div class="list-group portal-block-content portal-block-new-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $id = $publication[ContentObjectPublication :: PROPERTY_ID];
            $title = $publication[ContentObject :: PROPERTY_TITLE];

            $parameters = array(
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $course_id,
                Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
                Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(),
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => 'document',
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager :: ACTION_VIEW_DOCUMENTS,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $id);

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $html[] = '<a href="' . $link . '" class="list-group-item">';
            $html[] = '<span class="badge badge-date">' .
                 date('j M', $publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]) . '</span>';
            $html[] = '<p class="list-group-item-text">' . $title . '</p>';
            $html[] = '<h5 class="list-group-item-heading">' . $this->getCourseById($course_id)->get_title() . '</h5>';

            $html[] = '</a>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $publicationLeft
     * @param string[] $publicationRight
     * @return integer
     */
    public function sortDocuments($publicationLeft, $publicationRight)
    {
        if ($publicationLeft[ContentObjectPublication :: PROPERTY_MODIFIED_DATE] ==
             $publicationRight[ContentObjectPublication :: PROPERTY_MODIFIED_DATE])
        {
            return 0;
        }
        elseif ($publicationLeft[ContentObjectPublication :: PROPERTY_MODIFIED_DATE] >
             $publicationRight[ContentObjectPublication :: PROPERTY_MODIFIED_DATE])
        {
            return - 1;
        }
        else
        {
            return 1;
        }
    }
}
