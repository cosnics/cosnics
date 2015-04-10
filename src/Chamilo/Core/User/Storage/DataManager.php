<?php
namespace Chamilo\Core\User\Storage;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserLoginSession;
use Chamilo\Libraries\Architecture\Interfaces\UserRegistrationSupport;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\QueryCache;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @author Hans De Bisschop
 * @author Sven Vanpoucke
 * @package user.lib This is a skeleton for a data manager for the User application.
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'user_';

    /**
     * Logs a user in to the platform
     * 
     * @param $username string
     * @param $password string
     */
    public static function login($username, $password = null)
    {
        // Trim the username to make sure users are not created twice
        $username = trim($username);
        
        // If username is available, try to login
        if (! self :: is_username_available($username))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME), 
                new StaticConditionVariable($username));
            $user = self :: retrieve(User :: class_name(), $condition);
            $authentication_method = $user->get_auth_source();
            $authentication = Authentication :: factory($authentication_method);
            $succes = $authentication->check_login($user, $username, $password);
            
            if ($succes)
            {
                return $user;
            }
            
            return $authentication->get_message();
        }
        // If username is not available, check if an authentication method is able to register
        // the user in the platform
        else
        {
            $error_message = Translation :: get('UsernameNotAvailable'); // default error message
            $authentication_directory = dir(
                Path :: getInstance()->namespaceToFullPath('Chamilo\Libraries\Authentication'));
            
            while (false !== ($authentication_method = $authentication_directory->read()))
            {
                $authentication_method_name = (string) StringUtilities :: getInstance()->createString(
                    $authentication_method)->underscored();
                
                $is_directory = is_dir($authentication_dir->path . '/' . $authentication_method);
                $is_enabled = PlatformSetting :: get('enable_' . $authentication_method_name . '_authentication');
                
                if ($is_directory && $is_enabled)
                {
                    $authentication_class = 'Chamilo\Libraries\Authentication\\' . $authentication_method . '\\' .
                         $authentication_method . 'Authentication';
                    $authentication = new $authentication_class();
                    
                    if ($authentication instanceof UserRegistrationSupport)
                    {
                        if ($authentication->register_new_user($username, $password))
                        {
                            $authentication_directory->close();
                            return self :: get_instance()->retrieve_user_by_username($username);
                        }
                        else
                        {
                            // get authentication specific error message
                            $error_message = $authentication->get_message();
                        }
                    }
                }
            }
            
            $authentication_directory->close();
            
            return $error_message;
        }
    }

    /**
     * Logs the user out of the system
     */
    public static function logout()
    {
        $user = self :: retrieve_by_id(
            User :: class_name(), 
            intval(\Chamilo\Libraries\Platform\Session\Session :: get_user_id()));
        $authentication = Authentication :: factory($user->get_auth_source());
        if ($authentication->logout($user))
        {
            return true;
        }
        return false;
    }

    /**
     * Checks whether the user is allowed to be deleted Unfinished.
     */
    public static function user_deletion_allowed($user)
    {
        return false;
    }

    public static function official_code_exists($official_code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE), 
            new StaticConditionVariable($official_code));
        return (self :: count(User :: class_name(), $condition) > 0);
    }

    /**
     * Retrieve the users who are currently active
     * 
     * @param $condition \libraries\storage\Condition
     * @param $count int
     * @param $offset int
     * @param $order_by multitype:\common\libraries\ObjectTableOrder
     * @return \libraries\storage\DataClassResultSet
     */
    public static function retrieve_active_users($condition = null, $count = null, $offset = null, $order_by = array())
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ACTIVE), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);
        
        return self :: retrieves(User :: class_name(), $parameters);
    }

    /**
     * Retrieve the users who are currently approved
     * 
     * @param $condition \libraries\storage\Condition
     * @param $count int
     * @param $offset int
     * @param $order_by multitype:\common\libraries\ObjectTableOrder
     * @return \libraries\storage\DataClassResultSet
     */
    public static function retrieve_approved_users($condition = null, $count = null, $offset = null, $order_by = array())
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_APPROVED), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);
        
        return self :: retrieves(User :: class_name(), $parameters);
    }

    /**
     * Retrieve the users who are currently not approved
     * 
     * @param $condition \libraries\storage\Condition
     * @param $count int
     * @param $offset int
     * @param $order_by multitype:\common\libraries\ObjectTableOrder
     * @return \libraries\storage\DataClassResultSet
     */
    public static function retrieve_approval_users($condition = null, $count = null, $offset = null, $order_by = array())
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_APPROVED), 
            new StaticConditionVariable(0));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);
        
        return self :: retrieves(User :: class_name(), $parameters);
    }

    /**
     * Retrieve the (first) user whose status is "anonymous"
     * 
     * @return user\User boolean
     */
    public static function retrieve_anonymous_user()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_STATUS), 
            new StaticConditionVariable(User :: STATUS_ANONYMOUS));
        return self :: retrieve(User :: CLASS_NAME, $condition);
    }

    /**
     * Return the full name for a specific user, or a translation if provided or if the user does not exist
     * 
     * @param $id int
     * @param $unknown_user_translation string
     * @return string
     */
    public static function get_fullname_from_user($id, $unknown_user_translation = null)
    {
        $user = self :: retrieve_by_id(User :: CLASS_NAME, $id);
        if ($user instanceof User)
        {
            return $user->get_fullname();
        }
        
        if ($unknown_user_translation)
        {
            return $unknown_user_translation;
        }
        
        return Translation :: get('UnknownUser');
    }

    /**
     * Returns the User currently registered in the session
     * 
     * @return user\User
     */
    public static function get_current_user()
    {
        return self :: retrieve_by_id(User :: CLASS_NAME, \Chamilo\Libraries\Platform\Session\Session :: get_user_id());
    }

    /**
     * Retrieve a User based on the official code property
     * 
     * @param $official_code string
     * @return user\User
     */
    public static function retrieve_user_by_official_code($official_code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE), 
            new StaticConditionVariable($official_code));
        return self :: retrieve(User :: CLASS_NAME, $condition);
    }

    /**
     * Retrieve a User based on the username property
     * 
     * @param $username string
     * @return user\User
     */
    public static function retrieve_user_by_username($username)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME), 
            new StaticConditionVariable($username));
        return self :: retrieve(User :: CLASS_NAME, $condition);
    }

    /**
     * Retrieve a User based on the external user id property
     * 
     * @param $external_uid string
     * @return user\User
     */
    public static function retrieve_user_by_external_uid($external_uid)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EXTERNAL_UID), 
            new StaticConditionVariable($external_uid));
        return self :: retrieve(User :: CLASS_NAME, $condition);
    }

    /**
     * Retrieve a User based on the security token
     * 
     * @param $security_token string
     * @return user\User
     */
    public static function retrieve_user_by_security_token($security_token)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_SECURITY_TOKEN), 
            new StaticConditionVariable($security_token));
        return self :: retrieve(User :: CLASS_NAME, $condition);
    }

    /**
     * Retrieve a user-id based on the official code property
     * 
     * @param $official_code string
     * @return int
     */
    public static function retrieve_user_id_by_official_code($official_code)
    {
        $user = self :: retrieve_user_by_official_code($official_code);
        return ($user instanceof User ? $user->get_id() : null);
    }

    /**
     * Retrieve a DataClassResultSet of Users based on a set of official codes
     * 
     * @param $official_codes multitype:string
     * @return \libraries\storage\DataClassResultSet
     */
    public static function retrieve_users_by_official_codes($official_codes)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE), 
            $official_codes);
        return self :: retrieves(User :: class_name(), $condition);
    }

    /**
     * Retrieve a DataClassResultSet of Users based on a set of email addresses
     * 
     * @param $email multitype:string
     * @return \libraries\storage\DataClassResultSet
     * @deprecated Should no longer return an array, calls should be changed to use a while-loop now instead of a for
     *             each-loop
     */
    public static function retrieve_users_by_email($email)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL), 
            new StaticConditionVariable($email));
        return self :: retrieves(User :: class_name(), $condition)->as_array();
    }

    /**
     * Is the username still available in the storage layer or not?
     * 
     * @param $username string
     * @param $user_id int
     * @return boolean
     */
    public static function is_username_available($username, $user_id = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME), 
            new StaticConditionVariable($username));
        
        if ($user_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME), 
                new StaticConditionVariable($username));
            $conditions = new EqualityCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                new StaticConditionVariable($user_id));
            $condition = new AndCondition($conditions);
        }
        
        return ! (self :: count(User :: class_name(), $condition) == 1);
    }

    /**
     * Retrieve a User based on the username property
     * 
     * @param $username string
     * @return user\User
     * @deprecated Use DataManager :: retrieve_user_by_username() now
     */
    public static function retrieve_user_info($username)
    {
        return self :: retrieve_user_by_username($username);
    }

    /**
     * Attempt to retrieve a user based on his full name (first name + last name)
     * 
     * @param $fullname string
     * @return user\User
     */
    public static function retrieve_user_by_fullname($fullname)
    {
        $name = explode(' ', $fullname);
        $firstname = $name[0];
        $lastname = $name[1];
        
        $conditions = array();
        $conditions1 = array();
        $conditions2 = array();
        
        $conditions1[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME), 
            new StaticConditionVariable($firstname));
        $conditions1[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME), 
            new StaticConditionVariable($lastname));
        $conditions[] = new AndCondition($conditions1);
        
        $conditions2[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME), 
            new StaticConditionVariable($lastname));
        $conditions2[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME), 
            new StaticConditionVariable($firstname));
        $conditions[] = new AndCondition($conditions2);
        
        $condition = new OrCondition($conditions);
        return self :: retrieve(User :: class_name(), $condition);
    }

    /**
     * Count the users who are currently active
     * 
     * @param $condition \libraries\storage\Condition
     * @return \libraries\storage\DataClassResultSet
     */
    public static function count_active_users($condition = null)
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ACTIVE), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        return self :: count(User :: class_name(), new DataClassCountParameters($condition));
    }

    /**
     * Count the users who are currently approved
     * 
     * @param $condition \libraries\storage\Condition
     * @return \libraries\storage\DataClassResultSet
     */
    public static function count_approved_users($condition = null)
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_APPROVED), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        return self :: count(User :: class_name(), $condition);
    }

    /**
     * Count the users who are currently not approved
     * 
     * @param $condition \libraries\storage\Condition
     * @return \libraries\storage\DataClassResultSet
     */
    public static function count_approval_users($condition = null)
    {
        $conditions = array();
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_APPROVED), 
            new StaticConditionVariable(0));
        $condition = new AndCondition($conditions);
        
        return self :: count(User :: class_name(), $condition);
    }

    public static function delete_all_users()
    {
        while ($user = self :: retrieves(User :: class_name())->next_result())
        {
            $user->delete();
        }
    }

    public static function get_total_user_disk_quota($reset = false)
    {
        $cache_file = Path :: getInstance()->getCachePath(__NAMESPACE__) . 'total_user_disk_quota';
        
        if (! file_exists($cache_file) || $reset)
        {
            $policy = PlatformSetting :: get('quota_policy', __NAMESPACE__);
            $fallback = PlatformSetting :: get('quota_fallback', __NAMESPACE__);
            
            if ($policy == Calculator :: POLICY_USER && ! $fallback)
            {
                $property = new FunctionConditionVariable(
                    FunctionConditionVariable :: SUM, 
                    new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_DISK_QUOTA), 
                    'disk_quota');
                
                $parameters = new RecordRetrieveParameters(new DataClassProperties($property));
                
                $record = self :: record(User :: class_name(), $parameters);
                $total_quota = $record['disk_quota'];
            }
            else
            {
                $users = self :: retrieves(User :: class_name());
                
                $total_quota = 0;
                
                while ($user = $users->next_result())
                {
                    $calculator = new Calculator($user);
                    $total_quota += $calculator->get_maximum_user_disk_quota();
                }
                
                $total_quota;
            }
            
            Filesystem :: write_to_file($cache_file, serialize($total_quota));
        }
        else
        {
            return unserialize(file_get_contents($cache_file));
        }
    }

    public static function query_cache_example($param1 = null, $param2 = null)
    {
        $hash = QueryCache :: hash();
        
        if (! QueryCache :: exists($hash))
        {
            $record = 5;
            QueryCache :: add($record, $hash);
        }
        return QueryCache :: get($hash);
    }

    /**
     * retrieves the last stored browser session for a certain user
     * 
     * @param int $user_id
     * @return UserLoginSession
     */
    public function retrieve_user_login_session_by_user_id($user_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserLoginSession :: class_name(), UserLoginSession :: PROPERTY_USER_ID), 
            new StaticConditionVariable($user_id));
        return self :: retrieve(UserLoginSession :: CLASS_NAME, $condition);
    }
}
