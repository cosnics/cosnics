<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\CalendarNotFoundException;
use Exception;
use Microsoft\Graph\Generated\Models\Calendar;
use Microsoft\Graph\Generated\Users\Item\Calendars\Item\CalendarView\CalendarViewRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Calendars\Item\CalendarView\CalendarViewRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarRepository
{

    private GraphServiceClient $graphServiceClient;

    public function __construct(GraphServiceClient $graphServiceClient)
    {
        $this->graphServiceClient = $graphServiceClient;
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Event[]
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        string $userIdentifier, string $calendarIdentifier, int $fromDate, int $toDate
    ): array
    {
        try
        {
            $configuration = new CalendarViewRequestBuilderGetRequestConfiguration(
                queryParameters: new CalendarViewRequestBuilderGetQueryParameters(
                    count: true, endDateTime: date('c', $toDate), startDateTime: date('c', $fromDate), top: 600
                )
            );

            return $this->getGraphServiceClient()->users()->byUserId($userIdentifier)->calendars()->byCalendarId(
                $calendarIdentifier
            )->calendarView()->get($configuration)->wait()->getValue();
        }
        catch (Exception)
        {
            return [];
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\CalendarNotFoundException
     */
    public function getCalendarByIdentifier(string $userIdentifier, string $calendarIdentifier): Calendar
    {
        try
        {
            $calendar = $this->getGraphServiceClient()->users()->byUserId($userIdentifier)->calendars()->byCalendarId(
                $calendarIdentifier
            )->get()->wait();

            if (!$calendar instanceof Calendar)
            {
                throw new CalendarNotFoundException($userIdentifier, $calendarIdentifier);
            }

            return $calendar;
        }
        catch (Exception)
        {
            throw new CalendarNotFoundException($userIdentifier, $calendarIdentifier);
        }
    }

    protected function getGraphServiceClient(): GraphServiceClient
    {
        return $this->graphServiceClient;
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Calendar[]
     */
    public function listOwnedCalendars(string $azureUserIdentifier): array
    {
        try
        {
            return $this->getGraphServiceClient()->users()->byUserId($azureUserIdentifier)->calendars()->get()->wait()
                ->getValue();
        }
        catch (Exception)
        {
            return [];
        }
    }
}