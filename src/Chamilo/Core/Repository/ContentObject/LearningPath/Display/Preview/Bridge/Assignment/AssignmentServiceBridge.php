<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Bridge\Assignment;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssignmentServiceBridge
    implements \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface
{
    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType)
    {
        return 1;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType)
    {
        return 0;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeEntityTypeAndId(
        TreeNode $treeNode, $entityType, $entityId, Condition $condition
    )
    {
        // TODO: Implement countEntriesForTreeNodeEntityTypeAndId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId)
    {
        // TODO: Implement countDistinctScoreForTreeNodeEntityTypeAndId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getAverageScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId)
    {
        // TODO: Implement getAverageScoreForTreeNodeEntityTypeAndId() method.
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getLastScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId)
    {
        // TODO: Implement getLastScoreForTreeNodeEntityTypeAndId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeEntityTypeAndIdentifiers(TreeNode $treeNode, $entityType, $entityIdentifiers)
    {
        // TODO: Implement findEntriesByTreeNodeEntityTypeAndIdentifiers() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeEntityTypeAndId(
        TreeNode $treeNode, int $entityType, int $entityId, Condition $condition = null, int $offset = null,
        int $count = null, array $orderProperty = []
    )
    {
        // TODO: Implement findEntriesByTreeNodeEntityTypeAndId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNode(TreeNode $treeNode)
    {
        // TODO: Implement findEntriesByTreeNode() method.
    }

    /**
     * @param int $learningPathAttemptId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry
     */
    public function findEntryForLearningPathAttempt(int $learningPathAttemptId)
    {
        // TODO: Implement findEntryForLearningPathAttempt() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return mixed
     */
    public function findEntitiesByEntityType(TreeNode $treeNode, int $entityType, FilterParameters $filterParameters)
    {
        $users = array();

        for ($i = 1; $i <= 10; $i ++)
        {
            $user = array();
            $user['name'] = 'Preview User ' . $i;
            // $user = new User();
            // $user->set_lastname('User');
            // $user->set_firstname('Test ' . $i);
            // $user->set_email('test.' . $i . '@user.com');
            // $user->set_username('test.' . $i . '@user.com');

            $users[] = $user;
        }

        return new ArrayResultSet($users);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countEntitiesByEntityType(TreeNode $treeNode, $entityType, FilterParameters $filterParameters)
    {
        return 10;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType)
    {
        return 0;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator|array
     */
    public function findEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType)
    {
        return [];
    }

    /**
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType)
    {
        return Translation::get('Users');
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return Translation::get('User');
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters $entityTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, EntityTableParameters $entityTableParameters)
    {
        $entityTableParameters->setEntityHasMultipleMembers(false);
        $entityTableParameters->setEntityClass(User::class);
        $entityTableParameters->setEntityProperties(['name']);

        return new EntityTable($application, $entityTableParameters);
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters $entryTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, EntryTableParameters $entryTableParameters
    )
    {
        // TODO: Implement getEntryTableForEntityTypeAndId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return integer
     */
    public function getCurrentEntityType(TreeNode $treeNode)
    {
        return 0;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser, TreeNode $treeNode)
    {
        return $currentUser->getId();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser, TreeNode $treeNode)
    {
        return [$currentUser->getId()];
    }

    /**
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        // TODO: Implement isDateAfterAssignmentEndTime() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(TreeNode $treeNode, User $user, $entityType, $entityId)
    {
        return false;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(TreeNode $treeNode, int $entityType, int $entityId)
    {
        // TODO: Implement getUsersForEntity() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return boolean
     */
    public function canEditAssignment(TreeNode $treeNode)
    {
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function createEntry(
        TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId,
        $ipAdress
    )
    {
        // TODO: Implement createEntry() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO: Implement deleteEntry() method.
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        // TODO: Implement findEntryByIdentifier() method.
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        // TODO: Implement findEntriesByIdentifiers() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity(TreeNode $treeNode, $entityType, DataClass $entity)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId(TreeNode $treeNode, $entityType, $entityId)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score
     */
    public function createScore(
        TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
    )
    {
        // TODO: Implement createScore() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     */
    public function updateScore(
        TreeNode $treeNode,
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
    )
    {
        // TODO: Implement updateScore() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
    )
    {
        // TODO: Implement findScoreByEntry() method.
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score
     */
    public function initializeScore()
    {
        // TODO: Implement initializeScore() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry
     */
    public function findLastEntryForEntity(TreeNode $treeNode, $entityType, $entityIdentifier)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry,
        ContentObject $contentObject
    )
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
    )
    {
        // TODO: Implement deleteEntryAttachment() method.
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment|array
     */
    public function findAttachmentsByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
    )
    {
        return [];
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry,
        ContentObject $contentObject
    )
    {
        return false;
    }
}
