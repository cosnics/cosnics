<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\NotificationProcessor;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Notification\Domain\NotificationRedirect;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Domain\ViewingContext;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Queue\Exceptions\JobNoLongerValidException;
use Chamilo\Core\Queue\Service\JobProcessorInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentJobProcessor implements JobProcessorInterface
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
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected $publicationService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    /**
     * EntryNotificationProcessor constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService $treeNodeDataService
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param FilterManager $filterManager
     * @param NotificationManager $notificationManager
     */
    public function __construct(
        AssignmentService $assignmentService, EntityServiceManager $entityServiceManager,
        TreeNodeDataService $treeNodeDataService,
        PublicationService $publicationService, CourseService $courseService,
        UserService $userService, ContentObjectRepository $contentObjectRepository,
        FilterManager $filterManager, NotificationManager $notificationManager
    )
    {
        $this->assignmentService = $assignmentService;
        $this->entityServiceManager = $entityServiceManager;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->publicationService = $publicationService;
        $this->courseService = $courseService;
        $this->userService = $userService;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->filterManager = $filterManager;
        $this->notificationManager = $notificationManager;
    }

    /**
     * @param int $entryId
     * @param int $contentObjectPublicationID
     *
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     */
    protected function processForEntry($entryId, $contentObjectPublicationID)
    {
        $entry = $this->assignmentService->findEntryByIdentifier($entryId);
        if (!$entry instanceof Entry)
        {
            throw new JobNoLongerValidException(
                sprintf('The given entry with id %s could not be found', $entryId)
            );
        }

        $treeNodeData = $this->treeNodeDataService->getTreeNodeDataById($entry->getTreeNodeDataId());
        if(!$treeNodeData instanceof TreeNodeData)
        {
            throw new JobNoLongerValidException(
                sprintf(
                    'The given tree node with id %s could not be found',
                    $entry->getTreeNodeDataId()
                )
            );
        }

        $assignment = $this->contentObjectRepository->findById($treeNodeData->getContentObjectId());
        if (!$assignment instanceof Assignment)
        {
            throw new JobNoLongerValidException(
                sprintf(
                    'The given assignment with id %s could not be found', $treeNodeData->getContentObjectId()
                )
            );
        }

        $publication = $this->publicationService->getPublication($contentObjectPublicationID);
        if (!$publication instanceof ContentObjectPublication)
        {

            throw new JobNoLongerValidException(
                sprintf(
                    'The given content object publication with id %s could not be found',
                    $contentObjectPublicationID
                )
            );
        }

        $course = $this->courseService->getCourseById($publication->get_course_id());
        if (!$course instanceof Course)
        {
            throw new JobNoLongerValidException(
                sprintf(
                    'The given course with id %s could not be found', $publication->get_course_id()
                )
            );
        }

        $learningPath = $this->contentObjectRepository->findById($publication->get_content_object_id());
        if (!$learningPath instanceof LearningPath)
        {
            throw new JobNoLongerValidException(
                sprintf(
                    'The given learning path with id %s could not be found', $publication->get_content_object_id()
                )
            );
        }

        $targetUserIds = $this->getTargetUserIds($course, $entry);
        $filters = $this->getFilters($publication, $course, $assignment, $learningPath, $treeNodeData);
        $url = $this->getNotificationUrl($course, $publication, $treeNodeData, $entry);
        $viewingContexts = $this->getNotificationViewingContexts($publication, $assignment, $learningPath, $treeNodeData, $course, $entry);
        $notificationContexts = $this->getNotificationContexts($publication, $course, $treeNodeData);

        $date = new \DateTime();
        $date->setTimestamp($this->getCreationDate($entry));

        $this->notificationManager->createNotificationForUsers(
            $url, $viewingContexts, $date, $targetUserIds, $filters, $notificationContexts
        );
    }

    /**
     * @param Course $course
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param Entry $entry
     *
     * @return string
     */
    protected function getNotificationUrl(Course $course, ContentObjectPublication $publication, TreeNodeData $treeNodeData, Entry $entry)
    {
        $parameters = [
            Application::PARAM_CONTEXT => 'Chamilo\Application\Weblcms',
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->getId(),
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'LearningPath',
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->getId(),
            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $publication->get_category_id(),
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID => $treeNodeData->getId(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID => $entry->getEntityId(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE => $entry->getEntityType(
            )
        ];

        $redirect = new NotificationRedirect($parameters);
        $url = $redirect->getUrl();

        return $url;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param Course $course
     * @param Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return Filter[]
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getFilters(
        ContentObjectPublication $publication, Course $course, Assignment $assignment,
        LearningPath $learningPath, TreeNodeData $treeNodeData
    )
    {
        $filters = [
            $this->filterManager->getOrCreateFilterByContextPath(
                'Chamilo\\Application\\Weblcms::Course:' . $course->getId(),
                new TranslationContext(
                    'Chamilo\Application\Weblcms', 'NotificationFilterCourse',
                    ['{COURSE_TITLE}' => $course->get_title()]
                )
            ),
            $this->filterManager->getOrCreateFilterByContextPath(
                'Chamilo\\Application\\Weblcms::Tool:' . $publication->get_tool() . '::Course:' . $publication->get_course_id(),
                new TranslationContext(
                    'Chamilo\Application\Weblcms', 'NotificationFilterTool',
                    [
                        '{COURSE_TITLE}' => $course->get_title(),
                        '{TOOL}' => new TranslationContext(
                            'Chamilo\Application\Weblcms\Tool\Implementation\\' . $publication->get_tool(), 'TypeName'
                        )
                    ]
                )
            ),
            $this->filterManager->getOrCreateFilterByContextPath(
                'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId(),
                new TranslationContext(
                    'Chamilo\Application\Weblcms\Tool\Implementation\LearningPath', 'NotificationFilterPublication',
                    [
                        '{COURSE_TITLE}' => $course->get_title(), '{LEARNING_PATH_TITLE}' => $learningPath->get_title()
                    ]

                )
            ),
            $this->filterManager->getOrCreateFilterByContextPath(
                'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() . '::TreeNodeData:' . $treeNodeData->getId(),
                new TranslationContext(
                    'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment', 'NotificationFilterLearningPathAssignment',
                    [
                        '{COURSE_TITLE}' => $course->get_title(), '{LEARNING_PATH_TITLE}' => $learningPath->get_title(),
                        '{ASSIGNMENT_TITLE}' => $assignment->get_title()
                    ]

                )
            )
        ];

        return $filters;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return string[]
     */
    protected function getNotificationContexts(ContentObjectPublication $publication, Course $course, TreeNodeData $treeNodeData)
    {
        return [
            'Chamilo',
            'Assignment',
            'Chamilo\\Application\\Weblcms::Course:' . $course->getId(),
            'Chamilo\\Application\\Weblcms::Tool:' . $publication->get_tool() . '::Course:' . $course->getId(),
            'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId(),
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() . '::TreeNodeData:' . $treeNodeData->getId()
        ];
    }

    /**
     * @param ContentObjectPublication $publication
     * @param Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param Course $course
     * @param Entry $entry
     *
     * @return ViewingContext[]
     */
    protected function getNotificationViewingContexts(
        ContentObjectPublication $publication, Assignment $assignment,
        LearningPath $learningPath, TreeNodeData $treeNodeData, Course $course, Entry $entry
    )
    {
        $translations = $this->getNotificationViewingContextVariables($course, $publication, $treeNodeData);

        $viewingContexts = [];

        $key = 'Chamilo';
        $viewingContexts[] = new ViewingContext(
            $key,
            new TranslationContext(
                'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment', $translations[$key],
                [
                    '{LEARNING_PATH_TITLE}' => $learningPath->get_title(),
                    '{ASSIGNMENT_TITLE}' => $assignment->get_title(), '{COURSE_TITLE}' => $course->get_title(),
                    '{USER}' => $this->userService->getUserFullNameById($this->getUserId($entry))
                ]
            )
        );

        $key = 'Chamilo\\Application\\Weblcms::Course:' . $course->getId();
        $viewingContexts[] = new ViewingContext(
            $key,
            new TranslationContext(
                'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment', $translations[$key],
                [
                    '{LEARNING_PATH_TITLE}' => $learningPath->get_title(),
                    '{ASSIGNMENT_TITLE}' => $assignment->get_title(), '{COURSE_TITLE}' => $course->get_title(),
                    '{USER}' => $this->userService->getUserFullNameById($this->getUserId($entry))
                ]
            )
        );

        $key = 'Chamilo\\Application\\Weblcms::ContentObjectPublication:' . $publication->getId();
        $viewingContexts[] = new ViewingContext(
            $key,
            new TranslationContext(
                'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment', $translations[$key],
                [
                    '{LEARNING_PATH_TITLE}' => $learningPath->get_title(),
                    '{ASSIGNMENT_TITLE}' => $assignment->get_title(), '{COURSE_TITLE}' => $course->get_title(),
                    '{USER}' => $this->userService->getUserFullNameById($this->getUserId($entry))
                ]
            )
        );

        $key = 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\LearningPath:' . $publication->getId() . '::TreeNodeData:' . $treeNodeData->getId();
        $viewingContexts[] = new ViewingContext(
            $key,
            new TranslationContext(
                'Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment', $translations[$key],
                [
                    '{LEARNING_PATH_TITLE}' => $assignment->get_title(),
                    '{ASSIGNMENT_TITLE}' => $assignment->get_title(), '{COURSE_TITLE}' => $course->get_title(),
                    '{USER}' => $this->userService->getUserFullNameById($this->getUserId($entry))
                ]
            )
        );

        return $viewingContexts;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $publication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return array
     */
    abstract protected function getNotificationViewingContextVariables(Course $course, ContentObjectPublication $publication, TreeNodeData $treeNodeData);

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    abstract protected function getCreationDate(Entry $entry);

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return int
     */
    abstract protected function getUserId(Entry $entry);

    /**
     * @param Course $course
     * @param Entry $entry
     *
     * @return int[]
     */
    protected function getTargetUserIds($course, $entry)
    {
        $courseTeachers = $this->courseService->getTeachersFromCourse($course);
        $entityUsers = $this->entityServiceManager->getEntityServiceByType($entry->getEntityType())->getUsersForEntity(
            $entry->getEntityId()
        );

        $targetUserIds = [];
        $targetUserIds[] = $entry->getUserId();

        foreach ($courseTeachers as $courseTeacher)
        {
            $targetUserIds[] = $courseTeacher->getId();
        }

        foreach ($entityUsers as $entityUser)
        {
            if($entityUser instanceof User)
            {
                $targetUserIds[] = $entityUser->getId();
            }
            else
            {
                $targetUserIds[] = $entityUser;
            }
        }

        $targetUserIds = $this->filterTargetUsers($entry, $targetUserIds);

        return array_unique($targetUserIds);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     * @param array $targetUserIds
     *
     * @return array
     */
    protected function filterTargetUsers(Entry $entry, $targetUserIds = [])
    {
        foreach($targetUserIds as $key => $targetUserId)
        {
            if($targetUserId == $this->getUserId($entry))
            {
                unset($targetUserIds[$key]);
            }
        }

        return $targetUserIds;
    }
}