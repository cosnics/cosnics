<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

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
     * @var UserService
     */
    protected $userService;

    /**
     * @var array
     */
    protected $targetCourseGroupIds = [];

    public function __construct(PublicationEntityServiceManager $publicationEntityServiceManager, UserService $userService)
    {
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->userService = $userService;
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
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser): int
    {
        $availableEntityIdentifiers =
            $this->getAvailableEntityIdentifiersForUser($this->getContentObjectPublication(), $currentUser);

        return array_pop(array_reverse($availableEntityIdentifiers));
    }
}