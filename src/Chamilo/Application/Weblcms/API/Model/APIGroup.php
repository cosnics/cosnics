<?php

namespace Chamilo\Application\Weblcms\API\Model;

class APIGroup
{
    protected int $id;
    protected string $name;
    protected ?string $description = null;
    protected ?bool $isPublic = null;
    protected ?bool $followedByUser = null;
    protected ?string $joinLevel = null;
    protected ?int $membersCount = null;
    protected ?string $avatarUrl = null;
    protected ?string $contextType = null;
    protected int $courseId;
    protected ?string $role = null;
    protected ?int $groupCategoryId = null;
    protected ?string $sisGroupId = null;
    protected ?int $sisImportId = null;
    protected ?int $storageQuotaMb = null;
    protected ?array $permissions = null;
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
