<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;

class ContentObjectCopierWrapper
{
    protected Workspace $workspace;

    public function __construct(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * Copies a given content object
     *
     * @param ContentObject $contentObject
     * @param User $user
     * @param int $categoryId
     *
     * @return \int[]
     */
    public function copyContentObject(ContentObject $contentObject, User $user, int $categoryId = 0)
    {
        $contentObjectCopier = new ContentObjectCopier(
            $user, [$contentObject->getId()], $this->workspace, $contentObject->get_owner_id(), $this->workspace,
            $user->getId(), $categoryId
        );

        return $contentObjectCopier->run();
    }
}