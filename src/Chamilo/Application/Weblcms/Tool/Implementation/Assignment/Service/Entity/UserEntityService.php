<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserEntityService
{
    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var array
     */
    protected $targetUsersCache = [];

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->countTargetUsersForContentObjectPublication(
            $contentObjectPublication, $this->getTargetUserIdsForPublication($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     *
     */
    public function countEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {

    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int[]
     */
    protected function getTargetUserIdsForPublication(ContentObjectPublication $contentObjectPublication)
    {
        $id = $contentObjectPublication->getId();

        if(!array_key_exists($id, $this->targetUsersCache))
        {
            $this->targetUsersCache[$id] = DataManager::get_publication_target_users_by_publication_id(
                $contentObjectPublication->getId()
            );
        }

        return $this->targetUsersCache[$id];
    }
}