<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\CourseGroup\EntityTable;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use InvalidArgumentException;
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
     * UserEntityService constructor.
     *
     * @param AssignmentService $assignmentService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(AssignmentService $assignmentService, Translator $translator, UserService $userService)
    {
        $this->assignmentService = $assignmentService;
        $this->translator = $translator;
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication, Condition $condition = null)
    {
        return $this->assignmentService->countTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication), $condition
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
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(ContentObjectPublication $contentObjectPublication, User $currentUser)
    {
        $availableEntityIdentifiers =
            $this->getAvailableEntityIdentifiersForUser($contentObjectPublication, $currentUser);

        $availableEntityIdentifiers = array_reverse($availableEntityIdentifiers);

        return array_pop($availableEntityIdentifiers);
        // return $availableEntityIdentifiers[0];
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->translator->trans(
            'CourseGroupEntity', [], 'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
        );
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    public function getEntityTable(
        Application $application, AssignmentDataProvider $assignmentDataProvider,
        ContentObjectPublication $contentObjectPublication
    )
    {
        return new EntityTable(
            $application, $assignmentDataProvider, $contentObjectPublication, $this
        );
    }

    /**
     * @return string
     */
    public function getPluralEntityName()
    {
        return $this->translator->trans(
            'CourseGroupsEntity', [], 'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
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

            /** @var \Doctrine\Common\Collections\ArrayCollection $courseGroups */
            $courseGroups = DataManager::retrieve_publication_target_course_groups(
                $contentObjectPublication->getId(), $contentObjectPublication->get_course_id()
            );

            foreach ($courseGroups as $courseGroup)
            {
                $this->targetCourseGroupIds[$id][] = $courseGroup->getId();
            }
        }

        return $this->targetCourseGroupIds[$id];
    }

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForEntity($entityId)
    {
        /** @var CourseGroup $courseGroup */
        $courseGroup = DataManager::retrieve_by_id(CourseGroup::class, $entityId);
        $courseGroupMemberIds = $courseGroup->get_members(true, true);

        return $this->userService->findUsersByIdentifiers($courseGroupMemberIds);
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
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity)
    {
        if (!$entity instanceof CourseGroup)
        {
            throw new InvalidArgumentException('The given entity must be of the type ' . CourseGroup::class);
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
            throw new InvalidArgumentException('The given course group with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = null
    )
    {
        return $this->assignmentService->findTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication), $condition, $offset,
            $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieveEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->findTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication)
        );
    }
}