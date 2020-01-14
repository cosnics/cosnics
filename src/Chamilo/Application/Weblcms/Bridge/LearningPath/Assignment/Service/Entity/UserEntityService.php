<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserEntityService implements EntityServiceInterface
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
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService
     */
    protected $entryPlagiarismResultService;

    /**
     * @var array
     */
    protected $targetUsersCache = [];

    /**
     * UserEntityService constructor.
     *
     * @param AssignmentService $assignmentService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService $entryPlagiarismResultService
     */
    public function __construct(
        AssignmentService $assignmentService, Translator $translator,
        EntryPlagiarismResultService $entryPlagiarismResultService
    )
    {
        $this->assignmentService = $assignmentService;
        $this->translator = $translator;
        $this->entryPlagiarismResultService = $entryPlagiarismResultService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntities(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->assignmentService->findTargetUsersForTreeNodeData(
            $contentObjectPublication, $treeNodeData, $this->getTargetUserIdsForPublication($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntities(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->assignmentService->countTargetUsersForTreeNodeData(
            $contentObjectPublication, $treeNodeData, $this->getTargetUserIdsForPublication($contentObjectPublication), $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return int
     */
    public function countEntitiesWithEntries(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    )
    {
        return $this->assignmentService->countTargetUsersWithEntriesForTreeNodeData(
            $contentObjectPublication, $treeNodeData, $this->getTargetUserIdsForPublication($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveEntitiesWithEntries(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    )
    {
        return $this->assignmentService->findTargetUsersWithEntriesForTreeNodeData(
            $contentObjectPublication, $treeNodeData, $this->getTargetUserIdsForPublication($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultService->countUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultService->findUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int[]
     */
    protected function getTargetUserIdsForPublication(ContentObjectPublication $contentObjectPublication)
    {
        $id = $contentObjectPublication->getId();

        if (!array_key_exists($id, $this->targetUsersCache))
        {
            $this->targetUsersCache[$id] = [];

            $targetUsers = DataManager::get_publication_target_users_by_publication_id(
                $contentObjectPublication->getId()
            );

            foreach ($targetUsers as $targetUser)
            {
                $this->targetUsersCache[$id][] = $targetUser instanceof User ?
                    $targetUser->getId() : $targetUser[User::PROPERTY_ID];
            }
        }

        return $this->targetUsersCache[$id];
    }

    /**
     * @return string
     */
    public function getPluralEntityName()
    {
        return $this->translator->trans(
            'UsersEntity', [],
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment'
        );
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->translator->trans(
            'UserEntity', [],
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
        $entityTableParameters->setEntityClass(User::class);
        $entityTableParameters->setEntityProperties([User::PROPERTY_FIRSTNAME, User::PROPERTY_LASTNAME, User::PROPERTY_OFFICIAL_CODE]);
        $entityTableParameters->setEntityHasMultipleMembers(false);

        return new EntityTable($application, $entityTableParameters);
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    public function getEntryPlagiarismResultTable(
        Application $application,
        EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
    )
    {
        $entryPlagiarismResultTableParameters->setEntityClass(User::class);
        $entryPlagiarismResultTableParameters->setEntityProperties([User::PROPERTY_FIRSTNAME, User::PROPERTY_LASTNAME, User::PROPERTY_OFFICIAL_CODE]);

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
        return $currentUser->getId();
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
        return [$currentUser->getId()];
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
        return $user->getId() == $entityId;
    }

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForEntity($entityId)
    {
        return [$entityId];
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     *
     * @return String
     */
    public function renderEntityName(DataClass $entity)
    {
        if (!$entity instanceof User)
        {
            throw new \InvalidArgumentException('The given entity must be of the type ' . User::class);
        }

        return $entity->get_fullname();
    }

    /**
     * @param string[] $entityArray
     *
     * @return string
     */
    public function renderEntityNameByArray($entityArray = [])
    {
        return User::fullname($entityArray[User::PROPERTY_FIRSTNAME], $entityArray[User::PROPERTY_LASTNAME]);
    }

    /**
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameById($entityId)
    {
        $entity = DataManager::retrieve_by_id(User::class, $entityId);
        if (!$entity instanceof User)
        {
            throw new \InvalidArgumentException('The given user with id ' . $entityId . ' does not exist');
        }

        return $this->renderEntityName($entity);
    }
}
