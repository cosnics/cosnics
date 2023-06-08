<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewAnnouncementsBlockRenderer extends NewBlockRenderer implements ConfigurableBlockInterface
{
    public const CONFIGURATION_SHOW_CONTENT = 'show_content';

    public function displayContent(Element $block, ?User $user = null): string
    {
        if (!$block->getSetting(self::CONFIGURATION_SHOW_CONTENT, false))
        {
            $announcementUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_ANNOUNCEMENT
                ]
            );

            $html = [];

            $html[] = '<div class="panel-body portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
            $html[] = $this->getTranslator()->trans('ClickForAnnouncements', ['URL' => $announcementUrl],
                \Chamilo\Application\Weblcms\Manager::CONTEXT);
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return parent::displayContent($block, $user);
    }

    public function getConfigurationVariables(): array
    {
        return [self::CONFIGURATION_SHOW_CONTENT];
    }

    public function getContentObjectTypes(): array
    {
        return [Announcement::class];
    }

    public function getCourseViewerLink(array $publication): string
    {
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::CONTEXT;
        $parameters[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] =
            $publication[ContentObjectPublication::PROPERTY_COURSE_ID];
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = self::TOOL_ANNOUNCEMENT;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW;
        $parameters[Manager::PARAM_PUBLICATION_ID] = $publication[DataClass::PROPERTY_ID];

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getToolName(): string
    {
        return self::TOOL_ANNOUNCEMENT;
    }
}
