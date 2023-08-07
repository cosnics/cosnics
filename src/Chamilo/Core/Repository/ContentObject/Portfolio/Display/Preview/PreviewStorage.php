<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview;

use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 *
 * @package core\repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PreviewStorage
{
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_NOTIFICATION = 'notification';

    /**
     *
     * @var \core\repository\content_object\portfolio\display\PreviewStorage
     */
    private static $instance;

    /**
     *
     * @return \core\repository\content_object\portfolio\display\PreviewStorage
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new PreviewStorage();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $storage = $this->get_storage();
        
        if (! isset($storage))
        {
            $this->set_storage(array());
        }
    }

    /**
     * Empty the storage
     * 
     * @return boolean
     */
    public function reset()
    {
        return $this->set_storage(array());
    }

    /**
     *
     * @return mixed
     */
    public function get_storage()
    {
        return unserialize(Session::retrieve(__NAMESPACE__));
    }

    /**
     *
     * @param mixed $data
     * @return boolean
     */
    public function set_storage($data)
    {
        Session::register(__NAMESPACE__, serialize($data));
        return true;
    }

    /**
     *
     * @param string $property
     * @param mixed $value
     */
    public function set_property($property, $value)
    {
        $data = $this->get_storage();
        $data[$property] = $value;
        return $this->set_storage($data);
    }

    /**
     *
     * @param string $property
     * @return mixed
     */
    public function get_property($property)
    {
        $data = $this->get_storage();
        return $data[$property];
    }

    /**
     *
     * @return DummyFeedback[]
     */
    public function get_feedbacks()
    {
        $feedbacks = $this->get_property(self::PROPERTY_FEEDBACK);
        
        if (! isset($feedbacks))
        {
            $feedbacks = array();
            $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
        }
        
        return $feedbacks;
    }

    /**
     *
     * @param DummyFeedback $feedback
     * @return boolean
     */
    public function create_feedback(DummyFeedback $feedback)
    {
        $feedbacks = $this->get_feedbacks();
        $feedbacks[$feedback->get_content_object_id()][$feedback->get_complex_content_object_id()][$feedback->get_id()] = $feedback;
        return $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
    }

    /**
     *
     * @param DummyFeedback $attempt
     * @return boolean
     */
    public function update_feedback(DummyFeedback $feedback)
    {
        return $this->create_feedback($feedback);
    }

    /**
     *
     * @param DummyFeedback $feedback
     * @return boolean
     */
    public function delete_feedback(DummyFeedback $feedback)
    {
        $feedbacks = $this->get_feedbacks();
        unset(
            $feedbacks[$feedback->get_content_object_id()][$feedback->get_complex_content_object_id()][$feedback->get_id()]);
        return $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
    }

    /**
     *
     * @param string $feedback_id
     */
    public function retrieve_feedback($feedback_id)
    {
        $feedbacks = $this->get_feedbacks();
        foreach ($feedbacks as $content_object_feedbacks)
        {
            foreach ($content_object_feedbacks as $complex_content_object_item_feedbacks)
            {
                foreach ($complex_content_object_item_feedbacks as $feedback)
                {
                    if ($feedback->get_id() == $feedback_id)
                    {
                        return $feedback;
                    }
                }
            }
        }
        return null;
    }

    /**
     *
     * @param int $content_object_id
     * @param int $complex_content_object_item_id
     * @return \libraries\storage\ArrayResultSet
     */
    public function retrieve_feedbacks($content_object_id, $complex_content_object_item_id)
    {
        $feedbacks = $this->get_feedbacks();

        if(!is_array($feedbacks) || !array_key_exists($content_object_id, $feedbacks) || !array_key_exists($complex_content_object_item_id, $feedbacks[$content_object_id]))
            return new ArrayResultSet([]);

        return new ArrayResultSet(
            array_reverse(array_values($feedbacks[$content_object_id][$complex_content_object_item_id])));
    }

    /**
     *
     * @return DummyNotification[]
     */
    public function get_notifications()
    {
        $notifications = $this->get_property(self::PROPERTY_NOTIFICATION);
        
        if (! isset($notifications))
        {
            $notifications = array();
            $this->set_property(self::PROPERTY_NOTIFICATION, $notifications);
        }
        
        return $notifications;
    }

    /**
     *
     * @param DummyNotification $notification
     * @return boolean
     */
    public function create_notification(DummyNotification $notification)
    {
        $notifications = $this->get_notifications();
        $notifications[$notification->get_content_object_id()][$notification->get_complex_content_object_id()] = $notification;
        return $this->set_property(self::PROPERTY_NOTIFICATION, $notifications);
    }

    /**
     *
     * @param DummyNotification $attempt
     * @return boolean
     */
    public function update_notification(DummyNotification $notification)
    {
        return $this->create_notification($notification);
    }

    /**
     *
     * @param DummyNotification $notification
     * @return boolean
     */
    public function delete_notification(DummyNotification $notification)
    {
        $notifications = $this->get_notifications();
        unset($notifications[$notification->get_content_object_id()][$notification->get_complex_content_object_id()]);
        return $this->set_property(self::PROPERTY_NOTIFICATION, $notifications);
    }

    /**
     *
     * @param int $content_object_id
     * @param int $complex_content_object_item_id
     * @return DummyNotification
     */
    public function retrieve_notification($content_object_id, $complex_content_object_item_id)
    {
        $notifications = $this->get_notifications();
        return $notifications[$content_object_id][$complex_content_object_item_id];
    }

    /**
     *
     * @param int $content_object_id
     * @param int $complex_content_object_item_id
     *
     * @return ResultSet
     */
    public function retrieve_notifications($content_object_id, $complex_content_object_item_id)
    {
        $notifications = $this->get_notifications();
        return new ArrayResultSet(array($notifications[$content_object_id][$complex_content_object_item_id]));
    }
}