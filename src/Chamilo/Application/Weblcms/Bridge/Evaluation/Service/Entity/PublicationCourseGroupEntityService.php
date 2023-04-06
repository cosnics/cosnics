<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationCourseGroupEntityService implements PublicationEntityServiceInterface
{
    /**
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var CourseGroupService
     */
    protected $courseGroupService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $targetCourseGroupIds = [];

    public function __construct(PublicationEntityServiceManager $publicationEntityServiceManager, CourseGroupService $courseGroupService, UserService $userService, Translator $translator)
    {
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->courseGroupService = $courseGroupService;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication(): ContentObjectPublication
    {
        return $this->publicationEntityServiceManager->getContentObjectPublication();
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        $contentObjectPublication = $this->getContentObjectPublication();
        /** @var \Chamilo\Libraries\Storage\ResultSet\ResultSet $courseGroups */
        $courseGroups = DataManager::retrieve_publication_target_course_groups($contentObjectPublication->getId(), $contentObjectPublication->get_course_id());
        $groupIds = array();
        while ($courseGroup = $courseGroups->next_result())
        {
            $groupIds[] = $courseGroup->getId();
        }
        return $groupIds;
    }

    /**
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityId): array
    {
        /** @var CourseGroup $courseGroup */
        $courseGroup = DataManager::retrieve_by_id(CourseGroup::class_name(), $entityId);
        $courseGroupMemberIds = $courseGroup->get_members(true, true);
        return $this->userService->findUsersByIdentifiers($courseGroupMemberIds);
    }

    /**
     * @param User $user
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityId): bool
    {
        $availableEntityIdentifiers = $this->getAvailableEntityIdentifiersForUser($this->getContentObjectPublication(), $user);
        return in_array($entityId, $availableEntityIdentifiers);

    }

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
     * @param User $currentUser
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int
    {
        $availableEntityIdentifiers =
            $this->getAvailableEntityIdentifiersForUser($this->getContentObjectPublication(), $currentUser);

        $array = array_reverse($availableEntityIdentifiers);
        return array_pop($array);
    }

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string
    {
        $courseGroup = $this->courseGroupService->getCourseGroupById($entityId);
        return $courseGroup->get_name();
    }

    /**
     * @return string
     */
    public function getPluralEntityName(): string
    {
        return $this->translator->trans(
            'CourseGroupsEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation'
        );
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->translator->trans(
            'CourseGroupEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation'
        );
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
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId): String
    {
        $entity = DataManager::retrieve_by_id(CourseGroup::class, $entityId);
        if (!$entity instanceof CourseGroup)
        {
            throw new \InvalidArgumentException('The given course group with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }
}