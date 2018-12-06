<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Test\Unit\Service\NotificationProcessor;

use Chamilo\Application\Portfolio\Storage\DataClass\Notification;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\UserEntityService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\NotificationProcessor\EntryNotificationJobProcessor;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Service\FilterManager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Chamilo\Core\Queue\Storage\Entity\Job;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Faker\Provider\DateTime;

/**
 * Tests the EntryNotificationJobProcessor
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNotificationJobProcessorTest extends ChamiloTestCase
{
    /**
     * @var EntryNotificationJobProcessor
     */
    protected $entryNotificationJobProcessor;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $assignmentServiceMock;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $entityServiceManagerMock;

    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $publicationServiceMock;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $courseServiceMock;

    /**
     * @var \Chamilo\Core\User\Service\UserService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userServiceMock;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $contentObjectRepositoryMock;

    /**
     * @var FilterManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filterManagerMock;

    /**
     * @var NotificationManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $notificationManagerMock;

    /**
     * @var UserEntityService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userEntityServiceMock;

    /**
     * Setup before each test
     */
    public function setUp()
    {
        $this->assignmentServiceMock = $this->getMockBuilder(AssignmentService::class)
            ->disableOriginalConstructor()->getMock();

        $this->entityServiceManagerMock = $this->getMockBuilder(EntityServiceManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->publicationServiceMock = $this->getMockBuilder(PublicationService::class)
            ->disableOriginalConstructor()->getMock();

        $this->courseServiceMock = $this->getMockBuilder(CourseService::class)
            ->disableOriginalConstructor()->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()->getMock();

        $this->contentObjectRepositoryMock = $this->getMockBuilder(ContentObjectRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->filterManagerMock = $this->getMockBuilder(FilterManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->notificationManagerMock = $this->getMockBuilder(NotificationManager::class)
            ->disableOriginalConstructor()->getMock();

        $this->userEntityServiceMock = $this->getMockBuilder(UserEntityService::class)
            ->disableOriginalConstructor()->getMock();

        $this->entryNotificationJobProcessor = new EntryNotificationJobProcessor(
            $this->assignmentServiceMock, $this->entityServiceManagerMock, $this->publicationServiceMock,
            $this->courseServiceMock, $this->userServiceMock, $this->contentObjectRepositoryMock,
            $this->filterManagerMock, $this->notificationManagerMock
        );
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        unset($this->assignmentServiceMock);
        unset($this->entityServiceManagerMock);
        unset($this->publicationServiceMock);
        unset($this->courseServiceMock);
        unset($this->userServiceMock);
        unset($this->contentObjectRepositoryMock);
        unset($this->filterManagerMock);
        unset($this->notificationManagerMock);
        unset($this->entryNotificationJobProcessor);
    }

    public function testProcess()
    {
        $entryDate = time();

        $job = new Job();
        $job->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, 5);

        $entry = new Entry();
        $entry->setId(5);
        $entry->setContentObjectPublicationId(10);
        $entry->setEntityId(20);
        $entry->setEntityType(1);
        $entry->setUserId(20);
        $entry->setSubmitted($entryDate);

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(10);
        $contentObjectPublication->set_course_id(8);
        $contentObjectPublication->set_content_object_id(14);

        $course = new Course();
        $course->setId(8);

        $assignment = new Assignment();
        $assignment->setId(14);

        $teacher1 = new User();
        $teacher1->setId(19);

        $teacher2 = new User();
        $teacher2->setId(17);

        $entryUser = new User();
        $entryUser->setId(20);

        $teachers = [$teacher1, $teacher2];

        $this->assignmentServiceMock->expects($this->once())
            ->method('findEntryByIdentifier')
            ->with(5)
            ->will($this->returnValue($entry));

        $this->publicationServiceMock->expects($this->once())
            ->method('getPublication')
            ->with(10)
            ->will($this->returnValue($contentObjectPublication));

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->with(8)
            ->will($this->returnValue($course));

        $this->contentObjectRepositoryMock->expects($this->once())
            ->method('findById')
            ->with(14)
            ->will($this->returnValue($assignment));

        $this->courseServiceMock->expects($this->once())
            ->method('getTeachersFromCourse')
            ->will($this->returnValue($teachers));

        $this->entityServiceManagerMock->expects($this->once())
            ->method('getEntityServiceByType')
            ->with(1)
            ->will($this->returnValue($this->userEntityServiceMock));

        $this->userEntityServiceMock->expects($this->once())
            ->method('getUsersForEntity')
            ->with(20)
            ->will($this->returnValue([$entryUser]));

        $filters = [];

        $this->filterManagerMock->expects($this->exactly(3))
            ->method('getOrCreateFilterByContextPath')
            ->will(
                $this->returnCallback(
                    function ($path, TranslationContext $translationContext) use (&$filters) {
                        $notificationContext = new NotificationContext();
                        $notificationContext->setPath($path);

                        $filter = new Filter();
                        $filter->setNotificationContext($notificationContext);
                        $filter->setDescriptionContext($translationContext->getTranslationVariable());

                        $filters[] = $filter;

                        return $filter;
                    }
                )
            );

        $this->notificationManagerMock->expects($this->once())
            ->method('createNotificationForUsers')
            ->with(
                $this->callback(
                    function ($url) {
                        return $url == 'index.php?application=Chamilo%5CApplication%5CWeblcms&go=CourseViewer&course=8&tool=Assignment&tool_action=Display&publication=10&assignment_display_action=Entry&entry_id=5&entity_id=20&entity_type=1';
                    }
                ),
                $this->callback(
                    function (array $viewingContexts) {
                        /** @var \Chamilo\Core\Notification\Domain\ViewingContext[] $viewingContexts */
                        return $viewingContexts[0]->getTranslationContext()->getTranslationVariable() == 'NotificationNewAssignmentEntry';
                    }
                ),
                $this->callback(
                    function (\DateTime $date) use ($entryDate) {
                        return $entryDate == $date->getTimestamp();
                    }
                ),
                $this->callback(
                    function ($targetUserIds = []) {
                        return $targetUserIds == [1 => 19, 2 => 17];
                    }
                ),
                $this->callback(
                    function ($argFilters = []) use (&$filters) {
                        return $argFilters == $filters;
                    }
                ),
                $this->callback(
                    function (
                        $contextPaths = []
                    ) {
                        $wantedContextPaths = [
                            'Chamilo',
                            'Assignment',
                            'Chamilo\Application\Weblcms::Course:8',
                            'Chamilo\Application\Weblcms::Tool:::Course:8',
                            'Chamilo\Application\Weblcms::ContentObjectPublication:10'
                        ];

                        return $contextPaths == $wantedContextPaths;
                    }
                )
            );

        $this->entryNotificationJobProcessor->processJob($job);
    }

    /**
     * @expectedException \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     *
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessWithInvalidAssignment()
    {
        $entryDate = time();

        $job = new Job();
        $job->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, 5);

        $entry = new Entry();
        $entry->setId(5);
        $entry->setContentObjectPublicationId(10);
        $entry->setEntityId(20);
        $entry->setEntityType(1);
        $entry->setUserId(20);
        $entry->setSubmitted($entryDate);

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(10);
        $contentObjectPublication->set_course_id(8);
        $contentObjectPublication->set_content_object_id(14);

        $course = new Course();
        $course->setId(8);

        $this->assignmentServiceMock->expects($this->once())
            ->method('findEntryByIdentifier')
            ->with(5)
            ->will($this->returnValue($entry));

        $this->publicationServiceMock->expects($this->once())
            ->method('getPublication')
            ->with(10)
            ->will($this->returnValue($contentObjectPublication));

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->with(8)
            ->will($this->returnValue($course));

        $this->contentObjectRepositoryMock->expects($this->once())
            ->method('findById')
            ->with(14)
            ->will($this->returnValue(null));

        $this->entryNotificationJobProcessor->processJob($job);
    }

    /**
     * @expectedException \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     *
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessWithInvalidCourse()
    {
        $entryDate = time();

        $job = new Job();
        $job->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, 5);

        $entry = new Entry();
        $entry->setId(5);
        $entry->setContentObjectPublicationId(10);
        $entry->setEntityId(20);
        $entry->setEntityType(1);
        $entry->setUserId(20);
        $entry->setSubmitted($entryDate);

        $contentObjectPublication = new ContentObjectPublication();
        $contentObjectPublication->setId(10);
        $contentObjectPublication->set_course_id(8);
        $contentObjectPublication->set_content_object_id(14);

        $this->assignmentServiceMock->expects($this->once())
            ->method('findEntryByIdentifier')
            ->with(5)
            ->will($this->returnValue($entry));

        $this->publicationServiceMock->expects($this->once())
            ->method('getPublication')
            ->with(10)
            ->will($this->returnValue($contentObjectPublication));

        $this->courseServiceMock->expects($this->once())
            ->method('getCourseById')
            ->with(8)
            ->will($this->returnValue(null));

        $this->entryNotificationJobProcessor->processJob($job);
    }

    /**
     * @expectedException \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     *
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessWithInvalidContentObjectPublication()
    {
        $entryDate = time();

        $job = new Job();
        $job->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, 5);

        $entry = new Entry();
        $entry->setId(5);
        $entry->setContentObjectPublicationId(10);
        $entry->setEntityId(20);
        $entry->setEntityType(1);
        $entry->setUserId(20);
        $entry->setSubmitted($entryDate);

        $this->assignmentServiceMock->expects($this->once())
            ->method('findEntryByIdentifier')
            ->with(5)
            ->will($this->returnValue($entry));

        $this->publicationServiceMock->expects($this->once())
            ->method('getPublication')
            ->with(10)
            ->will($this->returnValue(null));

        $this->entryNotificationJobProcessor->processJob($job);
    }

    /**
     * @expectedException \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     *
     * @throws \Chamilo\Core\Queue\Exceptions\JobNoLongerValidException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testProcessWithInvalidEntry()
    {
        $job = new Job();
        $job->setParameter(EntryNotificationJobProcessor::PARAM_ENTRY_ID, 5);

        $this->assignmentServiceMock->expects($this->once())
            ->method('findEntryByIdentifier')
            ->with(5)
            ->will($this->returnValue(null));

        $this->entryNotificationJobProcessor->processJob($job);
    }
}

