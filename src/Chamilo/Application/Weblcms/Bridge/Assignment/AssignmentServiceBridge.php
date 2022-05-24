<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry\EntryTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use RuntimeException;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentServiceBridge implements AssignmentServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication
     */
    protected $assignmentPublication;

    /**
     * @var bool
     */
    protected $canEditAssignment;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     */
    public function __construct(
        AssignmentService $assignmentService, EntityServiceManager $entityServiceManager
    )
    {
        $this->assignmentService = $assignmentService;
        $this->entityServiceManager = $entityServiceManager;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication $assignmentPublication
     */
    public function setAssignmentPublication(Publication $assignmentPublication)
    {
        if (!isset($this->contentObjectPublication) ||
            $this->contentObjectPublication->getId() != $assignmentPublication->getPublicationId())
        {
            throw new RuntimeException(
                'The given assignment publication does not belong to the given content object publication'
            );
        }

        $this->assignmentPublication = $assignmentPublication;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected function getAssignment()
    {
        return $this->contentObjectPublication->getContentObject();
    }

    /**
     * @param bool $canEditAssignment
     */
    public function setCanEditAssignment($canEditAssignment = true)
    {
        $this->canEditAssignment = $canEditAssignment;
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByEntityType($entityType)
    {
        return $this->assignmentService->countDistinctEntriesByContentObjectPublicationAndEntityType(
            $this->contentObjectPublication, $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByEntityType($entityType)
    {
        return $this->assignmentService->countDistinctLateEntriesByContentObjectPublicationAndEntityType(
            $this->getAssignment(), $this->contentObjectPublication, $entityType
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return integer
     */
    public function countEntitiesByEntityType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntities($this->contentObjectPublication);
    }

    /**
     * @param int $entityType
     *
     * @return int
     */
    public function countEntitiesWithEntriesByEntityType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntitiesWithEntries($this->contentObjectPublication);
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function findEntitiesWithEntriesByEntityType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->retrieveEntitiesWithEntries($this->contentObjectPublication);
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
     * @return string
     */
    public function getEntityNameByType($entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getEntityName();
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTableForType(Application $application, $entityType)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->getEntityTable($application, $this, $this->contentObjectPublication);
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTableForEntityTypeAndId(Application $application, $entityType, $entityId)
    {
        return new EntryTable(
            $application, $this, $entityId, $entityType, $this->assignmentService, $this->contentObjectPublication
        );
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        return $this->assignmentPublication->getEntityType();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getCurrentEntityIdentifier($this->contentObjectPublication, $currentUser);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getAvailableEntityIdentifiersForUser($this->contentObjectPublication, $currentUser);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityType, $entityId)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->isUserPartOfEntity($user, $this->contentObjectPublication, $entityId);
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
     *
     * @return boolean
     */
    public function canEditAssignment()
    {
        return $this->canEditAssignment;
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAdress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        return $this->assignmentService->createEntry(
            $this->contentObjectPublication, $entityType, $entityId, $userId,
            $contentObjectId, $ipAdress
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        $this->assignmentService->deleteEntry($entry);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countEntriesForEntityTypeAndId($entityType, $entityId)
    {
        return $this->assignmentService->countEntriesForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->assignmentService->countDistinctScoreForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getAverageScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->assignmentService->getAverageScoreForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function getLastScoreForEntityTypeAndId($entityType, $entityId)
    {
        return $this->assignmentService->getLastScoreForContentObjectPublicationEntityTypeAndId(
            $this->contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->assignmentService->findEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntity($entityType, DataClass $entity)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->renderEntityName($entity);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->renderEntityNameById($entityId);
    }

    /**
     * @param Score $score
     *
     * @return Score
     */
    public function createScore(Score $score)
    {
        return $this->assignmentService->createScore(
            $score
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     */
    public function updateScore(Score $score)
    {
        $this->assignmentService->updateScore($score);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentService->findScoreByEntry($entry);
    }

    /**
     * @inheritdoc
     */
    public function initializeScore()
    {
        return $this->assignmentService->initializeScore();
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        return $this->assignmentService->findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
            $this->contentObjectPublication, $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findEntries()
    {
        return $this->assignmentService->findEntriesByContentObjectPublication($this->contentObjectPublication);
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Entry
     */
    public function findLastEntryForEntity($entityType, $entityIdentifier)
    {
        return $this->assignmentService->findLastEntryForEntityByContentObjectPublication(
            $this->contentObjectPublication, $entityType, $entityIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->attachContentObjectToEntry($entry, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        $this->assignmentService->deleteEntryAttachment($entryAttachment);
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->assignmentService->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->assignmentService->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        return $this->assignmentService->isContentObjectAttachedToEntry($entry, $contentObject);
    }
}