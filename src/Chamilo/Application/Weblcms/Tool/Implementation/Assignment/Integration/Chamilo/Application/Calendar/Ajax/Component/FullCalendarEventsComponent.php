<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Ajax\Component;

use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 * @package
 *          Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FullCalendarEventsComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{
    const PARAM_USER_USER_ID = 'user_id';
    const PARAM_FROM_DATE = 'start';
    const PARAM_TO_DATE = 'end';

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider
     */
    private $calendarEventDataProvider;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $userCalendar;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $calendarEventDataProvider = $this->getCalendarEventDataProvider();

        $events = $calendarEventDataProvider->getEvents(
            $this->getCalendarRendererProvider(),
            CalendarRendererProvider::SOURCE_TYPE_BOTH,
            $this->getFromDate(),
            $this->getToDate());

        $eventsCollection = array();

        foreach ($events as $event)
        {
            $eventsCollection[] = array(
                'title' => $event->getTitle(),
                'url' => $event->getUrl(),
                'start' => date('c', $event->getStartDate()),
                'end' => date('c', $event->getEndDate()),
                'allDay' => $event->isAllDay());
        }

        $response = new JsonResponse($eventsCollection);
        $response->send();
        exit();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\AjaxManager::getRequiredPostParameters()
     */
    public function getRequiredPostParameters()
    {
        return array();
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider
     */
    protected function getCalendarEventDataProvider()
    {
        if (! isset($this->calendarEventDataProvider))
        {
            $this->calendarEventDataProvider = new CalendarEventDataProvider();
        }

        return $this->calendarEventDataProvider;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Service\CalendarRendererProvider
     */
    protected function getCalendarRendererProvider()
    {
        if (! isset($this->calendarRendererProvider))
        {
            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getService('chamilo.application.calendar.service.visibility_service'),
                \Chamilo\Application\Calendar\Manager::context(),
                $this->getUserCalendar(),
                $this->getDisplayParameters());
        }

        return $this->calendarRendererProvider;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserCalendar()
    {
        if (! isset($this->userCalendar))
        {
            if ($this->getUserIdForCalendar() == $this->getUser()->getId())
            {
                $this->setUserCalendar($this->getUser());
            }
            else
            {
                $this->setUserCalendar(
                    \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        User::class_name(),
                        $this->getUserIdForCalendar()));
            }
        }
        return $this->userCalendar;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $userCalendar
     */
    public function setUserCalendar(User $userCalendar)
    {
        $this->userCalendar = $userCalendar;
    }

    /**
     *
     * @return string
     */
    protected function getFromDate()
    {
        $dateTime = new \DateTime($this->getRequest()->query->get(self::PARAM_FROM_DATE));
        return $dateTime->getTimestamp();
    }

    /**
     *
     * @return string
     */
    protected function getToDate()
    {
        $dateTime = new \DateTime($this->getRequest()->query->get(self::PARAM_TO_DATE));
        return $dateTime->getTimestamp();
    }

    /**
     *
     * @return integer
     */
    public function getUserIdForCalendar()
    {
        return $this->getRequest()->query->get(self::PARAM_USER_USER_ID, $this->getUser()->getId());
    }

    /**
     *
     * @return string[]
     */
    protected function getDisplayParameters()
    {
        return array(self::PARAM_USER_USER_ID => $this->getUserCalendar()->getId());
    }
}