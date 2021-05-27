<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview;

use ArrayIterator;
use Chamilo\Libraries\Platform\Session\Session;

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
     * @var \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\PreviewStorage
     */
    private static $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $storage = $this->get_storage();

        if (!isset($storage))
        {
            $this->set_storage([]);
        }
    }

    /**
     *
     * @param DummyFeedback $feedback
     *
     * @return boolean
     */
    public function create_feedback(DummyFeedback $feedback)
    {
        $feedbacks = $this->get_feedbacks();
        $feedbacks[$feedback->get_content_object_id()][$feedback->get_complex_content_object_id()][$feedback->get_id(
        )] = $feedback;

        return $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
    }

    /**
     *
     * @param DummyNotification $notification
     *
     * @return boolean
     */
    public function create_notification(DummyNotification $notification)
    {
        $notifications = $this->get_notifications();
        $notifications[$notification->get_content_object_id()][$notification->get_complex_content_object_id()] =
            $notification;

        return $this->set_property(self::PROPERTY_NOTIFICATION, $notifications);
    }

    /**
     *
     * @param DummyFeedback $feedback
     *
     * @return boolean
     */
    public function delete_feedback(DummyFeedback $feedback)
    {
        $feedbacks = $this->get_feedbacks();
        unset(
            $feedbacks[$feedback->get_content_object_id()][$feedback->get_complex_content_object_id(
            )][$feedback->get_id()]
        );

        return $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
    }

    /**
     *
     * @param DummyNotification $notification
     *
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
     * @return \Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview\PreviewStorage
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new PreviewStorage();
        }

        return self::$instance;
    }

    /**
     *
     * @return DummyFeedback[]
     */
    public function get_feedbacks()
    {
        $feedbacks = $this->get_property(self::PROPERTY_FEEDBACK);

        if (!isset($feedbacks))
        {
            $feedbacks = [];
            $this->set_property(self::PROPERTY_FEEDBACK, $feedbacks);
        }

        return $feedbacks;
    }

    /**
     *
     * @return DummyNotification[]
     */
    public function get_notifications()
    {
        $notifications = $this->get_property(self::PROPERTY_NOTIFICATION);

        if (!isset($notifications))
        {
            $notifications = [];
            $this->set_property(self::PROPERTY_NOTIFICATION, $notifications);
        }

        return $notifications;
    }

    /**
     *
     * @param string $property
     *
     * @return mixed
     */
    public function get_property($property)
    {
        $data = $this->get_storage();

        return $data[$property];
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
     * Empty the storage
     *
     * @return boolean
     */
    public function reset()
    {
        return $this->set_storage([]);
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
     *
     * @return \ArrayIterator<DummyNotification>
     */
    public function retrieve_feedbacks($content_object_id, $complex_content_object_item_id)
    {
        $feedbacks = $this->get_feedbacks();

        return new ArrayIterator(
            array_reverse(array_values($feedbacks[$content_object_id][$complex_content_object_item_id]))
        );
    }

    /**
     *
     * @param int $content_object_id
     * @param int $complex_content_object_item_id
     *
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
     * @return \ArrayIterator
     */
    public function retrieve_notifications($content_object_id, $complex_content_object_item_id)
    {
        $notifications = $this->get_notifications();

        return new ArrayIterator(array($notifications[$content_object_id][$complex_content_object_item_id]));
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
     * @param mixed $data
     *
     * @return boolean
     */
    public function set_storage($data)
    {
        Session::register(__NAMESPACE__, serialize($data));

        return true;
    }

    /**
     *
     * @param DummyFeedback $attempt
     *
     * @return boolean
     */
    public function update_feedback(DummyFeedback $feedback)
    {
        return $this->create_feedback($feedback);
    }

    /**
     *
     * @param DummyNotification $attempt
     *
     * @return boolean
     */
    public function update_notification(DummyNotification $notification)
    {
        return $this->create_notification($notification);
    }
}