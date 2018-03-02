<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntityRenderer\CourseGroupEntityRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\CourseGroup\EntityTable;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup\EntryTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity
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
     * UserEntityService constructor.
     *
     * @param AssignmentService $assignmentService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(AssignmentService $assignmentService, Translator $translator)
    {
        $this->assignmentService = $assignmentService;
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return int
     */
    public function countEntities(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->countTargetCourseGroupsForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication)
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
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntitiesWithEntries(ContentObjectPublication $contentObjectPublication)
    {
        return $this->assignmentService->findTargetCourseGroupsWithEntriesForContentObjectPublication(
            $contentObjectPublication, $this->getTargetCourseGroupIds($contentObjectPublication)
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
            $application, $assignmentDataProvider, $this->assignmentService, $contentObjectPublication,
            $this->getTargetCourseGroupIds($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTable
     */
    public function getEntryTable(
        Application $application, AssignmentDataProvider $assignmentDataProvider,
        ContentObjectPublication $contentObjectPublication, $entityId
    )
    {
        return new EntryTable(
            $application, $assignmentDataProvider, $entityId, $this->assignmentService, $contentObjectPublication
        );
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
        $courseGroups =
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::get_user_course_groups(
                $currentUser->getId(), $contentObjectPublication->get_course_id()
            );

        $subscribedGroupIds = [];

        foreach($courseGroups as $courseGroup)
        {
            $subscribedGroupIds[] = $courseGroup->getId();
        }

        $targetGroupIds = $this->getTargetCourseGroupIds($contentObjectPublication);

        return array_intersect($subscribedGroupIds, $targetGroupIds);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityId)
    {
        /** @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup */
        $courseGroup = DataManager::retrieve_by_id(CourseGroup::class_name(), $entityId);

        if (!$courseGroup instanceof CourseGroup)
        {
            return false;
        }

        return $courseGroup->is_member($user);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param integer $entityId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer
     */
    public function getEntityRendererForEntityId(AssignmentDataProvider $assignmentDataProvider, $entityId)
    {
        return new CourseGroupEntityRenderer($assignmentDataProvider, $entityId);
    }
}