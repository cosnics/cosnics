<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: new_announcements.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.block
 */

/**
 * This class represents a calendar repo_viewer component which can be used to browse through the possible learning
 * objects to publish.
 */
class NewAnnouncements extends NewBlock implements ConfigurableInterface
{
    const CONFIGURATION_SHOW_CONTENT = 'show_content';

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
        if (! $this->getBlock()->getSetting(self :: CONFIGURATION_SHOW_CONTENT, false))
        {
            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: package(),
                    \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_ANNOUNCEMENT));

            $html = array();

            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation :: get('ClickForAnnouncements', array('URL' => $redirect->getUrl()));
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        $publications = $this->getContent(self :: TOOL_ANNOUNCEMENT);

        if ($publications === self :: OVERSIZED_WARNING)
        {
            return $this->getOversizedWarning();
        }

        if (count($publications) == 0)
        {
            $html = array();

            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation :: get('NoNewAnnouncementsSinceLastVisit');
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        usort($publications, array($this, 'sortAnnouncements'));

        $html = array();

        $html[] = '<div class="list-group portal-block-content portal-block-new-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $title = htmlspecialchars($publication[ContentObject :: PROPERTY_TITLE]);
            $link = $this->getCourseViewerLink($this->getCourseById($course_id), $publication);

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

    private function getCourseViewerLink($course, $publication)
    {
        $id = $publication[ContentObjectPublication :: PROPERTY_ID];

        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course->get_id();
        $parameters[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = self :: TOOL_ANNOUNCEMENT;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $id;

        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(self :: CONFIGURATION_SHOW_CONTENT);
    }

    /**
     *
     * @param string[] $publicationLeft
     * @param string[] $publicationRight
     * @return integer
     */
    public function sortAnnouncements($publicationLeft, $publicationRight)
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
