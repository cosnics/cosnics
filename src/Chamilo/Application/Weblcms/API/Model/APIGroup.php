<?php

namespace Chamilo\Application\Weblcms\API\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Group')]
class APIGroup
{
    #[OA\Property]
    protected int $id;
    #[OA\Property]
    protected string $name;
    #[OA\Property]
    protected ?string $description = null;
    #[OA\Property]
    protected ?bool $isPublic = null;
    #[OA\Property]
    protected ?bool $followedByUser = null;
    #[OA\Property]
    protected ?string $joinLevel = null;
    #[OA\Property]
    protected ?int $membersCount = null;
    #[OA\Property]
    protected ?string $avatarUrl = null;
    #[OA\Property]
    protected ?string $contextType = null;
    #[OA\Property]
    protected int $courseId;
    #[OA\Property]
    protected ?string $role = null;
    #[OA\Property]
    protected ?int $groupCategoryId = null;
    #[OA\Property]
    protected ?string $sisGroupId = null;
    #[OA\Property]
    protected ?int $sisImportId = null;
    #[OA\Property]
    protected ?int $storageQuotaMb = null;
    #[OA\Property]
    protected ?array $permissions = null;
    #[OA\Property]
    protected ?array $users = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): APIGroup
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): APIGroup
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): APIGroup
    {
        $this->description = $description;
        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): APIGroup
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getFollowedByUser(): ?bool
    {
        return $this->followedByUser;
    }

    public function setFollowedByUser(?bool $followedByUser): APIGroup
    {
        $this->followedByUser = $followedByUser;
        return $this;
    }

    public function getJoinLevel(): ?string
    {
        return $this->joinLevel;
    }

    public function setJoinLevel(?string $joinLevel): APIGroup
    {
        $this->joinLevel = $joinLevel;
        return $this;
    }

    public function getMembersCount(): ?int
    {
        return $this->membersCount;
    }

    public function setMembersCount(?int $membersCount): APIGroup
    {
        $this->membersCount = $membersCount;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): APIGroup
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    public function getContextType(): ?string
    {
        return $this->contextType;
    }

    public function setContextType(?string $contextType): APIGroup
    {
        $this->contextType = $contextType;
        return $this;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function setCourseId(int $courseId): APIGroup
    {
        $this->courseId = $courseId;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): APIGroup
    {
        $this->role = $role;
        return $this;
    }

    public function getGroupCategoryId(): ?int
    {
        return $this->groupCategoryId;
    }

    public function setGroupCategoryId(?int $groupCategoryId): APIGroup
    {
        $this->groupCategoryId = $groupCategoryId;
        return $this;
    }

    public function getSisGroupId(): ?string
    {
        return $this->sisGroupId;
    }

    public function setSisGroupId(?string $sisGroupId): APIGroup
    {
        $this->sisGroupId = $sisGroupId;
        return $this;
    }

    public function getSisImportId(): ?int
    {
        return $this->sisImportId;
    }

    public function setSisImportId(?int $sisImportId): APIGroup
    {
        $this->sisImportId = $sisImportId;
        return $this;
    }

    public function getStorageQuotaMb(): ?int
    {
        return $this->storageQuotaMb;
    }

    public function setStorageQuotaMb(?int $storageQuotaMb): APIGroup
    {
        $this->storageQuotaMb = $storageQuotaMb;
        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): APIGroup
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getUsers(): ?array
    {
        return $this->users;
    }

    public function setUsers(?array $users): APIGroup
    {
        $this->users = $users;
        return $this;
    }
}
