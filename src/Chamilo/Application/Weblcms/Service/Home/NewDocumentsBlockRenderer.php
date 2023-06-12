<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewDocumentsBlockRenderer extends NewBlockRenderer
{
    public const CONTEXT = \Chamilo\Application\Weblcms\Manager::CONTEXT;

    public function getContentObjectTypes(): array
    {
        return [File::class, Webpage::class];
    }

    public function getCourseViewerLink(array $publication): string
    {
        $parameters = [
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication[ContentObjectPublication::PROPERTY_COURSE_ID],
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => self::TOOL_DOCUMENT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager::ACTION_VIEW_DOCUMENTS,
            Manager::PARAM_BROWSER_TYPE => ContentObjectRenderer::TYPE_TABLE,
            Manager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID]
        ];

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getToolName(): string
    {
        return self::TOOL_DOCUMENT;
    }
}
