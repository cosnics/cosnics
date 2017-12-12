<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\User\EntityTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\User\EntryTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Symfony\Component\Translation\Translator;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntityRenderer\UserEntityRenderer;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentDataProvider
    implements \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var ContentObjectPublication
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
     * AssignmentDataProvider constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     */
    public function __construct(Translator $translator, AssignmentService $assignmentService)
    {
        $this->translator = $translator;
        $this->assignmentService = $assignmentService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected function getAssignment()
    {
        return $this->contentObjectPublication->getContentObject();
    }

    /**
     * @param int[] $targetUserIds
     */
    public function setTargetUserIds($targetUserIds = [])
    {
        $this->targetUserIds = $targetUserIds;
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
    public function countDistinctFeedbackByEntityType($entityType)
    {
        return $this->assignmentService->countDistinctFeedbackByContentObjectPublicationAndEntityType(
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
        return $this->assignmentService->countTargetUsersForContentObjectPublication(
            $this->contentObjectPublication, $this->targetUserIds
        );
    }

    /**
     *
     * @param integer $entityType
     *
     * @return string
     */
    public function getEntityNameByType($entityType)
    {
        return $this->translator->trans(
            'Users', [],
            'Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath'
        );
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
        return new EntityTable(
            $application, $this, $this->assignmentService, $this->contentObjectPublication,
            $this->targetUserIds
        );
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
            $application, $this, $entityId, $this->assignmentService, $this->contentObjectPublication
        );
    }

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType()
    {
        return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER;
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
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function countFeedbackByEntityTypeAndEntityId($entityType, $entityId)
    {
        return $this->assignmentService->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
            $this->contentObjectPublication, $entityType, $entityId
        );
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function createEntry($entityType, $entityId, $userId, $contentObjectId, $ipAdress)
    {
        return $this->assignmentService->createEntry(
            $this->contentObjectPublication, $entityType, $entityId, $userId,
            $contentObjectId, $ipAdress
        );
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
    public function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId)
    {
        return $this->assignmentService->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
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
        return $this->assignmentService->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
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
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        return $this->assignmentService->countFeedbackByEntryIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    public function findEntryByIdentifier($entryIdentifier)
    {
        return $this->assignmentService->findEntryByIdentifier($entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByIdentifiers($entryIdentifiers)
    {
        return $this->assignmentService->findEntriesByIdentifiers($entryIdentifiers);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer
     */
    public function getEntityRendererForEntityTypeAndId($entityType, $entityId)
    {
        return new UserEntityRenderer($this, $entityId);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedScore
     *
     * @return boolean
     */
    public function createScore(Entry $entry, User $user, $submittedScore)
    {
        try
        {
            $this->assignmentService->createScore(
                $entry, $user, $submittedScore
            );
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     *
     * @return boolean
     */
    public function updateScore(Score $score)
    {
        try
        {
            $this->assignmentService->updateScore($score);
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submittedNote
     *
     * @return boolean
     */
    public function createNote(Entry $entry, User $user, $submittedNote)
    {
        try
        {
            $this->assignmentService->createNote(
                $entry, $user, $submittedNote
            );
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     *
     * @return boolean
     */
    public function updateNote(Note $note)
    {
        try
        {
            $this->assignmentService->updateNote($note);
        }
        catch (\Exception $ex)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    public function findScoreByEntry(Entry $entry)
    {
        return $this->assignmentService->findScoreByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function findNoteByEntry(Entry $entry)
    {
        return $this->assignmentService->findNoteByEntry($entry);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function initializeFeedback()
    {
        return $this->assignmentService->initializeFeedback();
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function findFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->assignmentService->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        return $this->assignmentService->countFeedbackByEntry($entry);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        return $this->assignmentService->findFeedbackByEntry($entry);
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        return $this->assignmentService->findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
            $this->contentObjectPublication, $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntries()
    {
        return $this->assignmentService->findEntriesByContentObjectPublication($this->contentObjectPublication);
    }
}