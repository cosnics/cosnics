<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class EntryFormHandlerParameters
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class EntryFormHandlerParameters
{
    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User[]
     */
    protected $targetUsers;

    /**
     * @var RubricData
     */
    protected $rubricData;

    /**
     * @var ContextIdentifier
     */
    protected $contextIdentifier;

    /**
     * EntryFormHandlerParameters constructor.
     *
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param array $targetUsers
     */
    public function __construct(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier, array $targetUsers
    )
    {
        if (empty($targetUsers))
        {
            throw new \InvalidArgumentException('The target users can not be empty');
        }

        foreach ($targetUsers as $targetUser)
        {
            if (!$targetUser instanceof User)
            {
                throw new \InvalidArgumentException('The target user must be an instance of user.');
            }
        }

        $this->user = $user;
        $this->rubricData = $rubricData;
        $this->contextIdentifier = $contextIdentifier;
        $this->targetUsers = $targetUsers;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return User[]
     */
    public function getTargetUsers(): ?array
    {
        return $this->targetUsers;
    }

    /**
     * @return RubricData
     */
    public function getRubricData(): ?RubricData
    {
        return $this->rubricData;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ?ContextIdentifier
    {
        return $this->contextIdentifier;
    }
}
