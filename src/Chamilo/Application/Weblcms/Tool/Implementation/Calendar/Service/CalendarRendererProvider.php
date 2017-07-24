<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Renderer\Service\CalendarRendererProvider
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    private $publicationService;

    /**
     *
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    private $course;

    /**
     *
     * @var string
     */
    private $tool;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository $dataProviderRepository
     * @param \Chamilo\Core\User\Storage\DataClass\User $dataUser
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewingUser
     * @param string[] $displayParameters;
     */
    public function __construct(PublicationService $publicationService, Course $course, $tool, User $dataUser,
        User $viewingUser, $displayParameters)
    {
        parent::__construct($dataUser, $viewingUser, $displayParameters);

        $this->publicationService = $publicationService;
        $this->course = $course;
        $this->tool = $tool;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->publicationService;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }

    /**
     *
     * @return string
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     *
     * @param string $tool
     */
    public function setTool($tool)
    {
        $this->tool = $tool;
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
        $publications = $this->getPublicationService()->getPublicationsForUser(
            $this->getDataUser(),
            $this->getCourse(),
            $this->getTool());
        $events = array();

        foreach ($publications as $publication)
        {
            $publicationContentObjectType = $publication->get_optional_property(ContentObject::PROPERTY_TYPE);

            $publicationContentObject = new $publicationContentObjectType();
            $publicationContentObject->set_title($publication->get_optional_property(ContentObject::PROPERTY_TITLE));
            $publicationContentObject->set_description(
                $publication->get_optional_property(ContentObject::PROPERTY_DESCRIPTION));
            $publicationContentObject->set_type($publication->get_optional_property(ContentObject::PROPERTY_TYPE));
            $publicationContentObject->set_current($publication->get_optional_property(ContentObject::PROPERTY_CURRENT));
            $publicationContentObject->set_owner_id(
                $publication->get_optional_property(ContentObject::PROPERTY_OWNER_ID));
            $publicationContentObject->set_creation_date(
                $publication->get_optional_property(ContentObject::PROPERTY_CREATION_DATE));
            $publicationContentObject->set_modification_date(
                $publication->get_optional_property(ContentObjectPublication::CONTENT_OBJECT_MODIFICATION_DATE_ALIAS));

            $publication->set_content_object($publicationContentObject);

            $eventParser = new EventParser($publication, $startTime, $endTime);
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

    /**
     *
     * @return boolean
     */
    public function supportsVisibility()
    {
        return $this instanceof VisibilitySupport;
    }

    /**
     *
     * @return boolean
     */
    public function supportsActions()
    {
        return $this instanceof ActionSupport;
    }
}