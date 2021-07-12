<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationPlatformGroupEntityService implements PublicationEntityServiceInterface
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
     * @var Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $targetPlatformGroupIds = [];

    public function __construct(PublicationEntityServiceManager $publicationEntityServiceManager, UserService $userService, Translator $translator)
    {
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication()
    {
        return $this->publicationEntityServiceManager->getContentObjectPublication();
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        $contentObjectPublication = $this->getContentObjectPublication();
        /** @var \Chamilo\Libraries\Storage\ResultSet\ResultSet $platformGroups */
        $platformGroups = DataManager::retrieve_publication_target_platform_groups(
            $contentObjectPublication->getId(), $contentObjectPublication->get_course_id()
        );
        $groupIds = array();
        while ($platformGroup = $platformGroups->next_result())
        {
            $groupIds[] = $platformGroup->getId();
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
        /** @var Group $platformGroup */
        $platformGroup = DataManager::retrieve_by_id(Group::class_name(), $entityId);
        $platformGroupMemberIds = $platformGroup->get_users(true, true);
        return $this->userService->findUsersByIdentifiers($platformGroupMemberIds);
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

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param User $currentUser
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(ContentObjectPublication $contentObjectPublication, User $currentUser): array
    {
        $subscribedGroupIds = \Chamilo\Core\Group\Storage\DataManager::retrieve_all_subscribed_groups_ids_recursive($currentUser->getId());

        $targetGroupIds = $this->getTargetPlatformGroupIds($contentObjectPublication);

        return array_values(array_intersect($subscribedGroupIds, $targetGroupIds));
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return int[]
     */
    protected function getTargetPlatformGroupIds(ContentObjectPublication $contentObjectPublication)
    {
        $id = $contentObjectPublication->getId();

        if (!array_key_exists($id, $this->targetPlatformGroupIds))
        {
            $this->targetPlatformGroupIds[$id] = [];

            /** @var \Chamilo\Libraries\Storage\ResultSet\ResultSet $platformGroups */
            $platformGroups = DataManager::retrieve_publication_target_platform_groups(
                $contentObjectPublication->getId(), $contentObjectPublication->get_course_id()
            );

            while ($platformGroup = $platformGroups->next_result())
            {
                $this->targetPlatformGroupIds[$id][] = $platformGroup->getId();
            }
        }

        return $this->targetPlatformGroupIds[$id];
    }

    /**
     * @param User $currentUser
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int
    {
        $availableEntityIdentifiers =
            $this->getAvailableEntityIdentifiersForUser($this->getContentObjectPublication(), $currentUser);

        return $availableEntityIdentifiers[0];
    }

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string
    {
        $platformGroup = DataManager::retrieve_by_id(Group::class, $entityId);
        return $platformGroup->get_name();
    }

    /**
     * @return string
     */
    public function getPluralEntityName(): string
    {
        return $this->translator->trans(
            'PlatformGroupsEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Evaluation'
        );
    }
}