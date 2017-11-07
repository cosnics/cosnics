<?php
namespace Chamilo\Core\Repository\Workspace;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

class PersonalWorkspace implements WorkspaceInterface
{
    const WORKSPACE_TYPE = 1;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $owner;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     */
    public function __construct(User $owner)
    {
        $this->owner = $owner;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface::getCreatorId()
     */
    public function getCreatorId()
    {
        return $this->getOwner()->getId();
    }

    /*
     * (non-PHPdoc)
     * @see \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface::getId()
     */
    public function getId()
    {
        return $this->getOwner()->getId();
    }

    /*
     * (non-PHPdoc)
     * @see \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface::getWorkspaceType()
     */
    public function getWorkspaceType()
    {
        return self::WORKSPACE_TYPE;
    }

    /*
     * (non-PHPdoc)
     * @see \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface::getTitle()
     */
    public function getTitle()
    {
        return Translation::get('MyRepository');
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface::getHash()
     */
    public function getHash()
    {
        return md5(serialize(array(__CLASS__, $this->getWorkspaceType(), $this->getId())));
    }
}