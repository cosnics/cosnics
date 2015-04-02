<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\CalendarInterface;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Libraries\Calendar\Event\EventParser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
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
        $user_events = $this->get_user_events($renderer, $from_date, $to_date);
        $shared_events = $this->get_shared_events($renderer, $from_date, $to_date);
        return array_merge($user_events, $shared_events);
    }

    public function get_user_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER), 
            new StaticConditionVariable($renderer->get_user()->get_id()));
        $publications = DataManager :: retrieves(
            Publication :: class_name(), 
            new DataClassRetrievesParameters($condition));
        
        return $this->render_events($renderer, $publications, $from_date, $to_date);
    }

    public function get_shared_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $from_date, $to_date)
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
        
        return $this->render_events($renderer, $publications, $from_date, $to_date);
    }

    public function render_events(\Chamilo\Libraries\Calendar\Renderer\Renderer $renderer, $publications, $from_date, 
        $to_date)
    {
        $events = array();
        
        while ($publication = $publications->next_result())
        {
            $publisher = $publication->get_publisher();
            $publishing_user = $publication->get_publication_publisher();
            
            $parser = EventParser :: factory(
                $publication->get_publication_object(), 
                $from_date, 
                $to_date, 
                Event :: class_name());
            
            $parsed_events = $parser->get_events();
            
            foreach ($parsed_events as &$parsed_event)
            {
                if ($publisher != $renderer->get_application()->get_user_id())
                {
                    $parsed_event->set_title($parsed_event->get_title() . ' [' . $publishing_user->get_fullname() . ']');
                }
                
                $parsed_event->set_id($publication->get_id());
                $parsed_event->set_context(\Chamilo\Application\Calendar\Extension\Personal\Manager :: context());
                
                $parameters = array();
                $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Application\Calendar\Extension\Personal\Manager :: context();
                $parameters[Application :: PARAM_ACTION] = \Chamilo\Application\Calendar\Extension\Personal\Manager :: ACTION_VIEW;
                $parameters[\Chamilo\Application\Calendar\Extension\Personal\Manager :: PARAM_PUBLICATION_ID] = $publication->get_id();
                $parsed_event->set_url($renderer->get_application()->get_url($parameters));
                
                $events[] = $parsed_event;
            }
        }
        
        return $events;
    }
}