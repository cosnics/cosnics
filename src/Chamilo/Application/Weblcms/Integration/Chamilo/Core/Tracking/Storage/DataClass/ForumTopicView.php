<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

class ForumTopicView extends SimpleTracker
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_forum_topic_id($parameters[self::PROPERTY_FORUM_TOPIC_ID]);
        $this->set_date(time());
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_USER_ID, 
                self::PROPERTY_PUBLICATION_ID, 
                self::PROPERTY_FORUM_TOPIC_ID, 
                self::PROPERTY_DATE));
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    public function get_forum_topic_id()
    {
        return $this->get_default_property(self::PROPERTY_FORUM_TOPIC_ID);
    }

    public function set_forum_topic_id($forum_topic_id)
    {
        $this->set_default_property(self::PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    public function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'tracking_weblcms_forum_topic_view';
    }
}
