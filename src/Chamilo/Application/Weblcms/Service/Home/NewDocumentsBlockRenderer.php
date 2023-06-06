<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewDocumentsBlockRenderer extends NewBlockRenderer
{

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getContentObjectTypes()
     */
    public function getContentObjectTypes()
    {
        return [File::class, Webpage::class];
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getCourseViewerLink()
     */
    public function getCourseViewerLink(Course $course, $publication)
    {
        $parameters = [
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->get_id(),
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'document',
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager::ACTION_VIEW_DOCUMENTS,
            Manager::PARAM_BROWSER_TYPE => ContentObjectRenderer::TYPE_TABLE,
            Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
        ];

        return $this->getLink($parameters);
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getToolName()
     */
    public function getToolName()
    {
        return 'Document';
    }
}