<?php
namespace Chamilo\Core\User\Storage\Entity;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * @package Chamilo\Core\User\Storage\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
#[ORM\Entity(repositoryClass: 'Chamilo\Core\User\Storage\Repository\UserRepository')]
#[ORM\Table(name: 'user_user')]
#[ORM\Index(name: 'id_idx', columns: ['id'])]
class User implements DoctrineEntityInterface
{
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

    #[ORM\Column(name: 'active', type: 'boolean')]
    protected bool $active;

    #[ORM\Column(name: 'auth_source', type: 'string')]
    protected string $authenticationSource;

    #[ORM\Column(name: 'configuration', type: 'json')]
    protected array $configuration = [];

    #[ORM\Column(name: 'creator_id', type: 'uuid', nullable: true)]
    protected ?Uuid $creatorIdentifier;

    #[ORM\Column(name: 'email', type: 'string', nullable: true)]
    protected ?string $email;

    #[ORM\Column(name: 'firstname', type: 'string', nullable: true)]
    protected ?string $givenName;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected Uuid $identifier;

    #[ORM\Column(name: 'official_code', type: 'string', nullable: true)]
    protected ?string $officialCode;

    #[ORM\Column(name: 'password', type: 'string')]
    protected string $password;

    #[ORM\Column(name: 'picture_uri', type: 'string', nullable: true)]
    protected ?string $pictureUri;

    #[ORM\Column(name: 'admin', type: 'boolean')]
    protected bool $platformAdministrator;

    #[ORM\Column(name: 'registration_date', type: 'integer', nullable: true)]
    protected int $registrationDate;

    #[ORM\Column(name: 'security_token', type: 'string', nullable: true)]
    protected ?string $securityToken;

    #[ORM\Column(name: 'lastname', type: 'string', nullable: true)]
    protected ?string $surname;

    #[ORM\Column(name: 'username', type: 'string')]
    protected string $username;

    public function __construct()
    {
        $this->setIdentifier(new UuidV7());
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function isActive(): bool
    {
        return $this->getActive();
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public static function getAlias(): string
    {
        return 't_usr_usr';
    }

    public function getAuthenticationSource(): string
    {
        return $this->authenticationSource;
    }

    public function setAuthenticationSource(string $authenticationSource): static
    {
        $this->authenticationSource = $authenticationSource;

        return $this;
    }

    public function getCreatorIdentifier(): ?string
    {
        return $this->creatorIdentifier;
    }

    public function setCreatorIdentifier(?string $creatorIdentifier): static
    {
        $this->creatorIdentifier = $creatorIdentifier;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getGivenName() . ' ' . $this->getSurname();
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): static
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getIdentifier(): Uuid
    {
        return $this->identifier;
    }

    public function setIdentifier(Uuid $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getOfficialCode(): ?string
    {
        return $this->officialCode;
    }

    public function setOfficialCode(?string $officialCode): static
    {
        $this->officialCode = $officialCode;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPictureUri(): ?string
    {
        return $this->pictureUri;
    }

    public function setPictureUri(?string $pictureUri): static
    {
        $this->pictureUri = $pictureUri;

        return $this;
    }

    public function getPlatformAdmin(): bool
    {
        return $this->platformAdministrator;
    }

    public function getRegistrationDate(): ?int
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(int $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getSecurityToken(): ?string
    {
        return $this->securityToken;
    }

    public function setSecurityToken(?string $securityToken): static
    {
        $this->securityToken = $securityToken;

        return $this;
    }

    public function getSetting(string $variable, mixed $defaultValue = null): mixed
    {
        return array_key_exists($variable, $this->configuration) ? $this->configuration[$variable] : $defaultValue;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->getUsername();
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isPlatformAdministrator(): bool
    {
        return $this->getPlatformAdmin();
    }

    public function setPlatformAdministrator(bool $platformAdministrator): static
    {
        $this->platformAdministrator = $platformAdministrator;

        return $this;
    }

    public function setSetting(string $variable, mixed $value): static
    {
        $this->configuration[$variable] = $value;

        return $this;
    }
}
