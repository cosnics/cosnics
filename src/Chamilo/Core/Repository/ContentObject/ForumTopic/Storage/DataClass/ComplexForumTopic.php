<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Forum\EmailNotification\TopicEmailNotificator;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: complex_forum_topic.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.forum_topic
 * @author Mattias De Pauw - Hogeschool Gent
 */
class ComplexForumTopic extends ComplexContentObjectItem
{
    const PROPERTY_FORUM_TYPE = 'forum_type';

    public function get_forum_type()
    {
        return $this->get_additional_property(self::PROPERTY_FORUM_TYPE);
    }

    public function set_forum_type($type)
    {
        $this->set_additional_property(self::PROPERTY_FORUM_TYPE, $type);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_FORUM_TYPE);
    }

    public function create()
    {
        parent::create();
        
        $email_notificator = new TopicEmailNotificator();
        
        $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $this->get_ref());
        
        $parent = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $this->get_parent());
        
        $email_notificator->set_forum($parent);
        $email_notificator->set_topic($lo);
        $text = Translation::get("TopicAddedEmailTitle", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
        $email_notificator->set_action_title($text);
        $text = Translation::get("TopicAddedEmailBody", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
        $email_notificator->set_action_body($text);
        $email_notificator->set_action_user(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                (int) Session::get_user_id()));
        
        $parent->add_topic();
        $parent->add_post($lo->get_total_posts(), $email_notificator);
        $parent->recalculate_last_post();
        
        $email_notificator->send_emails();
        
        return true;
    }

    public function delete()
    {
        parent::delete();
        
        $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $this->get_ref());
        
        $parent = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $this->get_parent());
        $parent->remove_topic();
        $parent->remove_post($lo->get_total_posts());
        
        $parent->recalculate_last_post();
        
        return true;
    }

    public function get_allowed_types()
    {
        return array(ForumPost::class_name());
    }
}
