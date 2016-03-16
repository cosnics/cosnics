<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class NewAssignments extends NewBlock
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
        $publications = $this->getContent(self :: TOOL_ASSIGNMENT);

        if (count($publications) == 0)
        {
            $html = array();

            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation :: get('NoNewAssignmentsSinceLastVisit');
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return $this->displayNewItems($publications);
    }

    public function displayNewItems($publications)
    {
        usort($publications, array($this, 'sortAssignments'));

        $html = array();

        $html[] = '<div class="list-group portal-block-content portal-block-new-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $id = $publication[ContentObjectPublication :: PROPERTY_ID];

            if ($publication[ContentObject :: PROPERTY_TYPE] != Assignment :: class_name())
            {
                continue;
            }

            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                Assignment :: class_name(),
                $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

            $parameters = array(
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $course_id,
                Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE,
                Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(),
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => NewBlock :: TOOL_ASSIGNMENT,
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: ACTION_BROWSE_SUBMITTERS,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $id);

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $start_date = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES),
                $content_object->get_start_time());
            $end_date = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES),
                $content_object->get_end_time());
            // $html[] = '<a href="' . $link . '">' . $icon . ' ' . $content_object->get_title() . '</a>: ' .
            // Translation :: get(
            // 'From') . ' ' . $start_date . ' ' . Translation :: get('Until') . ' ' . $end_date . '<br />';

            $html[] = '<a href="' . $link . '" class="list-group-item">';
            $html[] = '<span class="badge badge-date">' . date('j M', $content_object->get_start_time()) . ' - ' .
                 date('j M', $content_object->get_end_time()) . '</span>';
            $html[] = '<p class="list-group-item-text">' . $content_object->get_title() . '</p>';
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
    public function sortAssignments($publicationLeft, $publicationRight)
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
