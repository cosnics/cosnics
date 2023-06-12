<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class NewAssignmentsBlockRenderer extends NewBlockRenderer
{
    public const CONTEXT = \Chamilo\Application\Weblcms\Manager::CONTEXT;

    public function displayNewItem(array $publication): string
    {
        if ($publication[ContentObject::PROPERTY_TYPE] != Assignment::class)
        {
            return '';
        }

        return parent::displayNewItem($publication);
    }

    public function getBadgeContent($publication): string
    {
        $content_object = DataManager::retrieve_by_id(
            Assignment::class, $publication[Publication::PROPERTY_CONTENT_OBJECT_ID]
        );

        return '<span class="badge badge-date">' . date('j M Y', $content_object->get_start_time()) . ' - ' .
            date('j M Y', $content_object->get_end_time()) . '</span>';
    }

    public function getContentObjectTypes(): array
    {
        return [Assignment::class];
    }

    public function getCourseViewerLink(array $publication): string
    {
        $parameters = [
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication[ContentObjectPublication::PROPERTY_COURSE_ID],
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => self::TOOL_ASSIGNMENT,
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
            Manager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID]
        ];

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getToolName(): string
    {
        return self::TOOL_ASSIGNMENT;
    }
}
