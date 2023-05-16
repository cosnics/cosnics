<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

class ForumTopicView extends SimpleTracker
{
    public const CONTEXT = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_DATE = 'date';
    public const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';
    public const PROPERTY_PUBLICATION_ID = 'publication_id';
    public const PROPERTY_USER_ID = 'user_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_FORUM_TOPIC_ID,
                self::PROPERTY_DATE
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_forum_topic_view';
    }

    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    public function get_forum_topic_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_FORUM_TOPIC_ID);
    }

    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    public function set_forum_topic_id($forum_topic_id)
    {
        $this->setDefaultProperty(self::PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function validate_parameters(array $parameters = [])
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_forum_topic_id($parameters[self::PROPERTY_FORUM_TOPIC_ID]);
        $this->set_date(time());
    }
}
