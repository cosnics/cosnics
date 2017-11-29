<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    private $renderer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string[] $displayParameters;
     */
    public function __construct(Application $renderer, User $user, $displayParameters)
    {
        $this->renderer = $renderer;

        parent::__construct($user, $displayParameters);
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Action\Component\BrowserComponent $renderer
     */
    public function setRenderer(BrowserComponent $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param integer $sourceType
     * @param integer $startTime
     * @param integer $endTime
     */
    public function aggregateEvents($sourceType, $startTime, $endTime)
    {
        $publications = $this->getRenderer()->get_publications();
        $events = array();

        foreach ($publications as $publication)
        {

            if (method_exists(
                $this->getRenderer()->get_parent(),
                'convert_content_object_publication_to_calendar_event'))
            {
                $object = $this->getRenderer()->get_parent()->convert_content_object_publication_to_calendar_event(
                    $publication,
                    $startTime,
                    $endTime);
            }
            else
            {
                $class = $publication[ContentObject::PROPERTY_TYPE];
                $object = new $class($publication);
                $object->set_id($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            }

            $publicationObject = new ContentObjectPublication();
            $publicationObject->set_default_properties($publication);
            $publicationObject->set_content_object($object);

            $eventParser = new EventParser($publicationObject, $startTime, $endTime);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = array(), $filterParameters = array(), $encodeEntities = false)
    {
        $redirect = new Redirect($parameters, $filterParameters, $encodeEntities);
        return $redirect->getUrl();
    }
}