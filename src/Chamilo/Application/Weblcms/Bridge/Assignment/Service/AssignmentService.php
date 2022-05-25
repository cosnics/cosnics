<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor\EntryNotificationJobProcessor;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryAttachment;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\AssignmentRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Queue\Service\JobProducer;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentService
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService
{
    /**
     *
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentRepository
     */
    protected $assignmentRepository;

    /**
     * @var \Chamilo\Core\Queue\Service\JobProducer
     */
    protected $jobProducer;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\AssignmentRepository $assignmentRepository
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService $feedbackService
     * @param \Chamilo\Core\Queue\Service\JobProducer $jobProducer
     */
    public function __construct(
        AssignmentRepository $assignmentRepository, FeedbackService $feedbackService, JobProducer $jobProducer
    )
    {
        parent::__construct($assignmentRepository, $feedbackService);
        $this->jobProducer = $jobProducer;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByContentObjectPublicationAndEntityType(
        ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->assignmentRepository->countDistinctEntriesByContentObjectPublicationAndEntityType(
            $contentObjectPublication, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByContentObjectPublicationAndEntityType(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->assignmentRepository->countDistinctLateEntriesByContentObjectPublicationAndEntityType(
            $assignment, $contentObjectPublication, $entityType
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countDistinctScoreForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return int
     */
    public function countEntriesByContentObjectPublicationAndEntityType(
        ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->assignmentRepository->countEntriesByContentObjectPublicationAndEntityType(
            $contentObjectPublication, $entityType
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @param int $createdDate
     *
     * @return mixed
     */
    public function countEntriesByContentObjectPublicationWithCreatedDateLargerThan(
        ContentObjectPublication $contentObjectPublication, $entityType, $createdDate
    )
    {
        return $this->assignmentRepository->countEntriesByContentObjectPublicationWithCreatedDateLargerThan(
            $contentObjectPublication, $entityType, $createdDate
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntriesForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId, Condition $condition = null
    )
    {
        return $this->assignmentRepository->countEntriesForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $entityType, $entityId, $condition
        );
    }

    /**
     *
     * @param integer $contentObjectPublicationIdentifier
     *
     * @return integer
     */
    public function countEntriesForContentObjectPublicationIdentifier($contentObjectPublicationIdentifier)
    {
        return $this->assignmentRepository->countEntriesForContentObjectPublicationIdentifier(
            $contentObjectPublicationIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countLateEntriesByContentObjectPublicationEntityTypeAndId(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->countLateEntriesByContentObjectPublicationEntityTypeAndId(
            $assignment, $contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $courseGroupIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetCourseGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $courseGroupIds = [], $condition = null
    )
    {
        return $this->findTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $courseGroupIds, $condition
        )->count();
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $courseGroupIds
     *
     * @return int
     */
    public function countTargetCourseGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $courseGroupIds = []
    )
    {
        return $this->findTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $courseGroupIds
        )->count();
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $platformGroupIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetPlatformGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $platformGroupIds = [], $condition = null
    )
    {
        return $this->findTargetPlatformGroupsForContentObjectPublication(
            $contentObjectPublication, $platformGroupIds, $condition
        )->count();
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $platformGroupIds
     *
     * @return int
     */
    public function countTargetPlatformGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $platformGroupIds = []
    )
    {
        return $this->findTargetPlatformGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $platformGroupIds
        )->count();
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $userIds = [], $condition = null
    )
    {
        return $this->findTargetUsersForContentObjectPublication($contentObjectPublication, $userIds, $condition)
            ->count();
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     *
     * @return int
     */
    public function countTargetUsersWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $userIds = []
    )
    {
        return $this->findTargetUsersWithEntriesForContentObjectPublication(
            $contentObjectPublication, $userIds
        )->count();
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     * @param integer $userId
     * @param integer $contentObjectId
     * @param string $ipAddress
     *
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntry(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId, $userId, $contentObjectId,
        $ipAddress
    )
    {
        $entry = $this->createEntryInstance();

        $entry->setContentObjectPublicationId($contentObjectPublication->getId());

        $entry = $this->createEntryByInstance($entry, $entityType, $entityId, $userId, $contentObjectId, $ipAddress);
        if ($entry instanceof Entry)
        {
            $job = new Job();
            $job->setProcessorClass(EntryNotificationJobProcessor::class)->setParameter(
                EntryNotificationJobProcessor::PARAM_ENTRY_ID, $entry->getId()
            );

            $this->jobProducer->produceJob($job, 'notifications');
        }

        return $entry;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryAttachment|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment
     */
    protected function createEntryAttachmentInstance()
    {
        return new EntryAttachment();
    }

    /**
     * Creates a new instance for an entry
     *
     * @return Entry
     */
    protected function createEntryInstance()
    {
        return new Entry();
    }

    /**
     * Creates a new instance for a score
     *
     * @return Score
     */
    protected function createScoreInstance()
    {
        return new Score();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function deleteEntriesForContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $entries = $this->findEntriesByContentObjectPublication($contentObjectPublication);
        foreach ($entries as $entry)
        {
            $this->deleteEntry($entry);
        }
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | Entry[]
     */
    public function findEntriesByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType = null
    )
    {
        return $this->assignmentRepository->findEntriesByContentObjectPublication(
            $contentObjectPublication, $entityType
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityIdentifiers
    )
    {
        return $this->assignmentRepository->findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
            $contentObjectPublication, $entityType, $entityIdentifiers
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntriesForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId, $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        return $this->assignmentRepository->retrieveEntriesForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $entityType, $entityId, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param int[] $contentObjectPublicationIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntryStatisticsByContentObjectPublicationIdentifiers($contentObjectPublicationIdentifiers = [])
    {
        return $this->assignmentRepository->findEntryStatisticsByContentObjectPublicationIdentifiers(
            $contentObjectPublicationIdentifiers
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param $entityType
     * @param $entityId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntryStatisticsForEntityByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->findEntryStatisticsForEntityByContentObjectPublication(
            $contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry|\Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLastEntryForEntityByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityIdentifier
    )
    {
        return $this->assignmentRepository->findLastEntryForEntityByContentObjectPublication(
            $contentObjectPublication, $entityType, $entityIdentifier
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $courseGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetCourseGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $courseGroupIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        return $this->assignmentRepository->findTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $courseGroupIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $courseGroupIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetCourseGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $courseGroupIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        if (is_null($orderProperty))
        {
            $orderProperty = new OrderBy();
        }

        $orderProperty->add(
            new OrderProperty(new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME))
        );

        return $this->assignmentRepository->findTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $courseGroupIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $platformGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetPlatformGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $platformGroupIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        return $this->assignmentRepository->findTargetPlatformGroupsForContentObjectPublication(
            $contentObjectPublication, $platformGroupIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $platformGroupIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetPlatformGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $platformGroupIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        if (is_null($orderProperty))
        {
            $orderProperty = new OrderBy();
        }

        $orderProperty->add(new OrderProperty(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME)));

        return $this->assignmentRepository->findTargetPlatformGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $platformGroupIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetUsersForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $userIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        return $this->assignmentRepository->findTargetUsersForContentObjectPublication(
            $contentObjectPublication, $userIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTargetUsersWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $userIds = [], $condition = null, $offset = null,
        $count = null, $orderProperty = null
    )
    {
        if (is_null($orderProperty))
        {
            $orderProperty = new OrderBy();
        }

        $orderProperty->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)));
        $orderProperty->add(new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)));

        return $this->assignmentRepository->findTargetUsersWithEntriesForContentObjectPublication(
            $contentObjectPublication, $userIds, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function getAverageScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->retrieveAverageScoreForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $entityType, $entityId
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function getLastScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->assignmentRepository->retrieveLastScoreForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $entityType, $entityId
        );
    }
}