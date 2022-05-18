<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

// use Chamilo\Core\User\Service\UserGroupMembershipCacheService;

/**
 *
 * @package Chamilo\Core\User\Storage\DataClass$User
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class User extends DataClass
{
    const ANONYMOUS_ID = "1";

    const CONTEXT = __NAMESPACE__;

    const NAME_FORMAT_FIRST = 0;
    const NAME_FORMAT_LAST = 1;

    const PROPERTY_ACTIVATION_DATE = 'activation_date';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_APPROVED = 'approved';
    const PROPERTY_AUTH_SOURCE = 'auth_source';
    const PROPERTY_CREATOR_ID = 'creator_id';
    const PROPERTY_DATABASE_QUOTA = 'database_quota';
    const PROPERTY_DISK_QUOTA = 'disk_quota';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_EXTERNAL_UID = 'external_uid';
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_LASTNAME = 'lastname';
    const PROPERTY_OFFICIAL_CODE = 'official_code';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_PHONE = 'phone';
    const PROPERTY_PICTURE_URI = 'picture_uri';
    const PROPERTY_PLATFORMADMIN = 'admin';
    const PROPERTY_REGISTRATION_DATE = 'registration_date';
    const PROPERTY_SECURITY_TOKEN = 'security_token';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_TERMS_DATE = 'terms_date';
    const PROPERTY_USERNAME = 'username';

    const STATUS_ANONYMOUS = 0;
    const STATUS_STUDENT = 5;
    const STATUS_TEACHER = 1;

    public static function admin()
    {
        $user_id = \Chamilo\Libraries\Platform\Session\Session::get_user_id();
        if ($user_id && $user_id != '')
        {
            return DataManager::retrieve_by_id(User::class, (int) $user_id)->is_platform_admin();
        }
        else
        {
            return false;
        }
    }

    /**
     * Instructs the Datamanager to create this user.
     *
     * @return boolean True if success, false otherwise
     */
    public function create()
    {
        $this->set_registration_date(time());
        $this->set_security_token(sha1(time() . uniqid()));

        if (!parent::create($this))
        {
            return false;
        }

        return true;
    }

    public function delete()
    {
        $group_rel_user_condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_id())
        );
        $success = \Chamilo\Core\Group\Storage\DataManager::deletes(
            GroupRelUser::class, $group_rel_user_condition
        );

        if ($success)
        {
            return parent::delete();
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the fullname of this user
     *
     * @return string The fullname
     */
    public static function fullname($first_name, $last_name)
    {
        $format = Configuration::getInstance()->get_setting(array(Manager::context(), 'fullname_format'));

        switch ($format)
        {
            case self::NAME_FORMAT_FIRST :
                return $first_name . ' ' . $last_name;
                break;
            case self::NAME_FORMAT_LAST :
                return $last_name . ' ' . $first_name;
                break;
            default :
                return $first_name . ' ' . $last_name;
        }
    }

    /**
     *
     * @return string
     */
    public function getAuthenticationSource()
    {
        return $this->getDefaultProperty(self::PROPERTY_AUTH_SOURCE);
    }

    public function get_activation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTIVATION_DATE);
    }

    public function get_active()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTIVE);
    }

    public function get_approved()
    {
        return $this->getDefaultProperty(self::PROPERTY_APPROVED);
    }

    /**
     * Returns the auth_source for this user.
     *
     * @return String The auth_source
     * @deprecated Use getAuthenticationSource() now
     */
    public function get_auth_source()
    {
        return $this->getAuthenticationSource();
    }

    /**
     * Returns all (unique) properties by which a DataClass object can be cached
     *
     * @param $extendedPropertyNames string[]
     *
     * @return string[]
     */
    public static function getCacheablePropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getCacheablePropertyNames([self::PROPERTY_USERNAME]);
    }

    /**
     * Returns the creator ID for this user.
     *
     * @return Int The ID
     */
    public function get_creator_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_ID);
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the database quota for this user.
     *
     * @return Int the database quota
     */
    public function get_database_quota()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATABASE_QUOTA);
    }

    /**
     * Get the default properties of all users.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_LASTNAME,
                self::PROPERTY_FIRSTNAME,
                self::PROPERTY_USERNAME,
                self::PROPERTY_PASSWORD,
                self::PROPERTY_AUTH_SOURCE,
                self::PROPERTY_EXTERNAL_UID,
                self::PROPERTY_EMAIL,
                self::PROPERTY_STATUS,
                self::PROPERTY_PLATFORMADMIN,
                self::PROPERTY_PHONE,
                self::PROPERTY_OFFICIAL_CODE,
                self::PROPERTY_PICTURE_URI,
                self::PROPERTY_CREATOR_ID,
                self::PROPERTY_DISK_QUOTA,
                self::PROPERTY_DATABASE_QUOTA,
                self::PROPERTY_ACTIVATION_DATE,
                self::PROPERTY_EXPIRATION_DATE,
                self::PROPERTY_REGISTRATION_DATE,
                self::PROPERTY_ACTIVE,
                self::PROPERTY_SECURITY_TOKEN,
                self::PROPERTY_APPROVED,
                self::PROPERTY_TERMS_DATE
            )
        );
    }

    /**
     * Returns the disk quota for this user.
     *
     * @return Int the disk quota
     */
    public function get_disk_quota()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISK_QUOTA);
    }

    /**
     * Returns the email for this user.
     *
     * @return String The email address
     */
    public function get_email()
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL);
    }

    /**
     * Returns the expiration date for this user.
     *
     * @return string the theme
     */
    public function get_expiration_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXPIRATION_DATE);
    }

    /**
     * Returns the external authentication system unique id for this user (useful for instance with : Shibboleth,
     * OpenID, LDAP, .
     * ..)
     *
     * @return String The external unique id
     */
    public function get_external_uid()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXTERNAL_UID);
    }

    /**
     * Returns the firstname of this user.
     *
     * @return String The firstname
     */
    public function get_firstname()
    {
        return $this->getDefaultProperty(self::PROPERTY_FIRSTNAME);
    }

    /**
     * Returns the fullname of this user
     *
     * @return string The fullname
     */
    public function get_fullname()
    {
        return self::fullname($this->get_firstname(), $this->get_lastname());
    }

    public static function get_fullname_format_options()
    {
        $options = [];
        $options[self::NAME_FORMAT_FIRST] = Translation::get('FirstName') . ' ' . Translation::get('LastName');
        $options[self::NAME_FORMAT_LAST] = Translation::get('LastName') . ' ' . Translation::get('FirstName');

        return $options;
    }

    /**
     * @param false $only_retrieve_ids
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Group\Storage\DataClass\Group>|integer[]
     */
    public function get_groups($only_retrieve_ids = false)
    {
        // if (! $only_retrieve_ids)
        // {
        return \Chamilo\Core\Group\Storage\DataManager::retrieve_all_subscribed_groups_array(
            $this->getId(), $only_retrieve_ids
        );
        // }
        // else
        // {
        // $userGroupMembershipCacheService = new UserGroupMembershipCacheService();
        // return $userGroupMembershipCacheService->getMembershipsForUserAndType($this, $only_retrieve_ids);
        // }
    }

    /**
     * Returns the lastname of this user.
     *
     * @return String The lastname
     */
    public function get_lastname()
    {
        return $this->getDefaultProperty(self::PROPERTY_LASTNAME);
    }

    /**
     * Returns the official code for this user.
     *
     * @return String The official code
     */
    public function get_official_code()
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICIAL_CODE);
    }

    /**
     * Returns the password of this user.
     *
     * @return String The password
     */
    public function get_password()
    {
        return $this->getDefaultProperty(self::PROPERTY_PASSWORD);
    }

    /**
     * Returns the phone number for this user.
     *
     * @return String The phone number
     */
    public function get_phone()
    {
        return $this->getDefaultProperty(self::PROPERTY_PHONE);
    }

    /**
     * Returns the Picture URI for this user.
     *
     * @return String The URI
     */
    public function get_picture_uri()
    {
        return $this->getDefaultProperty(self::PROPERTY_PICTURE_URI);
    }

    /**
     * Returns if the user is platformadmin or not.
     *
     * @return Int platformadmin
     */
    public function get_platformadmin()
    {
        return $this->getDefaultProperty(self::PROPERTY_PLATFORMADMIN);
    }

    public function get_registration_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_REGISTRATION_DATE);
    }

    public function get_security_token()
    {
        return $this->getDefaultProperty(self::PROPERTY_SECURITY_TOKEN);
    }

    /**
     * Returns the status for this user.
     *
     * @return Int The status
     */
    public function get_status()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    public function get_status_name()
    {
        if ($this->get_platformadmin() == '1')
        {
            return Translation::get('PlatformAdministrator');
        }

        switch ($this->get_status())
        {
            case self::STATUS_ANONYMOUS :
                return Translation::get('Anonymous');
                break;
            case self::STATUS_STUDENT :
                return Translation::get('Student');
                break;
            case self::STATUS_TEACHER :
                return Translation::get('CourseAdmin');
                break;
            default :
                return Translation::get('Student');
        }
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'user_user';
    }

    /**
     * Returns the date on wich the user has last accepted the terms and conditions
     *
     * @return <integer> terms_date
     */
    public function get_terms_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_TERMS_DATE);
    }

    public function get_user_groups()
    {
        return \Chamilo\Core\Group\Storage\DataManager::retrieve_user_groups($this->get_id());
    }

    /**
     * Returns the username of this user.
     *
     * @return String The username
     */
    public function get_username()
    {
        return $this->getDefaultProperty(self::PROPERTY_USERNAME);
    }

    public function is_active()
    {
        if ($this->get_active())
        {
            if ($this->get_activation_date() == 0 || time() >= $this->get_activation_date())
            {
                if ($this->get_expiration_date() == 0 || time() <= $this->get_expiration_date())
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function is_anonymous_user()
    {
        return $this->get_status() == self::STATUS_ANONYMOUS;
    }

    /**
     * Checks if this user is a platform admin or not
     *
     * @return boolean true if the user is a platforma admin, false otherwise
     */
    public function is_platform_admin()
    {
        return $this->get_platformadmin() == 1;
    }

    /**
     * Checks if this user is a teacher or not
     *
     * @return boolean true if the user is a teacher, false otherwise
     */
    public function is_teacher()
    {
        return $this->get_status() == self::STATUS_TEACHER;
    }

    public function set_activation_date($activation_date)
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVATION_DATE, $activation_date);
    }

    public function set_active($active)
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVE, $active);
    }

    public function set_approved($approved)
    {
        $this->setDefaultProperty(self::PROPERTY_APPROVED, $approved);
    }

    /**
     * Sets the Auth_source for this user.
     *
     * @param $auth_source String the auth source.
     */
    public function set_auth_source($auth_source)
    {
        $this->setDefaultProperty(self::PROPERTY_AUTH_SOURCE, $auth_source);
    }

    /**
     * Sets the creator ID for this user.
     *
     * @param $creator_id String the creator ID.
     */
    public function set_creator_id($creator_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATOR_ID, $creator_id);
    }

    /**
     * Sets the database_quota for this user.
     *
     * @param $database_quota Int The database quota.
     */
    public function set_database_quota($database_quota)
    {
        $this->setDefaultProperty(self::PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    /**
     * Sets the disk quota for this user.
     *
     * @param $disk_quota Int The disk quota.
     */
    public function set_disk_quota($disk_quota)
    {
        $this->setDefaultProperty(self::PROPERTY_DISK_QUOTA, $disk_quota);
    }

    /**
     * Sets the email for this user.
     *
     * @param $email String the email.
     */
    public function set_email($email)
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL, $email);
    }

    /**
     * Sets the default theme for this user.
     *
     * @param $theme string The theme.
     */
    public function set_expiration_date($expiration_date)
    {
        $this->setDefaultProperty(self::PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    /**
     * Sets the external authentication system unique id for this user (useful for instance with : Shibboleth, OpenID,
     * LDAP, .
     * ..)
     *
     * @param $external_uid String the external unique id
     */
    public function set_external_uid($external_uid)
    {
        $this->setDefaultProperty(self::PROPERTY_EXTERNAL_UID, $external_uid);
    }

    /**
     * Sets the firstname of this user.
     *
     * @param $firstname String the firstname.
     */
    public function set_firstname($firstname)
    {
        $this->setDefaultProperty(self::PROPERTY_FIRSTNAME, $firstname);
    }

    /*
     */

    /**
     * Sets the lastname of this user.
     *
     * @param $lastname String the lastname.
     */
    public function set_lastname($lastname)
    {
        $this->setDefaultProperty(self::PROPERTY_LASTNAME, $lastname);
    }

    /**
     * Sets the official code for this user.
     *
     * @param $official_code String the official code.
     */
    public function set_official_code($official_code)
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICIAL_CODE, $official_code);
    }

    /**
     * Sets the password of this user.
     * If Chamilo configuration is set to encrypt the password, this function will also
     * take care of that.
     *
     * @param $password String the password.
     */
    public function set_password($password)
    {
        $this->setDefaultProperty(self::PROPERTY_PASSWORD, $password);
    }

    /**
     * Sets the phone number for this user.
     *
     * @param $phone String the phone number
     */
    public function set_phone($phone)
    {
        $this->setDefaultProperty(self::PROPERTY_PHONE, $phone);
    }

    /**
     * Sets the picture uri for this user object
     *
     * @param $picture_uri String the picture URI
     */
    public function set_picture_uri($picture_uri)
    {
        $this->setDefaultProperty(self::PROPERTY_PICTURE_URI, $picture_uri);
    }

    /**
     * Sets the platformadmin property for this user.
     *
     * @param $admin Int the platformadmin status.
     */
    public function set_platformadmin($admin)
    {
        $this->setDefaultProperty(self::PROPERTY_PLATFORMADMIN, $admin);
    }

    public function set_registration_date($registration_date)
    {
        $this->setDefaultProperty(self::PROPERTY_REGISTRATION_DATE, $registration_date);
    }

    public function set_security_token($security_token)
    {
        $this->setDefaultProperty(self::PROPERTY_SECURITY_TOKEN, $security_token);
    }

    /**
     * Sets the status for this user.
     *
     * @param $status Int the status.
     */
    public function set_status($status)
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    /**
     * Sets the date the user has accepted the terms and conditons
     *
     * @param <integer> $date
     */
    public function set_term_date($terms_date)
    {
        $this->setDefaultProperty(self::PROPERTY_TERMS_DATE, $terms_date);
    }

    /**
     * Sets the username of this user.
     *
     * @param $username String the username.
     */
    public function set_username($username)
    {
        $this->setDefaultProperty(self::PROPERTY_USERNAME, $username);
    }

    /**
     * check if user has seen/agreed with the latest terms and conditions return false if user has not seen latest
     * version, true when user has seen latest version
     */
    public function terms_conditions_uptodate()
    {
        $user_date = $this->get_terms_date();
        if ($user_date == null or $user_date === 0)
        {
            return false;
        }
        else
        {
            $system_date = Manager::get_date_terms_and_conditions_last_modified();
            if ($user_date < $system_date)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}
