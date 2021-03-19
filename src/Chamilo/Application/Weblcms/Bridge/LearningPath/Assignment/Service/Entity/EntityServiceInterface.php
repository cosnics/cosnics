<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface EntityServiceInterface
 *
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface EntityServiceInterface
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntities(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return int
     */
    public function countEntitiesWithEntries(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | DataClass[]
     */
    public function retrieveEntitiesWithEntries(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        FilterParameters $filterParameters
    );

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        FilterParameters $filterParameters
    );

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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters $entityTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTable(
        Application $application, EntityTableParameters $entityTableParameters
    );

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    public function getEntryPlagiarismResultTable(
        Application $application,
        EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
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
    public function getAvailableEntityIdentifiersForUser(
        ContentObjectPublication $contentObjectPublication, User $currentUser
    );

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
     * @param string[] $entityArray
     *
     * @return string
     */
    public function renderEntityNameByArray($entityArray = []);

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId);
}
