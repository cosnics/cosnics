<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\ConfigurableDataClassInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\ConfigurableDataClassTrait;

/**
 * @package Chamilo\Core\User\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class User extends DataClass implements ConfigurableDataClassInterface, UuidDataClassInterface
{
    use ConfigurableDataClassTrait;

    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_ACTIVE = 'active';
    public const string PROPERTY_AUTHENTICATION_SOURCE = 'auth_source';
    public const string PROPERTY_CREATOR_IDENTIFIER = 'creator_id';
    public const string PROPERTY_EMAIL = 'email';
    public const string PROPERTY_GIVEN_NAME = 'firstname';
    public const string PROPERTY_OFFICIAL_CODE = 'official_code';
    public const string PROPERTY_PASSWORD = 'password';
    public const string PROPERTY_PICTURE_URI = 'picture_uri';
    public const string PROPERTY_PLATFORM_ADMINISTRATOR = 'admin';
    public const string PROPERTY_REGISTRATION_DATE = 'registration_date';
    public const string PROPERTY_SECURITY_TOKEN = 'security_token';
    public const string PROPERTY_SURNAME = 'lastname';
    public const string PROPERTY_USERNAME = 'username';

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
        $extendedPropertyNames[] = self::PROPERTY_PLATFORM_ADMINISTRATOR;
        $extendedPropertyNames[] = self::PROPERTY_OFFICIAL_CODE;
        $extendedPropertyNames[] = self::PROPERTY_PICTURE_URI;
        $extendedPropertyNames[] = self::PROPERTY_CREATOR_IDENTIFIER;
        $extendedPropertyNames[] = self::PROPERTY_REGISTRATION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_ACTIVE;
        $extendedPropertyNames[] = self::PROPERTY_SECURITY_TOKEN;
        $extendedPropertyNames[] = self::PROPERTY_CONFIGURATION;

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

    public function setAuthenticationSource(string $authenticationSource): void
    {
        $this->setDefaultProperty(self::PROPERTY_AUTHENTICATION_SOURCE, $authenticationSource);
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

    public function setOfficialCode(?string $officialCode): void
    {
        $this->setDefaultProperty(self::PROPERTY_OFFICIAL_CODE, $officialCode);
    }

    public function setPassword(?string $password): void
    {
        $this->setDefaultProperty(self::PROPERTY_PASSWORD, $password);
    }

    public function setPictureUri(?string $pictureUri): void
    {
        $this->setDefaultProperty(self::PROPERTY_PICTURE_URI, $pictureUri);
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

    public function setSurname(?string $surname): void
    {
        $this->setDefaultProperty(self::PROPERTY_SURNAME, $surname);
    }

    public function setUsername(string $username): void
    {
        $this->setDefaultProperty(self::PROPERTY_USERNAME, $username);
    }
}
