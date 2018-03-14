<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface EntityServiceInterface
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface EntityServiceInterface
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator|\Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = []
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication, Condition $condition = null);

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int
     */
    public function countEntitiesWithEntries(ContentObjectPublication $contentObjectPublication);

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator | DataClass[]
     */
    public function retrieveEntitiesWithEntries(ContentObjectPublication $contentObjectPublication);

    /**
     * @return string
     */
    public function getPluralEntityName();

    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTable(
        Application $application, AssignmentDataProvider $assignmentDataProvider,
        ContentObjectPublication $contentObjectPublication
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(ContentObjectPublication $contentObjectPublication, User $currentUser);

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(ContentObjectPublication $contentObjectPublication, User $currentUser);

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, ContentObjectPublication $contentObjectPublication, $entityId);

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForEntity($entityId);

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity);

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId);
}