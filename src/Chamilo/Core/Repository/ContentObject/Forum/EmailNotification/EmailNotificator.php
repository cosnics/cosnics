<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\EmailNotification;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * this class contains a list of subscribed users and notificates the users
 * 
 * @author Mattias De Pauw - Hogeschool Gent
 */
abstract class EmailNotificator
{

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    public $users = array();

    public $topic;

    public $action_title;

    public $action_body;

    /**
     * @var User
     */
    public $action_user;

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Set the topic which is changed.
     * 
     * @param ForumTopic $topic
     *
     */
    public function set_topic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * Set the action what is done (title)
     * 
     * @param $action
     */
    public function set_action_title($action)
    {
        $this->action_title = $action;
    }

    /**
     * set the action what is done(body)
     * 
     * @param $action
     */
    public function set_action_body($action)
    {
        $this->action_body = $action;
    }

    /**
     * set the user who did the action
     * 
     * @param $action_user
     */
    public function set_action_user($action_user)
    {
        $this->action_user = $action_user;
    }

    /**
     * Add a unique list of users who must be notificated by email.
     * 
     * @param List $users
     */
    public function add_users($users)
    {
        $merge = (array) $this->users + (array) $users;
        
        $this->users = $merge;
    }

    /**
     * Send to all users a email
     */
    abstract public function send_emails();
}
