<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\AssignmentRepository
     */
    protected $assignmentRepository;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService
     */
    protected $feedbackService;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\AssignmentRepository $assignmentRepository
     */
    public function __construct(AssignmentRepository $assignmentRepository, FeedbackService $feedbackService)
    {
        $this->assignmentRepository = $assignmentRepository;
        $this->feedbackService = $feedbackService;
    }

    /**
     * @param Entry $entry
     */
    public function deleteEntry(Entry $entry)
    {
        if (!$this->assignmentRepository->deleteEntry($entry))
        {
            throw new \RuntimeException('Could not delete entry ' . $entry->getId());
        }

        $this->assignmentRepository->deleteScoreForEntry($entry);
        $this->feedbackService->deleteFeedbackForEntry($entry);
        $this->assignmentRepository->deleteAttachmentsForEntry($entry);
    }

    /**
     * @param Score $score
     *
     * @return Score | \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score
     */
    public function createScore(Score $score)
    {
        if(empty($score->getCreated()))
        {
            $score->setCreated(time());
        }

        if (!$this->assignmentRepository->createScore($score))
        {
            throw new \RuntimeException('Could not create a new score for entry ' . $score->getEntryId());
        }

        return $score;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     */
    public function updateScore(Score $score)
    {
        $score->setModified(time());

        if (!$this->assignmentRepository->updateScore($score))
        {
            throw new \RuntimeException('Could not update the score ' . $score->getId());
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @param $entityType
     * @param $entityId
     * @param $userId
     * @param $contentObjectId
     * @param $ipAddress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    protected function createEntryByInstance(Entry $entry, $entityType, $entityId, $userId, $contentObjectId, $ipAddress
    )
    {
        $entry->setContentObjectId($contentObjectId);
        $entry->setSubmitted(time());
        $entry->setEntityId($entityId);
        $entry->setEntityType($entityType);
        $entry->setUserId($userId);
        $entry->setIpAddress($ipAddress);

        if (!$this->assignmentRepository->createEntry($entry))
        {
            throw new \RuntimeException('Could not create a new score for entry ' . $entry->getId());
        }

        return $entry;
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentRepository->retrieveEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->assignmentRepository->retrieveEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentRepository->retrieveScoreByEntry($entry);
    }

    /**
     * @param integer $scoreIdentifier
     *
     * @return Score
     */
    public function findScoreByIdentifier($scoreIdentifier)
    {
        return $this->assignmentRepository->findScoreByIdentifier($scoreIdentifier);
    }

    /**
     * @return Score
     */
    public function initializeScore()
    {
        return $this->createScoreInstance();
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectUsedAsEntry(ContentObject $contentObject)
    {
        return $this->assignmentRepository->countContentObjectsUsedAsEntryByContentObjectIds(
                [$contentObject->getId()]
            ) > 0;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectOwnerSameAsSubmitter(ContentObject $contentObject)
    {
        $entries = $this->assignmentRepository->findEntriesByContentObjectId($contentObject);
        foreach($entries as $entry)
        {
            if($entry->getUserId() == $contentObject->get_owner_id())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        $entryAttachment = $this->createEntryAttachmentInstance();

        $entryAttachment->setEntryId($entry->getId());
        $entryAttachment->setAttachmentId($contentObject->getId());
        $entryAttachment->setCreated(time());

        if (!$this->assignmentRepository->createEntryAttachment($entryAttachment))
        {
            throw new \RuntimeException('Could not attach a content object to entry ' . $entryAttachment->getId());
        }

        return $entryAttachment;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function detachContentObjectFromEntry(Entry $entry, ContentObject $contentObject)
    {
        $entryAttachment =
            $this->assignmentRepository->findEntryAttachmentByEntryAndAttachmentId($entry, $contentObject->getId());

        if (!$entryAttachment instanceof EntryAttachment)
        {
            return;
        }

        $this->deleteEntryAttachment($entryAttachment);

    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment|\Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        if (!$this->assignmentRepository->deleteEntryAttachment($entryAttachment))
        {
            throw new \RuntimeException('Could not detach a content object to entry ' . $entryAttachment->getId());
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function updateEntryAttachment(EntryAttachment $entryAttachment)
    {
        if (!$this->assignmentRepository->updateEntryAttachment($entryAttachment))
        {
            throw new \RuntimeException('Could not update an entry attachment with id ' . $entryAttachment->getId());
        }
    }

    /**
     * @param int $entryAttachmentId
     *
     * @return EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->assignmentRepository->findEntryAttachmentById($entryAttachmentId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->assignmentRepository->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectAttachedToEntry(Entry $entry, ContentObject $contentObject)
    {
        $entryAttachment =
            $this->assignmentRepository->findEntryAttachmentByEntryAndAttachmentId($entry, $contentObject->getId());

        return $entryAttachment instanceof EntryAttachment;
    }

    /**
     * @param int[] $attachmentIds
     *
     * @return bool
     */
    public function countEntryAttachmentsByAttachmentIds($attachmentIds = [])
    {
        return $this->assignmentRepository->countEntryAttachmentsByAttachmentIds($attachmentIds);
    }

    /**
     * @param int $attachmentId
     *
     * @return EntryAttachment[]
     */
    public function findEntryAttachmentsByAttachmentId($attachmentId)
    {
        return $this->assignmentRepository->findEntryAttachmentsByAttachmentId($attachmentId);
    }

    /**
     * @param int $attachmentId
     */
    public function deleteEntryAttachmentsByAttachmentId($attachmentId)
    {
        $entryAttachments = $this->findEntryAttachmentsByAttachmentId($attachmentId);
        foreach($entryAttachments as $entryAttachment)
        {
            $this->deleteEntryAttachment($entryAttachment);
        }
    }

    /**
     * @param int $userId
     *
     * @return EntryAttachment[]
     */
    public function findEntryAttachmentsByUserId($userId)
    {
        return $this->assignmentRepository->findEntryAttachmentsByUserId($userId);
    }


    /**
     * @param int $userId
     *
     * @return int
     */
    public function countEntryAttachmentsByUserId($userId)
    {
        return $this->assignmentRepository->countEntryAttachmentsByUserId($userId);
    }

    /**
     * Creates a new instance for an entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    abstract protected function createEntryInstance();

    /**
     * Creates a new instance for an entry attachment
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    abstract protected function createEntryAttachmentInstance();

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    abstract protected function createScoreInstance();
}