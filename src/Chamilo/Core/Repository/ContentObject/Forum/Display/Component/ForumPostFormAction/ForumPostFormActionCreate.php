<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component\ForumPostFormAction;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Common functions off the create and quote forms
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 */
abstract class ForumPostFormActionCreate extends ForumPostFormAction
{

    /**
     * Checks whether the type is a create and post and makes it.
     * 
     * @param int $type
     *
     * @return succes
     */
    public function create_forum_post($type)
    {
        $values = $this->form->exportValues();
        
        $this->forumpost->set_title($values[ForumPost::PROPERTY_TITLE]);
        $this->forumpost->set_content($values[ForumPost::PROPERTY_CONTENT]);
        $this->forumpost->set_user_id($this->get_user_id());
        $this->forumpost->set_forum_topic_id($this->get_complex_content_object_item()->get_ref());
        
        if ($type == 2)
        {
            $this->forumpost->set_reply_on_post_id($this->selected_forum_post_id);
        }
        
        // Create message
        
        $value = $this->forumpost->create();
        
        if ($value)
        {
            // Process attachments
            $this->forumpost->attach_content_objects($values['attachments']['lo'], ContentObject::ATTACHMENT_NORMAL);
        }
        
        return $value;
    }
}
