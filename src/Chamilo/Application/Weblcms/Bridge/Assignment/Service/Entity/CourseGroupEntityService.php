<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupEntityService implements EntityServiceInterface
{
    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $targetCourseGroupIds = [];

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService
     */
    protected $entryPlagiarismResultService;

    /**
     * UserEntityService constructor.
     *
     * @param AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService $entryPlagiarismResultService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        AssignmentService $assignmentService, EntryPlagiarismResultService $entryPlagiarismResultService,
        Translator $translator, UserService $userService
    )
    {
        $this->assignmentService = $assignmentService;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->entryPlagiarismResultService = $entryPlagiarismResultService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        return $this->assignmentService->findTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters)
    {
        return $this->assignmentService->countTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication), $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int
     */
    public function countEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->countTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->findTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultService->findCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\FilterParameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultService->countCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int[]
     */
    protected function getTargetCourseGroupIds(ContentObjectPublication $contentObjectPublication)
    {
        $id = $contentObjectPublication->getId();

        if (!array_key_exists($id, $this->targetCourseGroupIds))
        {
            $this->targetCourseGroupIds[$id] = [];

            /** @var \Chamilo\Libraries\Storage\ResultSet\ResultSet $courseGroups */
            $courseGroups = DataManager::retrieve_publication_target_course_groups(
                $contentObjectPublication->getId(), $contentObjectPublication->get_course_id()
            );

            while ($courseGroup = $courseGroups->next_result())
            {
                $this->targetCourseGroupIds[$id][] = $courseGroup->getId();
            }
        }

        return $this->targetCourseGroupIds[$id];
    }

    /**
     * @return string
     */
    public function getPluralEntityName()
    {
        return $this->translator->trans(
            'CourseGroupsEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
        );
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->translator->trans(
            'CourseGroupEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
        );
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters $entityTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTable(
        Application $application, EntityTableParameters $entityTableParameters
    )
    {
        $entityTableParameters->setEntityClass(CourseGroup::class);
        $entityTableParameters->setEntityProperties([CourseGroup::PROPERTY_NAME]);
        $entityTableParameters->setEntityHasMultipleMembers(true);

        return new EntityTable($application, $entityTableParameters);
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    public function getEntryPlagiarismResultTable(
        Application $application, EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
    )
    {
        $entryPlagiarismResultTableParameters->setEntityClass(CourseGroup::class);
        $entryPlagiarismResultTableParameters->setEntityProperties([CourseGroup::PROPERTY_NAME]);

        return new EntryPlagiarismResultTable($application, $entryPlagiarismResultTableParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(ContentObjectPublication $contentObjectPublication, User $currentUser)
    {
        $availableEntityIdentifiers =
            $this->getAvailableEntityIdentifiersForUser($contentObjectPublication, $currentUser);

        $reversedArray = array_reverse($availableEntityIdentifiers);
        return array_pop($reversedArray);
        // return $availableEntityIdentifiers[0];
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(
        ContentObjectPublication $contentObjectPublication, User $currentUser
    )
    {
        $courseGroups =
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::get_user_course_groups(
                $currentUser->getId(), $contentObjectPublication->get_course_id()
            );

        $subscribedGroupIds = [];

        foreach ($courseGroups as $courseGroup)
        {
            $subscribedGroupIds[] = $courseGroup->getId();
        }

        $targetGroupIds = $this->getTargetCourseGroupIds($contentObjectPublication);

        return array_intersect($subscribedGroupIds, $targetGroupIds);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, ContentObjectPublication $contentObjectPublication, $entityId)
    {
        $availableEntityIdentifiers = $this->getAvailableEntityIdentifiersForUser($contentObjectPublication, $user);

        return in_array($entityId, $availableEntityIdentifiers);
    }

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForEntity($entityId)
    {
        /** @var CourseGroup $courseGroup */
        $courseGroup = DataManager::retrieve_by_id(CourseGroup::class_name(), $entityId);
        $courseGroupMemberIds = $courseGroup->get_members(true, true);

        return $this->userService->findUsersByIdentifiers($courseGroupMemberIds);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity)
    {
        if (!$entity instanceof CourseGroup)
        {
            throw new \InvalidArgumentException('The given entity must be of the type ' . CourseGroup::class);
        }

        return $entity->get_name();
    }

    /**
     * @param string[] $entityArray
     *
     * @return string
     */
    public function renderEntityNameByArray($entityArray = [])
    {
        return $entityArray[CourseGroup::PROPERTY_NAME];
    }

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId)
    {
        $entity = DataManager::retrieve_by_id(CourseGroup::class, $entityId);
        if (!$entity instanceof CourseGroup)
        {
            throw new \InvalidArgumentException('The given course group with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }
}
