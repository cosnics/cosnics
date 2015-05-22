<?php
namespace Chamilo\Core\Tracking\Action;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\Tracking\Storage\DataClass\EventRelTracker;
use Chamilo\Core\Tracking\Storage\DataClass\TrackerRegistration;
use Chamilo\Libraries\Platform\Translation;

/**
 * Extension of the generic installer for trackers
 * 
 * @author Hans De Bisschop
 */
abstract class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        if (! $this->register_trackers())
        {
            return $this->failed(Translation :: get('TrackingFailed', null, Manager :: context()));
        }
        
        return true;
    }

    /**
     * Registers the trackers, events and creates the storage units for the trackers
     */
    public function register_trackers()
    {
        $xml = $this->parse_events(static :: get_path() . 'Resources/Tracking/events.xml');
        
        if (! isset($xml['events']))
        {
            return true;
        }
        
        $registered_trackers = array();
        foreach ($xml['events'] as $event_name => $event_properties)
        {
            $event = new Event();
            $event->set_name($event_name);
            $event->set_active(true);
            $event->set_context($xml['context']);
            
            if (! $event->create())
            {
                $this->add_message(
                    self :: TYPE_ERROR, 
                    Translation :: get('EventCreationFailed', null, Manager :: context()) . ': <em>' .
                         $event_properties['name'] . '</em>');
                return false;
            }
            
            foreach ($event_properties['trackers'] as $tracker_name => $tracker_properties)
            {
                if (! array_key_exists($tracker_properties['name'], $registered_trackers))
                {
                    $tracker = new TrackerRegistration();
                    $tracker->set_tracker($tracker_properties['name']);
                    $tracker->set_context($xml['context']);
                    
                    if (! $tracker->create())
                    {
                        $this->add_message(
                            self :: TYPE_ERROR, 
                            Translation :: get('TrackerRegistrationFailed', null, Manager :: context()) . ': <em>' .
                                 $tracker_properties['name'] . '</em>');
                        return false;
                    }
                    
                    $registered_trackers[$tracker_properties['name']] = $tracker;
                }
                
                $event_rel_tracker = new EventRelTracker();
                $event_rel_tracker->set_tracker_id($registered_trackers[$tracker_properties['name']]->get_id());
                $event_rel_tracker->set_event_id($event->get_id());
                $event_rel_tracker->set_active(true);
                if ($event_rel_tracker->create())
                {
                    $this->add_message(
                        self :: TYPE_NORMAL, 
                        Translation :: get('TrackersRegisteredToEvent', null, Manager :: context()) . ': <em>' .
                             $event_properties['name'] . ' + ' . $tracker_properties['name'] . '</em>');
                }
                else
                {
                    $this->add_message(
                        self :: TYPE_ERROR, 
                        Translation :: get('TrackerRegistrationToEventFailed', null, Manager :: context()) . ': <em>' .
                             $event_properties['name'] . '</em>');
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     *
     * @param string $file
     */
    public function parse_events($file)
    {
        $doc = new \DOMDocument();
        $result = array();
        
        $doc->load($file);
        $object = $doc->getElementsByTagname('package')->item(0);
        $result['context'] = $object->getAttribute('context');
        
        // Get events
        $events = $doc->getElementsByTagname('event');
        $trackers = array();
        
        foreach ($events as $index => $event)
        {
            $event_name = $event->getAttribute('name');
            $trackers = array();
            
            // Get trackers in event
            $event_trackers = $event->getElementsByTagname('tracker');
            $attributes = array('name', 'active');
            
            foreach ($event_trackers as $index => $event_tracker)
            {
                $property_info = array();
                
                foreach ($attributes as $index => $attribute)
                {
                    if ($event_tracker->hasAttribute($attribute))
                    {
                        $property_info[$attribute] = $event_tracker->getAttribute($attribute);
                    }
                }
                $trackers[$event_tracker->getAttribute('name')] = $property_info;
            }
            
            $result['events'][$event_name]['name'] = $event_name;
            $result['events'][$event_name]['trackers'] = $trackers;
        }
        
        return $result;
    }
}
