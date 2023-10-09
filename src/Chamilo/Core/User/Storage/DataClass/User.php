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
     * @deprecated Use UserService::createUser()
     */
    public function create(): bool
    {
        return $this->getUserService()->createUser($this);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use UserService::deleteUser()
     */
    public function delete(): bool
    {
        return $this->getUserService()->deleteUser($this);
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
     * @return string[]
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

    public function getPlatformAdmin(): bool
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_PLATFORMADMIN);
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

    public function get_active(): bool
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_ACTIVE);
    }

    public function get_approved(): bool
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_APPROVED);
    }

    /**
     * @deprecated Use getAuthenticationSource() now
     */
    public function get_auth_source(): string
    {
        return $this->getAuthenticationSource();
    }

    public function get_creator_id(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_ID);
    }

    public function get_database_quota(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DATABASE_QUOTA);
    }

    public function get_disk_quota(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DISK_QUOTA);
    }

    public function get_email(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL);
    }

    public function get_expiration_date(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_EXPIRATION_DATE);
    }

    /**
     * Returns the external authentication system unique id for this user (useful for instance with : Shibboleth,
     * OpenID, LDAP, ...)
     */
    public function get_external_uid(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_EXTERNAL_UID);
    }

    public function get_firstname(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_FIRSTNAME);
    }

    public function get_fullname(): string
    {
        return self::fullname($this->get_firstname(), $this->get_lastname());
    }

    /**
     * @return string[]
     */
    public static function get_fullname_format_options(): array
    {
        $options = [];
        $options[self::NAME_FORMAT_FIRST] = Translation::get('FirstName') . ' ' . Translation::get('LastName');
        $options[self::NAME_FORMAT_LAST] = Translation::get('LastName') . ' ' . Translation::get('FirstName');

        return $options;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Group\Storage\DataClass\Group>|string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use GroupsTreeTraverser::findAllSubscribedGroupIdentifiersForUserIdentifier() or
     *             GroupsTreeTraverser::findAllSubscribedGroupsForUserIdentifier() based ont he value of
     *             $only_retrieve_ids
     */
    public function get_groups(bool $only_retrieve_ids = false): array|ArrayCollection
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

    public function get_lastname(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_LASTNAME);
    }

    public function get_official_code(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICIAL_CODE);
    }

    public function get_password(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PASSWORD);
    }

    public function get_phone(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_PHONE);
    }

    public function get_picture_uri(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_PICTURE_URI);
    }

    /**
     * @deprecated Use User::getPlatformAdmin()
     */
    public function get_platformadmin(): bool
    {
        return $this->getPlatformAdmin();
    }

    public function get_registration_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_REGISTRATION_DATE);
    }

    public function get_security_token(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_SECURITY_TOKEN);
    }

    public function get_status(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    public function get_status_name(): string
    {
        if ($this->getPlatformAdmin() == '1')
        {
            return Translation::get('PlatformAdministrator');
        }

        switch ($this->get_status())
        {
            case self::STATUS_ANONYMOUS :
                return Translation::get('Anonymous');
            case self::STATUS_TEACHER :
                return Translation::get('CourseAdmin');
            default :
                return Translation::get('Student');
        }
    }

    public function get_terms_date(): int
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
        return $this->getPlatformAdmin();
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

    public function set_active(bool $active): void
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVE, $active);
    }

    public function set_approved(bool $approved): void
    {
        $this->setDefaultProperty(self::PROPERTY_APPROVED, $approved);
    }

    public function set_auth_source(string $auth_source): void
    {
        $this->setDefaultProperty(self::PROPERTY_AUTH_SOURCE, $auth_source);
    }

    public function set_creator_id(?string $creator_id): void
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

    public function set_email(?string $email): void
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

    public function set_firstname(?string $firstname): void
    {
        $this->setDefaultProperty(self::PROPERTY_FIRSTNAME, $firstname);
    }

    public function set_lastname(?string $lastname): void
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

    public function set_platformadmin(bool $admin): void
    {
        $this->setDefaultProperty(self::PROPERTY_PLATFORMADMIN, $admin);
    }

    public function set_registration_date(int $registration_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_REGISTRATION_DATE, $registration_date);
    }

    public function set_security_token(?string $security_token): void
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
}
