<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupEntityService implements EntityServiceInterface
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
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var array
     */
    protected $targetPlatformGroupIds = [];

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
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator|\Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = []
    )
    {
        return $this->assignmentService->findTargetPlatformGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetPlatformGroupIds($contentObjectPublication),
            $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication, Condition $condition = null)
    {
        return $this->assignmentService->countTargetPlatformGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetPlatformGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int
     */
    public function countEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->countTargetPlatformGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetPlatformGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->findTargetPlatformGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetPlatformGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int[]
     */
    protected function getTargetPlatformGroupIds(ContentObjectPublication $contentObjectPublication)
    {
        $id = $contentObjectPublication->getId();

        if (!array_key_exists($id, $this->targetPlatformGroupIds))
        {
            $this->targetPlatformGroupIds[$id] = [];

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
     * @return string
     */
    public function getPluralEntityName()
    {
        return $this->translator->trans(
            'PlatformGroupsEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
        );
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->translator->trans(
            'PlatformGroupEntity', [],
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
        $entityTableParameters->setEntityClass(Group::class);
        $entityTableParameters->setEntityProperties([Group::PROPERTY_NAME]);
        $entityTableParameters->setEntityHasMultipleMembers(true);

        return new EntityTable($application, $entityTableParameters);
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

        return $availableEntityIdentifiers[0];
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
        $subscribedGroupIds = \Chamilo\Core\Group\Storage\DataManager::retrieve_all_subscribed_groups_array(
            $currentUser->getId(), true
        );

        $targetGroupIds = $this->getTargetPlatformGroupIds($contentObjectPublication);

        return array_values(array_intersect($subscribedGroupIds, $targetGroupIds));
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
        /** @var Group $entity */
        $entity = DataManager::retrieve_by_id(Group::class_name(), $entityId);
        $groupUserIds = $entity->get_users(true, true);

        return $this->userService->findUsersByIdentifiers($groupUserIds);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity)
    {
        if(!$entity instanceof Group)
        {
            throw new \InvalidArgumentException('The given entity must be of the type ' . Group::class);
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
        return $entityArray[Group::PROPERTY_NAME];
    }

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId)
    {
        $entity = DataManager::retrieve_by_id(Group::class, $entityId);
        if(!$entity instanceof Group)
        {
            throw new \InvalidArgumentException('The given platform group with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }
}