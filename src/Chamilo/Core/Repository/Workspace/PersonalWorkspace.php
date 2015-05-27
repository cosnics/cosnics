<?php
namespace Chamilo\Core\Repository\Workspace;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

class PersonalWorkspace implements WorkspaceInterface
{

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

    public function getId()
    {
        throw new \Exception(Translation :: get('PersonalWorkspaceImplementedImplicitlyNoIdentifier'));
    }
}