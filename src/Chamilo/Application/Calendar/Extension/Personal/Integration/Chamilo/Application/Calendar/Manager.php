<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Manager implements CalendarInterface
{

    public function get_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $user_events = $this->getUserEvents($renderer, $from_date, $to_date);
        $shared_events = $this->getSharedEvents($renderer, $from_date, $to_date);
        return array_merge($user_events, $shared_events);
    }

    public function getUserEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER),
            new StaticConditionVariable($renderer->get_user()->get_id()));
        $publications = DataManager :: retrieves(
            Publication :: class_name(),
            new DataClassRetrievesParameters($condition));

        return $this->renderEvents($renderer, $publications, $from_date, $to_date);
    }

    public function getSharedEvents(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $events = array();
        $user_groups = $renderer->get_user()->get_groups(true);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(PublicationUser :: class_name(), PublicationUser :: PROPERTY_USER),
            new StaticConditionVariable($renderer->get_user()->get_id()));

        if (count($user_groups) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(PublicationGroup :: class_name(), PublicationGroup :: PROPERTY_GROUP_ID),
                $user_groups);
        }

        $share_condition = new OrCondition($conditions);

        $publisher_condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER),
                new StaticConditionVariable($renderer->get_user()->get_id())));

        $condition = new AndCondition($share_condition, $publisher_condition);
        $publications = Datamanager :: retrieve_shared_personal_calendar_publications($condition);

        return $this->renderEvents($renderer, $publications, $from_date, $to_date);
    }

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