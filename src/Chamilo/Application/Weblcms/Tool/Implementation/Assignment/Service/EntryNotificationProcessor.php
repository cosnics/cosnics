<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Notification\Domain\NotificationTriggerData;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Notification\Service\NotificationProcessor\NotificationProcessorInterface;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationProcessor implements NotificationProcessorInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

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
     * @var \Chamilo\Core\Notification\Service\NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Domain\NotificationTriggerData | NotificationTriggerData $notificationTriggerData
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processNotificationTrigger(NotificationTriggerData $notificationTriggerData)
    {
        $entry = $this->assignmentService->findEntryByIdentifier($notificationTriggerData->getEntryId());
        if (!$entry instanceof Entry)
        {
            throw new \InvalidArgumentException(
                sprintf('The given entry with id %s could not be found', $notificationTriggerData->getEntryId())
            );
        }

        $publication = $this->publicationService->getPublication($entry->getContentObjectPublicationId());
        if (!$publication instanceof ContentObjectPublication)
        {

            throw new \InvalidArgumentException(
                sprintf(
                    'The given content object publication with id %s could not be found',
                    $entry->getContentObjectPublicationId()
                )
            );
        }

        $course = $this->courseService->getCourseById($publication->get_course_id());
        if (!$course instanceof Course)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given course with id %s could not be found', $publication->get_course_id()
                )
            );
        }

        $assignment = $this->contentObjectRepository->findById($publication->get_content_object_id());
        if (!$assignment instanceof Assignment)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given assignment with id %s could not be found', $publication->get_content_object_id()
                )
            );
        }

        $targetUserIds = $this->getTargetUserIds($course, $entry);
        $filters = $this->getFilters($publication, $course, $assignment);
        $url = $this->getNotificationUrl($course, $publication, $entry);
        $descriptionContext = $this->getNotificationTranslationContext($assignment, $course, $entry);
        $date = new \DateTime($entry->getSubmitted());

        $this->notificationManager->createNotificationForUsers(
            $url, $descriptionContext, $date, $targetUserIds, $filters
        );
    }

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
            $targetUserIds[] = $entityUser->getId();
        }

        return $targetUserIds;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param Course $course
     * @param Assignment $assignment
     *
     * @return Filter[]
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getFilters($publication, $course, $assignment): array
    {
        foreach ($this->translator->getFallbackLocales() as $locale)
        {
            $translation = $this->translator->trans(
                'NotificationFilterCourse',
                [
                    'COURSE_TITLE' => $course->get_title(),
                    'TOOL' => $this->translator->trans(
                        'TypeName', [],
                        'Chamilo\Application\Weblcms\Tool\\' . $publication->get_tool()
                    )
                ],
                'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
            );
        }

        $filters = [
            $this->filterManager->getOrCreateFilterByPath(
                'course:' . $publication->get_course_id(),
                    new TranslationContext(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 'NotificationFilterCourse',
                        ['COURSE_TITLE' => $course->get_title()]
                    )
            ),
            $this->filterManager->getOrCreateFilterByPath(
                'tool:' . $publication->get_tool() . '-course:' . $publication->get_course_id(),
                    new TranslationContext(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 'NotificationFilterTool',
                        [
                            'COURSE_TITLE' => $course->get_title(),
                            'TOOL' => new TranslationContext(
                                'Chamilo\Application\Weblcms\Tool\\' . $publication->get_tool(), 'TypeName'
                            )
                        ]
                    )
            ),
            $this->filterManager->getOrCreateFilterByPath(
                'publication:' . $publication->getId(),
                    new TranslationContext(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 'NotificationFilterPublication',
                        [
                            'COURSE_TITLE' => $course->get_title(), 'PUBLICATION_TITLE' => $assignment->get_title(),
                            'TOOL' => new TranslationContext(
                                'Chamilo\Application\Weblcms\Tool\\' . $publication->get_tool(), 'TypeName'
                            )
                        ]

                    )
            )
        ];

        return $filters;
    }

    /**
     * @param Course $course
     * @param ContentObjectPublication $publication
     * @param Entry $entry
     *
     * @return string
     */
    protected function getNotificationUrl($course, $publication, $entry): string
    {
        $parameters = [
            Application::PARAM_CONTEXT => 'Chamilo\Application\Weblcms',
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->getId(),
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Assignment',
            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id(),
            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $publication->get_category_id(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID => $entry->getEntityId(),
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE => $entry->getEntityType(
            )
        ];

        $redirect = new Redirect($parameters);
        $url = $redirect->getUrl();

        return $url;
    }

    /**
     * @param Assignment $assignment
     * @param Course $course
     * @param Entry $entry
     *
     * @return TranslationContext
     */
    protected function getNotificationTranslationContext($assignment, $course, $entry): string
    {
        return new TranslationContext(
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 'NewAssignmentEntry',
            [
                'PUBLICATION_TITLE' => $assignment->get_title(), 'COURSE_TITLE' => $course->get_title(),
                'USER' => $this->userService->getUserFullNameById($entry->getUserId())
            ]

        );
    }
}