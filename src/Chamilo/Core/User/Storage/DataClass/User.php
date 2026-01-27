<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class User extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ACTIVATION_DATE = 'activation_date';
    public const PROPERTY_ACTIVE = 'active';
    public const PROPERTY_AUTHENTICATION_SOURCE = 'auth_source';
    public const PROPERTY_CREATOR_IDENTIFIER = 'creator_id';
    public const PROPERTY_EMAIL = 'email';
    public const PROPERTY_GIVEN_NAME = 'firstname';

    public const PROPERTY_OFFICIAL_CODE = 'official_code';

    public const PROPERTY_PASSWORD = 'password';

    public const PROPERTY_PICTURE_URI = 'picture_uri';

    public const PROPERTY_PLATFORM_ADMINISTRATOR = 'admin';

    public const PROPERTY_REGISTRATION_DATE = 'registration_date';

    public const PROPERTY_SECURITY_TOKEN = 'security_token';

    public const PROPERTY_STATUS = 'status';

    public const PROPERTY_SURNAME = 'lastname';

    public const PROPERTY_USERNAME = 'username';

    public const STATUS_ANONYMOUS = 0;
    public const STATUS_STUDENT = 5;
    public const STATUS_TEACHER = 1;

    public function getActive(): bool
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_ACTIVE);
    }

    public function getAuthenticationSource(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_AUTHENTICATION_SOURCE);
    }

    public function getCreatorIdentifier(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATOR_IDENTIFIER);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_SURNAME;
        $extendedPropertyNames[] = self::PROPERTY_GIVEN_NAME;
        $extendedPropertyNames[] = self::PROPERTY_USERNAME;
        $extendedPropertyNames[] = self::PROPERTY_PASSWORD;
        $extendedPropertyNames[] = self::PROPERTY_AUTHENTICATION_SOURCE;
        $extendedPropertyNames[] = self::PROPERTY_EMAIL;
        $extendedPropertyNames[] = self::PROPERTY_STATUS;
        $extendedPropertyNames[] = self::PROPERTY_PLATFORM_ADMINISTRATOR;
        $extendedPropertyNames[] = self::PROPERTY_OFFICIAL_CODE;
        $extendedPropertyNames[] = self::PROPERTY_PICTURE_URI;
        $extendedPropertyNames[] = self::PROPERTY_CREATOR_IDENTIFIER;
        $extendedPropertyNames[] = self::PROPERTY_ACTIVATION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_REGISTRATION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_ACTIVE;
        $extendedPropertyNames[] = self::PROPERTY_SECURITY_TOKEN;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getEmail(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL);
    }

    public function getFullName(): string
    {
        return $this->getGivenName() . ' ' . $this->getSurname();
    }

    public function getGivenName(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_GIVEN_NAME);
    }

    public function getOfficialCode(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_OFFICIAL_CODE);
    }

    public function getPassword(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PASSWORD);
    }

    public function getPictureUri(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_PICTURE_URI);
    }

    public function getPlatformAdmin(): bool
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_PLATFORM_ADMINISTRATOR);
    }

    public function getRegistrationDate()
    {
        return $this->getDefaultProperty(self::PROPERTY_REGISTRATION_DATE);
    }

    public function getSecurityToken(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_SECURITY_TOKEN);
    }

    public function getStatus(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'user_user';
    }

    public function getSurname(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_SURNAME);
    }

    public function getUsername(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USERNAME);
    }

    public function isActive(): bool
    {
        return $this->getActive();
    }

    public function isPlatformAdministrator(): bool
    {
        return $this->getPlatformAdmin();
    }

    public function setActive(bool $active): void
    {
        $this->setDefaultProperty(self::PROPERTY_ACTIVE, (int) $active);
    }

    public function setAuthenticationSource(string $auth_source): void
    {
        $this->setDefaultProperty(self::PROPERTY_AUTHENTICATION_SOURCE, $auth_source);
    }

    public function setCreatorIdentifier(?string $creatorIdentifier): void
    {
        $this->setDefaultProperty(self::PROPERTY_CREATOR_IDENTIFIER, $creatorIdentifier);
    }

    public function setEmail(?string $email): void
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL, $email);
    }

    public function setGivenName(?string $givenName): void
    {
        $this->setDefaultProperty(self::PROPERTY_GIVEN_NAME, $givenName);
    }

    public function setOfficialCode(?string $official_code): void
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICIAL_CODE, $official_code);
    }

    public function setPassword(?string $password): void
    {
        $this->setDefaultProperty(self::PROPERTY_PASSWORD, $password);
    }

    public function setPictureUri(?string $picture_uri): void
    {
        $this->setDefaultProperty(self::PROPERTY_PICTURE_URI, $picture_uri);
    }

    public function setPlatformAdministrator(bool $admin): void
    {
        $this->setDefaultProperty(self::PROPERTY_PLATFORM_ADMINISTRATOR, (int) $admin);
    }

    public function setRegistrationDate(int $registrationDate): void
    {
        $this->setDefaultProperty(self::PROPERTY_REGISTRATION_DATE, $registrationDate);
    }

    public function setSecurityToken(?string $securityToken): void
    {
        $this->setDefaultProperty(self::PROPERTY_SECURITY_TOKEN, $securityToken);
    }

    public function setStatus(int $status): void
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    public function setSurname(?string $surname): void
    {
        $this->setDefaultProperty(self::PROPERTY_SURNAME, $surname);
    }

    public function setUsername(string $username): void
    {
        $this->setDefaultProperty(self::PROPERTY_USERNAME, $username);
    }
}
