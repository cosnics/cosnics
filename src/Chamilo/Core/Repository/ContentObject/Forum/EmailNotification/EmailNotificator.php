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

    public $action_body;

    public $action_title;

    /**
     * @var User
     */
    public $action_user;

    public $topic;

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    public $users = [];

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */

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
     * Set the action what is done (title)
     *
     * @param $action
     */
    public function set_action_title($action)
    {
        $this->action_title = $action;
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
     * Set the topic which is changed.
     *
     * @param ForumTopic $topic
     */
    public function set_topic($topic)
    {
        $this->topic = $topic;
    }
}
