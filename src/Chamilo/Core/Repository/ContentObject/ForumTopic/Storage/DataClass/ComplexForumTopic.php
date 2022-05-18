<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Forum\EmailNotification\TopicEmailNotificator;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.forum_topic
 * @author Mattias De Pauw - Hogeschool Gent
 */
class ComplexForumTopic extends ComplexContentObjectItem
{
    const PROPERTY_FORUM_TYPE = 'forum_type';

    public function create()
    {
        parent::create();

        $email_notificator = new TopicEmailNotificator();

        $lo = DataManager::retrieve_by_id(
            ContentObject::class, $this->get_ref()
        );

        $parent = DataManager::retrieve_by_id(
            ContentObject::class, $this->get_parent()
        );

        $email_notificator->set_forum($parent);
        $email_notificator->set_topic($lo);
        $text = Translation::get("TopicAddedEmailTitle", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
        $email_notificator->set_action_title($text);
        $text = Translation::get("TopicAddedEmailBody", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
        $email_notificator->set_action_body($text);
        $email_notificator->set_action_user(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, (int) Session::get_user_id()
            )
        );

        $parent->add_topic();
        $parent->add_post($lo->get_total_posts(), $email_notificator);
        $parent->recalculate_last_post();

        $email_notificator->send_emails();

        return true;
    }

    public function delete()
    {
        parent::delete();

        $lo = DataManager::retrieve_by_id(
            ContentObject::class, $this->get_ref()
        );

        $parent = DataManager::retrieve_by_id(
            ContentObject::class, $this->get_parent()
        );
        $parent->remove_topic();
        $parent->remove_post($lo->get_total_posts());

        $parent->recalculate_last_post();

        return true;
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_FORUM_TYPE);
    }

    public function get_allowed_types()
    {
        return array(ForumPost::class);
    }

    public function get_forum_type()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FORUM_TYPE);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_complex_forum_topic';
    }

    public function set_forum_type($type)
    {
        $this->setAdditionalProperty(self::PROPERTY_FORUM_TYPE, $type);
    }
}
