<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
    implements ActionSupport
{

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    private $renderer;

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param string[] $displayParameters ;
     */
    public function __construct(Application $renderer, User $dataUser, $displayParameters)
    {
        $this->renderer = $renderer;

        parent::__construct($dataUser, $displayParameters);
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function aggregateEvents(?int $startTime = null, ?int $endTime = null): array
    {
        $publications = $this->getRenderer()->get_publications();
        $events = [];

        foreach ($publications as $publication)
        {
            if (method_exists(
                $this->getRenderer()->get_parent(), 'convert_content_object_publication_to_calendar_event'
            ))
            {
                $object = $this->getRenderer()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication, $startTime, $endTime
                );
            }
            else
            {
                $class = $publication[ContentObject::PROPERTY_TYPE];
                $object = new $class($publication);
                $object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            }

            $publicationObject = new ContentObjectPublication();
            $publicationObject->setDefaultProperties($publication);
            $publicationObject->set_content_object($object);

            $eventParser = new EventParser($publicationObject, $startTime, $endTime);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    public function getEventActions(Event $event): array
    {
        $actions = [];

        if ($event->getContext() == \Chamilo\Application\Weblcms\Manager::CONTEXT)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getRenderer()->get_url(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE_PUBLICATION,
                        Manager::PARAM_PUBLICATION_ID => $event->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
            );

            $actions[] = new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->getRenderer()->get_url(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_PUBLICATION_ID => $event->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON, true
            );
        }

        return $actions;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @see \Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = [], $filterParameters = [])
    {
        return $this->getUrlGenerator()->fromParameters($parameters, $filterParameters);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     */
    public function setRenderer(BrowserComponent $renderer)
    {
        $this->renderer = $renderer;
    }
}