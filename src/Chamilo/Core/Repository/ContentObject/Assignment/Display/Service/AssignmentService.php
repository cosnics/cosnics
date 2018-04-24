<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Abstract class to provide common functionality to handle assignment entries, feedback, scores and notes
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository
     */
    protected $assignmentRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository $assignmentRepository
     */
    public function __construct(AssignmentRepository $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
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
        $this->assignmentRepository->deleteFeedbackForEntry($entry);
        $this->assignmentRepository->deleteAttachmentsForEntry($entry);
    }

    /**
     * @param Score $score
     * @return Score
     */
    public function createScore(Score $score)
    {
        if (!$this->assignmentRepository->createScore($score))
        {
            throw new \RuntimeException('Could not create a new score for entry ' . $entry->getId());
        }

        return $score;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     */
    public function updateScore(Score $score)
    {
        if (!$this->assignmentRepository->updateScore($score))
        {
            throw new \RuntimeException('Could not update the score ' . $score->getId());
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function createNote(Entry $entry, User $user, $submittedNote)
    {
        $note = $this->createNoteInstance();

        $note->setNote($submittedNote);
        $note->setEntryId($entry->getId());
        $note->setCreated(time());
        $note->setModified(time());
        $note->setUserId($user->getId());

        if (!$this->assignmentRepository->createNote($note))
        {
            throw new \RuntimeException('Could not create a new note for entry ' . $note->getId());
        }

        return $note;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     */
    public function updateNote(Note $note)
    {
        if (!$this->assignmentRepository->updateNote($note))
        {
            throw new \RuntimeException('Could not update the note ' . $note->getId());
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @param $entityType
     * @param $entityId
     * @param $userId
     * @param $contentObjectId
     * @param $ipAddress
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
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
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->assignmentRepository->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentRepository->retrieveScoreByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        return $this->assignmentRepository->retrieveNoteByEntry($entry);
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->assignmentRepository->retrieveFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        return $this->assignmentRepository->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
    )
    {
        return $this->assignmentRepository->findFeedbackByEntry($entry);
    }

    /**
     * @return Score
     */
    public function initializeScore()
    {
        return $this->createScoreInstance();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->createFeedbackInstance();
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment
     */
    public function attachContentObjectToEntry(Entry $entry, ContentObject $contentObject)
    {
        $entryAttachment = $this->createEntryAttachmentInstance();

        $entryAttachment->setEntryId($entry->getId());
        $entryAttachment->setAttachmentId($contentObject->getId());

        if (!$this->assignmentRepository->createEntryAttachment($entryAttachment))
        {
            throw new \RuntimeException('Could not attach a content object to entry ' . $entryAttachment->getId());
        }

        return $entryAttachment;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment $entryAttachment
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        if (!$this->assignmentRepository->deleteEntryAttachment($entryAttachment))
        {
            throw new \RuntimeException('Could not detach a content object to entry ' . $entryAttachment->getId());
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment $entryAttachment
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        return $this->assignmentRepository->findAttachmentsByEntry($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    abstract protected function createEntryInstance();

    /**
     * Creates a new instance for an entry attachment
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment
     */
    abstract protected function createEntryAttachmentInstance();

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    abstract protected function createScoreInstance();

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    abstract protected function createFeedbackInstance();

    /**
     * Creates a new instance for a score
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    abstract protected function createNoteInstance();
}