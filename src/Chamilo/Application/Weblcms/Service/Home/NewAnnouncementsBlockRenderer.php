<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewAnnouncementsBlockRenderer extends NewBlockRenderer implements ConfigurableBlockInterface
{
    public const CONFIGURATION_SHOW_CONTENT = 'show_content';

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::displayContent()
     */
    public function displayContent()
    {
        if (!$this->getBlock()->getSetting(self::CONFIGURATION_SHOW_CONTENT, false))
        {
            $announcementUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
                    \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ANNOUNCEMENT
                ]
            );

            $html = [];

            $html[] =
                '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                '">';
            $html[] = Translation::get('ClickForAnnouncements', ['URL' => $announcementUrl]);
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return parent::displayContent();
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return [self::CONFIGURATION_SHOW_CONTENT];
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getContentObjectTypes()
     */
    public function getContentObjectTypes()
    {
        return [Announcement::class];
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getCourseViewerLink()
     */
    public function getCourseViewerLink(Course $course, $publication)
    {
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT] =
            \Chamilo\Application\Weblcms\Manager::CONTEXT;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course->get_id();
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = self::TOOL_ANNOUNCEMENT;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW;
        $parameters[Manager::PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication::PROPERTY_ID];

        return $this->getLink($parameters);
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getToolName()
     */
    public function getToolName()
    {
        return 'Announcement';
    }
}
