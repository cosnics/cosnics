<?php

namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\User\Storage\DataClass\User;

class ContentObjectCopierWrapper
{
    /**
     * Copies a given content object
     *
     * @param ContentObject $contentObject
     * @param User $user
     * @param int $categoryId
     *
     * @return \int[]
     */
    public function copyContentObject(ContentObject $contentObject, User $user, $categoryId)
    {
        $contentObjectCopier = new ContentObjectCopier(
            $user, array($contentObject->getId()), new PersonalWorkspace($contentObject->get_owner()),
            $contentObject->get_owner_id(), new PersonalWorkspace($user), $user->getId(),
            $categoryId
        );

        return $contentObjectCopier->run();
    }
}