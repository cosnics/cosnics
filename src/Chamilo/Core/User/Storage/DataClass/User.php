<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class User extends DataClass
{
    public const ANONYMOUS_ID = '1';

    public const CONTEXT = Manager::CONTEXT;

    public const NAME_FORMAT_FIRST = 0;
    public const NAME_FORMAT_LAST = 1;

    public const PROPERTY_ACTIVATION_DATE = 'activation_date';
    public const PROPERTY_ACTIVE = 'active';
    public const PROPERTY_APPROVED = 'approved';
    public const PROPERTY_AUTH_SOURCE = 'auth_source';
    public const PROPERTY_CREATOR_ID = 'creator_id';
    public const PROPERTY_DATABASE_QUOTA = 'database_quota';
    public const PROPERTY_DISK_QUOTA = 'disk_quota';
    public const PROPERTY_EMAIL = 'email';
    public const PROPERTY_EXPIRATION_DATE = 'expiration_date';
    public const PROPERTY_EXTERNAL_UID = 'external_uid';
    public const PROPERTY_FIRSTNAME = 'firstname';
    public const PROPERTY_LASTNAME = 'lastname';
    public const PROPERTY_OFFICIAL_CODE = 'official_code';
    public const PROPERTY_PASSWORD = 'password';
    public const PROPERTY_PHONE = 'phone';
    public const PROPERTY_PICTURE_URI = 'picture_uri';
    public const PROPERTY_PLATFORMADMIN = 'admin';
    public const PROPERTY_REGISTRATION_DATE = 'registration_date';
    public const PROPERTY_SECURITY_TOKEN = 'security_token';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TERMS_DATE = 'terms_date';
    public const PROPERTY_USERNAME = 'username';

    public const STATUS_ANONYMOUS = 0;
    public const STATUS_STUDENT = 5;
    public const STATUS_TEACHER = 1;

    /**
     * Instructs the Datamanager to create this user.
     *
     * @return bool True if success, false otherwise
     */
    public function create(): bool
    {
        $this->set_registration_date(time());
        $this->set_security_token(sha1(time() . uniqid()));

        if (!parent::create($this))
        {
            return false;
        }

        return true;
    }

    public function delete(): bool
    {
        $success = $this->getGroupMembershipService()->unsubscribeUserFromAllGroups($this);

        if ($success)
        {
            return parent::delete();
        }
        else
        {
            return false;
        }
    }

    public static function fullname(string $first_name, string $last_name): string
    {
        /**
         * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
         */
        $configurationConsulter =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ConfigurationConsulter::class);

        $format = $configurationConsulter->getSetting([Manager::CONTEXT, 'fullname_format']);

        switch ($format)
        {
            case self::NAME_FORMAT_LAST :
                return $last_name . ' ' . $first_name;
            default :
                return $first_name . ' ' . $last_name;
        }
    }

    public function getAuthenticationSource(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_AUTH_SOURCE);
    }

    public static function getCacheablePropertyNames(array $cacheablePropertyNames = []): array
    {
        return parent::getCacheablePropertyNames([self::PROPERTY_USERNAME]);
    }

    /**
     * Get the default properties of all users.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
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
            ]
        );
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            GroupMembershipService::class
        );
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(GroupsTreeTraverser::class);
    }

    public function getPlatformAdmin(): int
    {
        return (int) $this->getDefaultProperty(self::PROPERTY_PLATFORMADMIN);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'user_user';
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
     * Returns the creator ID for this user.
     *
     * @return Int The ID
     */
    public function get_creator_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_ID);
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>|string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use GroupsTreeTraverser::findAllSubscribedGroupIdentifiersForUserIdentifier() or
     *             GroupsTreeTraverser::findAllSubscribedGroupsForUserIdentifier() based ont he value of
     *             $only_retrieve_ids
     */
    public function get_groups($only_retrieve_ids = false)
    {
        if ($only_retrieve_ids)
        {
            return $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($this->getId());
        }
        else
        {
            return $this->getGroupsTreeTraverser()->findAllSubscribedGroupsForUserIdentifier($this->getId());
        }
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
     * @deprecated Use User::getPlatformAdmin()
     */
    public function get_platformadmin(): int
    {
        return $this->getPlatformAdmin();
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
        if ($this->getPlatformAdmin() == '1')
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
     * Returns the date on wich the user has last accepted the terms and conditions
     *
     * @return <int> terms_date
     */
    public function get_terms_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_TERMS_DATE);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function get_user_groups(): ArrayCollection
    {
        return $this->getGroupsTreeTraverser()->findDirectlySubscribedGroupsForUserIdentifier($this->getId());
    }

    public function get_username(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USERNAME);
    }

    public function isPlatformAdmin(): bool
    {
        return $this->getPlatformAdmin() == 1;
    }

    public function is_active(): bool
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

    public function is_anonymous_user(): bool
    {
        return $this->get_status() == self::STATUS_ANONYMOUS;
    }

    /**
     * @deprecated Use User::isPlatformAdmin()
     */
    public function is_platform_admin(): bool
    {
        return $this->isPlatformAdmin();
    }

    public function is_teacher(): bool
    {
        return $this->get_status() == self::STATUS_TEACHER;
    }

    public function set_activation_date(?int $activation_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVATION_DATE, $activation_date);
    }

    public function set_active(int $active): void
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVE, $active);
    }

    public function set_approved(int $approved): void
    {
        $this->setDefaultProperty(self::PROPERTY_APPROVED, $approved);
    }

    public function set_auth_source(string $auth_source): void
    {
        $this->setDefaultProperty(self::PROPERTY_AUTH_SOURCE, $auth_source);
    }

    public function set_creator_id(?int $creator_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_CREATOR_ID, $creator_id);
    }

    public function set_database_quota(int $database_quota): void
    {
        $this->setDefaultProperty(self::PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    public function set_disk_quota(int $disk_quota): void
    {
        $this->setDefaultProperty(self::PROPERTY_DISK_QUOTA, $disk_quota);
    }

    public function set_email(string $email): void
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL, $email);
    }

    public function set_expiration_date(int $expiration_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_EXPIRATION_DATE, $expiration_date);
    }

    public function set_external_uid(?string $external_uid): void
    {
        $this->setDefaultProperty(self::PROPERTY_EXTERNAL_UID, $external_uid);
    }

    public function set_firstname(string $firstname): void
    {
        $this->setDefaultProperty(self::PROPERTY_FIRSTNAME, $firstname);
    }

    public function set_lastname(string $lastname): void
    {
        $this->setDefaultProperty(self::PROPERTY_LASTNAME, $lastname);
    }

    public function set_official_code(?string $official_code): void
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICIAL_CODE, $official_code);
    }

    public function set_password(?string $password): void
    {
        $this->setDefaultProperty(self::PROPERTY_PASSWORD, $password);
    }

    public function set_phone(?string $phone): void
    {
        $this->setDefaultProperty(self::PROPERTY_PHONE, $phone);
    }

    public function set_picture_uri(?string $picture_uri): void
    {
        $this->setDefaultProperty(self::PROPERTY_PICTURE_URI, $picture_uri);
    }

    public function set_platformadmin(int $admin): void
    {
        $this->setDefaultProperty(self::PROPERTY_PLATFORMADMIN, $admin);
    }

    public function set_registration_date(int $registration_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_REGISTRATION_DATE, $registration_date);
    }

    public function set_security_token(string $security_token): void
    {
        $this->setDefaultProperty(self::PROPERTY_SECURITY_TOKEN, $security_token);
    }

    public function set_status(int $status): void
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    public function set_term_date(int $terms_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_TERMS_DATE, $terms_date);
    }

    public function set_username(string $username): void
    {
        $this->setDefaultProperty(self::PROPERTY_USERNAME, $username);
    }

    /**
     * check if user has seen/agreed with the latest terms and conditions return false if user has not seen latest
     * version, true when user has seen latest version
     */
    public function terms_conditions_uptodate(): bool
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
