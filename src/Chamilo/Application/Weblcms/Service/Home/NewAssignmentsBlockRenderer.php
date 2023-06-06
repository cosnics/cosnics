<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewAssignmentsBlockRenderer extends NewBlockRenderer
{

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::displayNewItem()
     */
    public function displayNewItem($publication)
    {
        if ($publication[ContentObject::PROPERTY_TYPE] != Assignment::class)
        {
            return;
        }

        return parent::displayNewItem($publication);
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getBadgeContent()
     */
    public function getBadgeContent($publication)
    {
        $content_object = DataManager::retrieve_by_id(
            Assignment::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
        );

        return '<span class="badge badge-date">' . date('j M Y', $content_object->get_start_time()) . ' - ' .
            date('j M Y', $content_object->get_end_time()) . '</span>';
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getContentObjectTypes()
     */
    public function getContentObjectTypes()
    {
        return [Assignment::class];
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
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => NewBlockRenderer::TOOL_ASSIGNMENT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
            Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
        ];

        return $this->getLink($parameters);
    }

    /**
     * @see \Chamilo\Application\Weblcms\Service\Home\NewBlockRenderer::getToolName()
     */
    public function getToolName()
    {
        return 'Assignment';
    }
}
