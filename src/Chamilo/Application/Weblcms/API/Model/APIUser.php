<?php

namespace Chamilo\Application\Weblcms\API\Model;
use OpenApi\Attributes as OA;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
#[OA\Schema(title: 'User')]
class APIUser
{
    #[OA\Property]
    protected int $id;
    #[OA\Property]
    protected string $name;
    #[OA\Property]
    protected ?string $sortableName = null;
    #[OA\Property]
    protected string $lastName;
    #[OA\Property]
    protected string $firstName;
    #[OA\Property]
    protected ?string $shortName = null;
    #[OA\Property]
    protected ?string $sisUserId = null;
    #[OA\Property]
    protected ?int $sisImportId = null;
    #[OA\Property]
    protected ?string $integrationId = null;
    #[OA\Property]
    protected string $loginId;
    #[OA\Property]
    protected ?string $avatarUrl = null;
    #[OA\Property]
    protected ?string $avatarState = null;
    #[OA\Property]
    protected ?array $enrollments = null;
    #[OA\Property]
    protected string $email;
    #[OA\Property]
    protected ?string $locale = null;
    #[OA\Property]
    protected ?string $lastLogin = null;
    #[OA\Property]
    protected ?string $timeZone = null;
    #[OA\Property]
    protected ?string $bio = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): APIUser
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): APIUser
    {
        $this->name = $name;
        return $this;
    }

    public function getSortableName(): ?string
    {
        return $this->sortableName;
    }

    public function setSortableName(?string $sortableName): APIUser
    {
        $this->sortableName = $sortableName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): APIUser
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): APIUser
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): APIUser
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getSisUserId(): ?string
    {
        return $this->sisUserId;
    }

    public function setSisUserId(?string $sisUserId): APIUser
    {
        $this->sisUserId = $sisUserId;
        return $this;
    }

    public function getSisImportId(): ?int
    {
        return $this->sisImportId;
    }

    public function setSisImportId(?int $sisImportId): APIUser
    {
        $this->sisImportId = $sisImportId;
        return $this;
    }

    public function getIntegrationId(): ?string
    {
        return $this->integrationId;
    }

    public function setIntegrationId(?string $integrationId): APIUser
    {
        $this->integrationId = $integrationId;
        return $this;
    }

    public function getLoginId(): string
    {
        return $this->loginId;
    }

    public function setLoginId(string $loginId): APIUser
    {
        $this->loginId = $loginId;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): APIUser
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getAvatarState(): ?string
    {
        return $this->avatarState;
    }

    public function setAvatarState(?string $avatarState): APIUser
    {
        $this->avatarState = $avatarState;
        return $this;
    }

    public function getEnrollments(): ?array
    {
        return $this->enrollments;
    }

    public function setEnrollments(?array $enrollments): APIUser
    {
        $this->enrollments = $enrollments;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): APIUser
    {
        $this->email = $email;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): APIUser
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLastLogin(): ?string
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?string $lastLogin): APIUser
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(?string $timeZone): APIUser
    {
        $this->timeZone = $timeZone;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): APIUser
    {
        $this->bio = $bio;
        return $this;
    }
}
