<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarRendererProvider extends \Chamilo\Libraries\Calendar\Service\CalendarRendererProvider
{

    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    private $course;

    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    private $publicationService;

    /**
     * @var string
     */
    private $tool;

    public function __construct(
        PublicationService $publicationService, Course $course, $tool, User $dataUser, $displayParameters
    )
    {
        parent::__construct($dataUser, $displayParameters);

        $this->publicationService = $publicationService;
        $this->course = $course;
        $this->tool = $tool;
    }

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function aggregateEvents(?int $startTime = null, ?int $endTime = null): array
    {
        $publications = $this->getPublicationService()->getPublicationsForUser(
            $this->getDataUser(), $this->getCourse(), $this->getTool()
        );
        $events = [];

        foreach ($publications as $publication)
        {
            $eventParser = new EventParser($publication, $startTime, $endTime);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\PublicationService
     */
    public function getPublicationService()
    {
        return $this->publicationService;
    }

    /**
     * @return string
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @see \Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface::getUrl()
     */
    public function getUrl($parameters = [], $filterParameters = [], $encodeEntities = false)
    {
        $redirect = new Redirect($parameters, $filterParameters, $encodeEntities);

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     * @param string $tool
     */
    public function setTool($tool)
    {
        $this->tool = $tool;
    }
}