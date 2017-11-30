<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Ehb\Application\Weblcms\Tool\Implementation\Assignment\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AssignmentService
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository
     */
    protected $assignmentRepository;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository $assignmentRepository
     */
    public function __construct(LearningPathAssignmentRepository $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    /**
     *
     * @param integer $treeNodeAttemptIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeAttemptIdentifier($treeNodeAttemptIdentifier)
    {
        return $this->assignmentRepository->countEntriesForTreeNodeAttemptIdentifier($treeNodeAttemptIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeAttemptAndEntityType(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        return $this->assignmentRepository->countDistinctEntriesByTreeNodeAttemptAndEntityType(
            $treeNodeAttempt,
            $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeAttemptAndEntityType(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        return $this->assignmentRepository->countDistinctFeedbackByTreeNodeAttemptAndEntityType(
            $treeNodeAttempt,
            $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctLateEntriesByTreeNodeAttemptAndEntityType(
        TreeNodeAttempt $treeNodeAttempt,
        $entityType
    )
    {
        return $this->assignmentRepository->countDistinctLateEntriesByTreeNodeAttemptAndEntityType(
            $treeNodeAttempt,
            $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     *
     * @return integer
     */
    public function countEntitiesByTreeNodeAttemptAndEntityType(TreeNodeAttempt $treeNodeAttempt, $entityType)
    {
        switch ($entityType)
        {
            case Entry::ENTITY_TYPE_USER :
                return $this->countTargetUsersForTreeNodeAttempt($treeNodeAttempt);
                break;

            case Entry::ENTITY_TYPE_COURSE_GROUP :
                return $this->countTargetCourseGroupsForTreeNodeAttempt($treeNodeAttempt);
                break;
            case Entry::ENTITY_TYPE_PLATFORM_GROUP :
                return $this->countTargetGroupsForTreeNodeAttempt($treeNodeAttempt);
                break;
        }
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset, $count,
        $orderProperty
    )
    {
        return $this->assignmentRepository->findTargetUsersForTreeNodeAttempt(
            $treeNodeAttempt,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetUsersForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition = null)
    {
        return $this->assignmentRepository->countTargetUsersForTreeNodeAttempt($treeNodeAttempt, $condition);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeAttemptByEntityTypeAndEntityId(
        TreeNodeAttempt $treeNodeAttempt,
        $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countFeedbackForTreeNodeAttemptByEntityTypeAndEntityId(
            $treeNodeAttempt,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetCourseGroupsForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset,
        $count, $orderProperty
    )
    {
        return $this->assignmentRepository->findTargetCourseGroupsForTreeNodeAttempt(
            $treeNodeAttempt,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetCourseGroupsForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition = null)
    {
        return $this->assignmentRepository->countTargetCourseGroupsForTreeNodeAttempt($treeNodeAttempt, $condition);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetGroupsForTreeNodeAttempt(
        TreeNodeAttempt $treeNodeAttempt, $condition, $offset, $count,
        $orderProperty
    )
    {
        return $this->assignmentRepository->findTargetGroupsForTreeNodeAttempt(
            $treeNodeAttempt,
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param Condition $condition
     *
     * @return integer
     */
    public function countTargetGroupsForTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt, $condition = null)
    {
        return $this->assignmentRepository->countTargetGroupsForTreeNodeAttempt($treeNodeAttempt, $condition);
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countEntriesForTreeNodeAttemptEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId
    )
    {
        return $this->assignmentRepository->countEntriesForTreeNodeAttemptEntityTypeAndId(
            $treeNodeAttempt,
            $entityType,
            $entityId,
            null
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId
    )
    {
        return $this->assignmentRepository->countDistinctFeedbackForEntityTypeAndId(
            $treeNodeAttempt,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId(TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId)
    {
        return $this->assignmentRepository->countDistinctScoreForEntityTypeAndId(
            $treeNodeAttempt,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return string[]
     */
    public function getAverageScoreForEntityTypeAndId(TreeNodeAttempt $treeNodeAttempt, $entityType, $entityId)
    {
        return $this->assignmentRepository->retrieveAverageScoreForEntityTypeAndId(
            $treeNodeAttempt,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesForTreeNodeAttemptEntityTypeAndId(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityId, $condition, $offset, $count, $orderProperty
    )
    {
        return $this->assignmentRepository->retrieveEntriesForTreeNodeAttemptEntityTypeAndId(
            $treeNodeAttempt,
            $entityType,
            $entityId,
            $condition,
            $offset,
            $count,
            $orderProperty
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
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeAttemptEntityTypeAndIdentifiers(
        TreeNodeAttempt $treeNodeAttempt, $entityType,
        $entityIdentifiers
    )
    {
        return $this->assignmentRepository->findEntriesByTreeNodeAttemptEntityTypeAndIdentifiers(
            $treeNodeAttempt,
            $entityType,
            $entityIdentifiers
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        return $this->assignmentRepository->findEntriesByTreeNodeAttempt($treeNodeAttempt);
    }
}