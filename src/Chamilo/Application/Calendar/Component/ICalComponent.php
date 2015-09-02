<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Sabre\VObject\Component\VCalendar;
use Chamilo\Libraries\Calendar\TimeZone\TimeZoneCalendarWrapper;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules\VObjectRecurrenceRulesFormatter;
use Chamilo\Libraries\Format\Response\Response;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager implements NoAuthenticationSupport
{

    public function run()
    {
        $authenticationValidator = new AuthenticationValidator(Configuration :: get_instance());

        if (! $authenticationValidator->isAuthenticated())
        {
            $authentication = Authentication :: factory('SecurityToken');
            $user = $authentication->check_login();

            if ($user instanceof User)
            {
                $this->getApplicationConfiguration()->setUser($user);
                $this->renderCalendar();
                $authentication->logout($user);
            }
            else
            {
                $response = new \Symfony\Component\HttpFoundation\Response();
                $response->setStatusCode(401);
                $response->send();
            }
        }
        else
        {
            if ($this->getRequest()->query->get(self :: PARAM_DOWNLOAD))
            {
                $this->renderCalendar();
            }
            else
            {
                $icalDownloadUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => self :: package(),
                        self :: PARAM_ACTION => Manager :: ACTION_ICAL,
                        self :: PARAM_DOWNLOAD => 1));

                $icalExternalUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => self :: package(),
                        self :: PARAM_ACTION => Manager :: ACTION_ICAL,
                        User :: PROPERTY_SECURITY_TOKEN => $this->getUser()->get_security_token()));

                $html = array();

                $html[] = $this->render_header();

                $html[] = $icalDownloadUrl->getUrl();
                $html[] = $icalExternalUrl->getUrl();

                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    private function renderCalendar()
    {
        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            array(),
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $providedEvents = $dataProvider->getAllEvents();

        $calendar = new VCalendar();

        // Add the correct timezone information from 1970 until 2038
        \iCalUtilityFunctions :: createTimezone(
            new TimeZoneCalendarWrapper($calendar),
            date_default_timezone_get(),
            array(),
            1,
            2145916799);

        foreach ($providedEvents as $providedEvent)
        {
            $event = $calendar->add('VEVENT');

            $event->add(
                'DTSTART',
                new \DateTime(
                    date('Y-m-d\TH:i:s', $providedEvent->getStartDate()),
                    new \DateTimeZone(date_default_timezone_get())));

            $event->add(
                'DTEND',
                new \DateTime(
                    date('Y-m-d\TH:i:s', $providedEvent->getEndDate()),
                    new \DateTimeZone(date_default_timezone_get())));

            $description = trim(preg_replace('/\s\s+/', '\\n', strip_tags($providedEvent->getContent())));

            $event->add('SUMMARY', trim($providedEvent->getTitle()));
            $event->add('DESCRIPTION', $description);

            $event->add(
                'CREATED',
                new \DateTime(date('Y-m-d\TH:i:s', time()), new \DateTimeZone(date_default_timezone_get())));

            $event->add(
                'DTSTAMP',
                new \DateTime(date('Y-m-d\TH:i:s', time()), new \DateTimeZone(date_default_timezone_get())));

            $uniqueIdentifiers = array($providedEvent->getSource(), $providedEvent->getId());

            $event->add('UID', md5(serialize($uniqueIdentifiers)));
            $event->add('URL', $providedEvent->getUrl());

            $vObjectRecurrenceRulesFormatter = new VObjectRecurrenceRulesFormatter();

            $event->add('RRULE', $vObjectRecurrenceRulesFormatter->format($providedEvent->getRecurrenceRules()));
        }

        $response = new Response($calendar->serialize());
        $response->headers->set('Content-Type', 'text/calendar');
        $response->send();
    }
}