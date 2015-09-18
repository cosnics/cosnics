<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * $Id: user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib
 */
/**
 * This class represents a user.
 * User objects have a number of default properties: - user_id: the numeric ID of the
 * user; - lastname: the lastname of the user; - firstname: the firstname of the user; - password: the password for this
 * user; - auth_source: - external_uid: the external authentication system unique id of the user (eg: Shibboleth uid,
 * OpenID uid, ...) - email: the email address of this user; - status: the status of this user: 1 is teacher, 5 is a
 * student; - phone: the phone number of the user; - official_code; the official code of this user; - picture_uri: the
 * URI location of the picture of this user; - creator_id: the user_id of the user who created this user; - language:
 * the language setting of this user; - disk quota: the disk quota for this user; - database_quota: the database quota
 * for this user; - version_quota: the default quota for this user of no quota for a specific learning object type is
 * set.
 *
 * @author Hans de Bisschop
 * @author Dieter De Neef
 */
class User extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const CONTEXT = __NAMESPACE__;
    const PROPERTY_LASTNAME = 'lastname';
    const PROPERTY_FIRSTNAME = 'firstname';
    const PROPERTY_USERNAME = 'username';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_AUTH_SOURCE = 'auth_source';
    const PROPERTY_EXTERNAL_UID = 'external_uid';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PLATFORMADMIN = 'admin';
    const PROPERTY_PHONE = 'phone';
    const PROPERTY_OFFICIAL_CODE = 'official_code';
    const PROPERTY_PICTURE_URI = 'picture_uri';
    const PROPERTY_CREATOR_ID = 'creator_id';
    const PROPERTY_DISK_QUOTA = 'disk_quota';
    const PROPERTY_DATABASE_QUOTA = 'database_quota';
    const PROPERTY_ACTIVATION_DATE = 'activation_date';
    const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    const PROPERTY_REGISTRATION_DATE = 'registration_date';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_SECURITY_TOKEN = 'security_token';
    const PROPERTY_APPROVED = 'approved';
    const NAME_FORMAT_FIRST = 0;
    const NAME_FORMAT_LAST = 1;
    const ANONYMOUS_ID = "1";
    const STATUS_ANONYMOUS = 0;
    const STATUS_TEACHER = 1;
    const STATUS_STUDENT = 5;
    const PROPERTY_TERMS_DATE = 'terms_date';

    /**
     * Get the default properties of all users.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_LASTNAME,
                self :: PROPERTY_FIRSTNAME,
                self :: PROPERTY_USERNAME,
                self :: PROPERTY_PASSWORD,
                self :: PROPERTY_AUTH_SOURCE,
                self :: PROPERTY_EXTERNAL_UID,
                self :: PROPERTY_EMAIL,
                self :: PROPERTY_STATUS,
                self :: PROPERTY_PLATFORMADMIN,
                self :: PROPERTY_PHONE,
                self :: PROPERTY_OFFICIAL_CODE,
                self :: PROPERTY_PICTURE_URI,
                self :: PROPERTY_CREATOR_ID,
                self :: PROPERTY_DISK_QUOTA,
                self :: PROPERTY_DATABASE_QUOTA,
                self :: PROPERTY_ACTIVATION_DATE,
                self :: PROPERTY_EXPIRATION_DATE,
                self :: PROPERTY_REGISTRATION_DATE,
                self :: PROPERTY_ACTIVE,
                self :: PROPERTY_SECURITY_TOKEN,
                self :: PROPERTY_APPROVED,
                self :: PROPERTY_TERMS_DATE));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the lastname of this user.
     *
     * @return String The lastname
     */
    public function get_lastname()
    {
        return $this->get_default_property(self :: PROPERTY_LASTNAME);
    }

    /**
     * Returns the firstname of this user.
     *
     * @return String The firstname
     */
    public function get_firstname()
    {
        return $this->get_default_property(self :: PROPERTY_FIRSTNAME);
    }

    /**
     * Returns the fullname of this user
     *
     * @return string The fullname
     */
    public function get_fullname()
    {
        return self :: fullname($this->get_firstname(), $this->get_lastname());
    }

    /**
     * Returns the fullname of this user
     *
     * @return string The fullname
     */
    public static function fullname($first_name, $last_name)
    {
        $format = PlatformSetting :: get('fullname_format', Manager :: context());

        switch ($format)
        {
            case self :: NAME_FORMAT_FIRST :
                return $first_name . ' ' . $last_name;
                break;
            case self :: NAME_FORMAT_LAST :
                return $last_name . ' ' . $first_name;
                break;
            default :
                return $first_name . ' ' . $last_name;
        }
    }

    /**
     * Returns the username of this user.
     *
     * @return String The username
     */
    public function get_username()
    {
        return $this->get_default_property(self :: PROPERTY_USERNAME);
    }

    /**
     * Returns the password of this user.
     *
     * @return String The password
     */
    public function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
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
     *
     * @return string
     */
    public function getAuthenticationSource()
    {
        return $this->get_default_property(self :: PROPERTY_AUTH_SOURCE);
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
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_UID);
    }

    /**
     * Returns the email for this user.
     *
     * @return String The email address
     */
    public function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    /**
     * Returns the status for this user.
     *
     * @return Int The status
     */
    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns if the user is platformadmin or not.
     *
     * @return Int platformadmin
     */
    public function get_platformadmin()
    {
        return $this->get_default_property(self :: PROPERTY_PLATFORMADMIN);
    }

    /**
     * Returns the official code for this user.
     *
     * @return String The official code
     */
    public function get_official_code()
    {
        return $this->get_default_property(self :: PROPERTY_OFFICIAL_CODE);
    }

    /**
     * Returns the phone number for this user.
     *
     * @return String The phone number
     */
    public function get_phone()
    {
        return $this->get_default_property(self :: PROPERTY_PHONE);
    }

    /**
     * Returns the Picture URI for this user.
     *
     * @return String The URI
     */
    public function get_picture_uri()
    {
        return $this->get_default_property(self :: PROPERTY_PICTURE_URI);
    }

    /**
     * Returns the creator ID for this user.
     *
     * @return Int The ID
     */
    public function get_creator_id()
    {
        return $this->get_default_property(self :: PROPERTY_CREATOR_ID);
    }

    /**
     * Returns the disk quota for this user.
     *
     * @return Int the disk quota
     */
    public function get_disk_quota()
    {
        return $this->get_default_property(self :: PROPERTY_DISK_QUOTA);
    }

    /**
     * Returns the database quota for this user.
     *
     * @return Int the database quota
     */
    public function get_database_quota()
    {
        return $this->get_default_property(self :: PROPERTY_DATABASE_QUOTA);
    }

    public function get_activation_date()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVATION_DATE);
    }

    /**
     * Returns the expiration date for this user.
     *
     * @return string the theme
     */
    public function get_expiration_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRATION_DATE);
    }

    public function get_registration_date()
    {
        return $this->get_default_property(self :: PROPERTY_REGISTRATION_DATE);
    }

    public function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    public function get_security_token()
    {
        return $this->get_default_property(self :: PROPERTY_SECURITY_TOKEN);
    }

    /**
     * Returns the date on wich the user has last accepted the terms and conditions
     *
     * @return <integer> terms_date
     */
    public function get_terms_date()
    {
        return $this->get_default_property(self :: PROPERTY_TERMS_DATE);
    }

    /**
     * Sets the lastname of this user.
     *
     * @param $lastname String the lastname.
     */
    public function set_lastname($lastname)
    {
        $this->set_default_property(self :: PROPERTY_LASTNAME, $lastname);
    }

    /**
     * Sets the firstname of this user.
     *
     * @param $firstname String the firstname.
     */
    public function set_firstname($firstname)
    {
        $this->set_default_property(self :: PROPERTY_FIRSTNAME, $firstname);
    }

    /**
     * Sets the username of this user.
     *
     * @param $username String the username.
     */
    public function set_username($username)
    {
        $this->set_default_property(self :: PROPERTY_USERNAME, $username);
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
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    /**
     * Sets the Auth_source for this user.
     *
     * @param $auth_source String the auth source.
     */
    public function set_auth_source($auth_source)
    {
        $this->set_default_property(self :: PROPERTY_AUTH_SOURCE, $auth_source);
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
        $this->set_default_property(self :: PROPERTY_EXTERNAL_UID, $external_uid);
    }

    /**
     * Sets the email for this user.
     *
     * @param $email String the email.
     */
    public function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    /**
     * Sets the status for this user.
     *
     * @param $status Int the status.
     */
    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Sets the platformadmin property for this user.
     *
     * @param $admin Int the platformadmin status.
     */
    public function set_platformadmin($admin)
    {
        $this->set_default_property(self :: PROPERTY_PLATFORMADMIN, $admin);
    }

    /**
     * Sets the official code for this user.
     *
     * @param $official_code String the official code.
     */
    public function set_official_code($official_code)
    {
        $this->set_default_property(self :: PROPERTY_OFFICIAL_CODE, $official_code);
    }

    /**
     * Sets the phone number for this user.
     *
     * @param $phone String the phone number
     */
    public function set_phone($phone)
    {
        $this->set_default_property(self :: PROPERTY_PHONE, $phone);
    }

    /**
     * Sets the picture uri for this user object
     *
     * @param $picture_uri String the picture URI
     */
    public function set_picture_uri($picture_uri)
    {
        $this->set_default_property(self :: PROPERTY_PICTURE_URI, $picture_uri);
    }

    public function set_security_token($security_token)
    {
        $this->set_default_property(self :: PROPERTY_SECURITY_TOKEN, $security_token);
    }

    /**
     * Determines if this user has uploaded a picture
     *
     * @return boolean
     */
    public function has_picture()
    {
        $uri = $this->get_picture_uri();
        return ((strlen($uri) > 0) && (Path :: getInstance()->isWebUri($uri) || file_exists(
            Path :: getInstance()->getProfilePicturePath() . $uri)));
    }

    public function get_full_picture_path()
    {
        if ($this->has_picture())
        {
            return Path :: getInstance()->getProfilePicturePath() . $this->get_picture_uri();
        }
        else
        {
            $profilePictureIdentifier = Session :: get('profile_picture_identifier', false);

            if (! $profilePictureIdentifier)
            {
                $profilePictureIdentifier = rand(0, 75);
                Session :: register('profile_picture_identifier', $profilePictureIdentifier);
            }

            return Theme :: getInstance()->getImagePath(
                self :: package(),
                'Unknown' . DIRECTORY_SEPARATOR . $profilePictureIdentifier,
                'png',
                false);
        }
    }

    /**
     * Sets the picture file
     *
     * @param array The information of the uploaded file (from the $_FILES- array)
     * @todo Make image resizing configurable
     */
    public function set_picture_file($file_info)
    {
        $this->delete_picture();
        $path = Path :: getInstance()->getProfilePicturePath();
        Filesystem :: create_dir($path);
        $img_file = Filesystem :: create_unique_name($path, $this->get_id() . '-' . $file_info['name']);
        move_uploaded_file($file_info['tmp_name'], $path . $img_file);
        $image_manipulation = ImageManipulation :: factory($path . $img_file);
        // Scale image to fit in 400x400 box. Should be configurable somewhere
        $image_manipulation->scale(400, 400);
        $image_manipulation->write_to_file();
        $this->set_picture_uri($img_file);
    }

    /**
     * Removes the picture connected to this user
     */
    public function delete_picture()
    {
        if ($this->has_picture())
        {
            $path = Path :: getInstance()->getProfilePicturePath() . $this->get_picture_uri();
            Filesystem :: remove($path);
            $this->set_picture_uri(null);
        }
    }

    /**
     * Sets the creator ID for this user.
     *
     * @param $creator_id String the creator ID.
     */
    public function set_creator_id($creator_id)
    {
        $this->set_default_property(self :: PROPERTY_CREATOR_ID, $creator_id);
    }

    /**
     * Sets the disk quota for this user.
     *
     * @param $disk_quota Int The disk quota.
     */
    public function set_disk_quota($disk_quota)
    {
        $this->set_default_property(self :: PROPERTY_DISK_QUOTA, $disk_quota);
    }

    /**
     * Sets the database_quota for this user.
     *
     * @param $database_quota Int The database quota.
     */
    public function set_database_quota($database_quota)
    {
        $this->set_default_property(self :: PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    public function set_activation_date($activation_date)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVATION_DATE, $activation_date);
    }

    /**
     * Sets the default theme for this user.
     *
     * @param $theme string The theme.
     */
    public function set_expiration_date($expiration_date)
    {
        $this->set_default_property(self :: PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    public function set_registration_date($registration_date)
    {
        $this->set_default_property(self :: PROPERTY_REGISTRATION_DATE, $registration_date);
    }

    public function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    public function set_approved($approved)
    {
        $this->set_default_property(self :: PROPERTY_APPROVED, $approved);
    }

    /**
     * Sets the date the user has accepted the terms and conditons
     *
     * @param <integer> $date
     */
    public function set_term_date($terms_date)
    {
        $this->set_default_property(self :: PROPERTY_TERMS_DATE, $terms_date);
    }

    public function get_approved()
    {
        return $this->get_default_property(self :: PROPERTY_APPROVED);
    }

    /**
     * Checks if this user is a platform admin or not
     *
     * @return boolean true if the user is a platforma admin, false otherwise
     */
    public function is_platform_admin()
    {
        return ($this->get_platformadmin() == 1 ? true : false);
    }

    public static function admin()
    {
        $user_id = \Chamilo\Libraries\Platform\Session\Session :: get_user_id();
        if ($user_id && $user_id != '')
        {
            return DataManager :: retrieve_by_id(User :: class_name(), (int) $user_id)->is_platform_admin();
        }
        else
        {
            return false;
        }
    }

    /*
     */
    public function is_anonymous_user()
    {
        return ($this->get_status() == self :: STATUS_ANONYMOUS ? true : false);
    }

    /**
     * Checks if this user is a teacher or not
     *
     * @return boolean true if the user is a teacher, false otherwise
     */
    public function is_teacher()
    {
        return ($this->get_status() == self :: STATUS_TEACHER ? true : false);
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

    /**
     * Instructs the Datamanager to create this user.
     *
     * @return boolean True if success, false otherwise
     */
    public function create()
    {
        $this->set_registration_date(time());
        $this->set_security_token(sha1(time() . uniqid()));

        if (! parent :: create($this))
        {
            return false;
        }

        return true;
    }

    public function delete()
    {
        $group_rel_user_condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser :: class_name(), GroupRelUser :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_id()));
        $success = \Chamilo\Core\Group\Storage\DataManager :: deletes(
            GroupRelUser :: class_name(),
            $group_rel_user_condition);

        if ($success)
        {
            return parent :: delete();
        }
        else
        {
            return false;
        }
    }

    public function get_groups($only_retrieve_ids = false)
    {
        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_all_subscribed_groups_array(
            $this->get_id(),
            $only_retrieve_ids);
    }

    public function get_user_groups()
    {
        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_user_groups($this->get_id());
    }

    public function get_status_name()
    {
        if ($this->get_platformadmin() == '1')
        {
            return Translation :: get('PlatformAdministrator');
        }

        switch ($this->get_status())
        {
            case self :: STATUS_ANONYMOUS :
                return Translation :: get('Anonymous');
                break;
            case self :: STATUS_STUDENT :
                return Translation :: get('Student');
                break;
            case self :: STATUS_TEACHER :
                return Translation :: get('CourseAdmin');
                break;
            default :
                return Translation :: get('Student');
        }
    }

    public function get_fullname_format_options()
    {
        $options = array();
        $options[self :: NAME_FORMAT_FIRST] = Translation :: get('FirstName') . ' ' . Translation :: get('LastName');
        $options[self :: NAME_FORMAT_LAST] = Translation :: get('LastName') . ' ' . Translation :: get('FirstName');
        return $options;
    }

    /**
     * Returns all (unique) properties by which a DataClass object can be cached
     *
     * @param $extended_property_names multitype:string
     * @return multitype:string
     */
    public static function get_cacheable_property_names($extended_property_names = array())
    {
        return parent :: get_cacheable_property_names(array(self :: PROPERTY_USERNAME));
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
            $system_date = Manager :: get_date_terms_and_conditions_last_modified();
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
