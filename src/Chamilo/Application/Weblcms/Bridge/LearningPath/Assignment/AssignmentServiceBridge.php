<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentServiceBridge implements AssignmentServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var int[]
     */
    protected $targetUserIds;

    /**
     * @var bool
     */
    protected $canEditAssignment;

    /**
     * AssignmentServiceBridge constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     */
    public function __construct(
        Translator $translator, AssignmentService $assignmentService, EntityServiceManager $entityServiceManager
    )
    {
        $this->translator = $translator;
        $this->assignmentService = $assignmentService;
        $this->entityServiceManager = $entityServiceManager;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService $trackingService
     */
    public function setLearningPathTrackingService(TrackingService $trackingService)
    {
        $this->learningPathTrackingService = $trackingService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param int[] $targetUserIds
     */
    public function setTargetUserIds($targetUserIds = [])
    {
        $this->targetUserIds = $targetUserIds;
    }

    /**
     * @param $canEditAssignment
     */
    public function setCanEditAssignment($canEditAssignment)
    {
        $this->canEditAssignment = $canEditAssignment;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType)
    {
        return $this->assignmentService->countDistinctEntriesByTreeNodeDataAndEntityType(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByTreeNodeAndEntityType(TreeNode $treeNode, $entityType)
    {
        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = $treeNode->getContentObject();

        return $this->assignmentService->countDistinctLateEntriesByTreeNodeDataAndEntityType(
            $assignment, $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return mixed
     */
    public function findEntitiesByEntityType(
        TreeNode $treeNode, int $entityType, FilterParameters $filterParameters
    )
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->retrieveEntities(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $filterParameters
        );
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntities(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $filterParameters
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntitiesWithEntries($this->contentObjectPublication, $treeNode->getTreeNodeData());
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntitiesWithEntriesByEntityType(TreeNode $treeNode, $entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->retrieveEntitiesWithEntries(
            $this->contentObjectPublication, $treeNode->getTreeNodeData()
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getPluralEntityName();
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getEntityName();
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters $entityTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, EntityTableParameters $entityTableParameters)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityTableParameters->getEntityType());

        return $entityService->getEntityTable($application, $entityTableParameters);
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableParameters $entryTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     * @throws \Exception
     */
    public function getEntryTableForEntityTypeAndId(Application $application, EntryTableParameters $entryTableParameters
    )
    {
        $entryTableParameters->setEntryClassName(
            \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::class
        );
        $entryTableParameters->setScoreClassName(
            \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score::class
        );

        return new EntryTable($application, $entryTableParameters);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return integer
     */
    public function getCurrentEntityType(TreeNode $treeNode)
    {
        /** @var AssignmentConfiguration $configuration */
        $configuration = $treeNode->getConfiguration(new AssignmentConfiguration());

        return $configuration->getEntityType();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser, TreeNode $treeNode)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->getCurrentEntityIdentifier($this->contentObjectPublication, $currentUser);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser, TreeNode $treeNode)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->getAvailableEntityIdentifiersForUser($this->contentObjectPublication, $currentUser);
    }

    /**
     *
     * @param integer $date
     *
     * @return boolean
     */
    public function isDateAfterAssignmentEndTime($date)
    {
        return false;
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->isUserPartOfEntity($user, $this->contentObjectPublication, $entityId);
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->getUsersForEntity($entityId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return boolean
     */
    public function canEditAssignment(TreeNode $treeNode)
    {
        return $this->canEditAssignment;
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
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function createEntry(
        TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId, $userId, $contentObjectId,
        $ipAdress
    )
    {
        $user = new User();
        $user->setId($userId);

        /** @var \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath */
        $learningPath = $this->contentObjectPublication->getContentObject();

        $this->learningPathTrackingService->setActiveAttemptCompleted($learningPath, $treeNode, $user);

        $entry = $this->assignmentService->createEntry(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $treeNodeAttempt, $entityType, $entityId,
            $userId,
            $contentObjectId, $ipAdress
        );

        $this->assignmentService->createLearningPathAttemptEntryRelation($treeNodeAttempt, $entry);

        if($entityType != \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::ENTITY_TYPE_USER)
        {
            $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);
            $users = $entityService->getUsersForEntity($entityId);

            foreach($users as $user)
            {
                if($user->getId() == $userId)
                {
                    continue;
                }

                $attempt = $this->learningPathTrackingService->getActiveAttempt($learningPath, $treeNode, $user);
                $this->learningPathTrackingService->setActiveAttemptCompleted($learningPath, $treeNode, $user);
                $this->assignmentService->createLearningPathAttemptEntryRelation($attempt, $entry);
            }
        }

        return $entry;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        $this->assignmentService->deleteEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeEntityTypeAndId(
        TreeNode $treeNode, $entityType, $entityId, Condition $condition = null
    )
    {
        return $this->assignmentService->countEntriesForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityId, $condition
        );
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
        return $this->assignmentService->countDistinctScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityId
        );
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
        return $this->assignmentService->getAverageScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getLastScoreForTreeNodeEntityTypeAndId(TreeNode $treeNode, $entityType, $entityId)
    {
        return $this->assignmentService->getLastScoreForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry | \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->assignmentService->findEntriesByIdentifiers($entryIdentifiers);
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->renderEntityName($entity);
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType($treeNode));

        return $entityService->renderEntityNameById($entityId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param Score $score
     *
     * @return Score | \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score
     */
    public function createScore(TreeNode $treeNode, Score $score)
    {
        $score = $this->assignmentService->createScore(
            $score
        );

        $entry = $this->findEntryByIdentifier($score->getEntryId());

        /** @var \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath */
        $learningPath = $this->contentObjectPublication->getContentObject();

        $attempts = $this->assignmentService->findLearningPathAttemptsByEntry($entry);
        foreach($attempts as $attempt)
        {
            $attemptUser = new User();
            $attemptUser->setId($attempt->getUserId());

            $this->learningPathTrackingService->changeAssessmentScore(
                $learningPath, $attemptUser, $treeNode, $attempt->getId(), $score->getScore()
            );
        }

        return $score;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score $score
     *
     * @throws \Exception
     */
    public function updateScore(TreeNode $treeNode, Score $score)
    {
        $this->assignmentService->updateScore($score);

        /** @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry */
        $entry = $this->findEntryByIdentifier($score->getEntryId());
        if (!$entry instanceof Entry)
        {
            throw new \Exception('Could not find the entry for the given score');
        }

        /** @var \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath */
        $learningPath = $this->contentObjectPublication->getContentObject();

        $attempts = $this->assignmentService->findLearningPathAttemptsByEntry($entry);
        foreach($attempts as $attempt)
        {
            $attemptUser = new User();
            $attemptUser->setId($attempt->getUserId());

            $this->learningPathTrackingService->changeAssessmentScore(
                $learningPath, $attemptUser, $treeNode, $attempt->getId(), $score->getScore()
            );
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentService->findScoreByEntry($entry);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return new Feedback();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Score
     */
    public function initializeScore()
    {
        return $this->assignmentService->initializeScore();
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeEntityTypeAndIdentifiers(TreeNode $treeNode, $entityType, $entityIdentifiers)
    {
        return $this->assignmentService->findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityIdentifiers
        );
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
        int $count = null,
        array $orderProperty = []
    )
    {
        return $this->assignmentService->findEntriesForTreeNodeDataEntityTypeAndId(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityId, $condition, $offset,
            $count, $orderProperty
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNode(TreeNode $treeNode)
    {
        return $this->assignmentService->findEntriesByTreeNodeData(
            $this->contentObjectPublication, $treeNode->getTreeNodeData()
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity(TreeNode $treeNode, $entityType, $entityIdentifier)
    {
        return $this->assignmentService->findLastEntryForEntity(
            $this->contentObjectPublication, $treeNode->getTreeNodeData(), $entityType, $entityIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->attachContentObjectToEntry($entry, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\EntryAttachment|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->assignmentService->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->assignmentService->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->assignmentService->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->isContentObjectAttachedToEntry($entry, $contentObject);
    }

    /**
     * @param int $learningPathAttemptId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry
     */
    public function findEntryForLearningPathAttempt(int $learningPathAttemptId)
    {
        return $this->assignmentService->findEntryForLearningPathAttempt($learningPathAttemptId);
    }
}