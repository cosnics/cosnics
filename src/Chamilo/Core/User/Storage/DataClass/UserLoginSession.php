<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * keeps track of browser session of last login. used to compare stored browser session with actual browser session this
 * way the user can be prevented from logging in twice or more
 */
class UserLoginSession extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SESSION_ID = 'session_id';

    public static $checked = false;
 // has the browser session been checked already?
    public static $single_login = null;
 // are there more than one active sessions for a certain user

    /**
     * Get the default properties of all users.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_SESSION_ID));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the
     *
     * @return String The lastname
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the
     *
     * @param String $firstname the firstname.
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the
     *
     * @return String The lastname
     */
    public function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Sets the
     *
     * @param String $firstname the firstname.
     */
    public function set_session_id($session_id)
    {
        $this->set_default_property(self :: PROPERTY_SESSION_ID, $session_id);
    }

    /**
     * checks if user is logged in more than once by checking if the active session is the same as the one store in
     * userloginsession
     *
     * @param type $update ?update the session table when session_id is different?
     */
    public static function check_single_login($update = true)
    {
        if (self :: $checked == false)
        {
            $current_user_id = \Chamilo\Libraries\Platform\Session\Session :: get_user_id();

            $user_login = DataManager :: retrieve_user_login_session_by_user_id($current_user_id);

            $current_session_id = session_id();
            if ($user_login)
            {
                // user_id exists in table --> compare session_ids
                // bypass if user is 'logged in as'
                if ($user_login->get_session_id() != $current_session_id &&
                     is_null(\Chamilo\Libraries\Platform\Session\Session :: get('_as_admin')))
                {
                    // session id is different
                    if ($update)
                    {
                        // update session table (upon loggin in)
                        $user_login->set_session_id($current_session_id);
                        $user_login->update();
                    }
                    else
                    {
                        // notify double login (upon evry other visit)
                        self :: $checked = true;
                        self :: $single_login = false;
                    }
                }
                else
                {
                    // session id is same: single login
                    self :: $single_login = true;
                    self :: $checked = true;
                }
            }
            else
            {
                if ($current_user_id)
                {
                    // not in table: first time login => create
                    $user_login_session = new UserLoginSession();
                    $user_login_session->set_user_id($current_user_id);
                    $user_login_session->set_session_id($current_session_id);
                    self :: $single_login = $user_login_session->create() ? true : false;
                    self :: $checked = true;
                }
            }
        }
    }
}
