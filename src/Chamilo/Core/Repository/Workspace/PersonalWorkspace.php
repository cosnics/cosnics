<?php
namespace Chamilo\Core\Repository\Workspace;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

class PersonalWorkspace implements WorkspaceInterface
{
    public const WORKSPACE_TYPE = 1;

    private User $owner;

    public function __construct(User $owner)
    {
        $this->owner = $owner;
    }

    public function getCreatorId(): ?string
    {
        return $this->getOwner()->getId();
    }

    public function getHash(): string
    {
        return md5(serialize([__CLASS__, $this->getWorkspaceType(), $this->getId()]));
    }

    public function getId(): ?string
    {
        return $this->getOwner()->getId();
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getTitle(): string
    {
        return Translation::get('MyRepository');
    }

    public function getWorkspaceType(): int
    {
        return self::WORKSPACE_TYPE;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }
}