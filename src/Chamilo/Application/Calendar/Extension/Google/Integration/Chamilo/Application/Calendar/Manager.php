<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Utilities\UUID;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager implements CalendarInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\CalendarInterface::getEvents()
     */
    public function getEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $fromDate, $toDate)
    {
        return array();

        $result = $this->retrieveEvents();

        var_dump($result);
        exit();

        $eventTest = new Event(
            UUID :: v4(),
            time(),
            time() + 100000,
            null,
            null,
            'Google Drive Event',
            null,
            'Google Drive',
            __NAMESPACE__);

        return array($eventTest);
    }

    public function retrieveEvents()
    {
        $configuration = Configuration :: get_instance();
        $configurationContext = \Chamilo\Application\Calendar\Extension\Google\Manager :: context();

        $googleClient = new \Google_Client();
        $googleClient->setDeveloperKey($configuration->get_setting(array($configurationContext, 'developer_key')));

        $calendarClient = new \Google_Service_Calendar($googleClient);

        $googleClient->setClientId($configuration->get_setting(array($configurationContext, 'client_id')));
        $googleClient->setClientSecret($configuration->get_setting(array($configurationContext, 'client_secret')));
        $googleClient->setScopes('https://www.googleapis.com/auth/calendar.readonly');

        $googleClient->setAccessToken(LocalSetting :: get('token', $configurationContext));

        return $calendarClient->calendarList->listCalendarList(array('minAccessRole' => 'owner'));
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Renderer $renderer
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication[] $publications
     * @param integer $fromDate
     * @param integer $toDate
     */
    private function renderEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $publications, $fromDate,
        $toDate)
    {
        $events = array();

        while ($publication = $publications->next_result())
        {
            $eventParser = new EventParser($renderer, $publication, $fromDate, $toDate);
            $events = array_merge($events, $eventParser->getEvents());
        }

        return $events;
    }
}