<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Migrates old learning paths to the new learning path structure
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathMigrationService
{
    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var LearningPathTrackingRepository
     */
    protected $learningPathTrackingRepository;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * Cache for the sections that are converted from learning paths
     *
     * @var Section[]
     */
    protected $sectionFromLearningPathCache;

    /**
     * Migrates the old learning paths to the new structure
     */
    public function migrateLearningPaths()
    {
        $learningPaths = $this->learningPathService->getLearningPaths();
        foreach ($learningPaths as $learningPath)
        {
            $this->migrateLearningPath($learningPath, $learningPath->getId());
        }
    }

    protected function migrateLearningPath(
        LearningPath $learningPath, $parentId, LearningPathChild $parentLearningPathChild = null
    )
    {
        $complexContentObjectItems = $this->getComplexContentObjectItemsForParent($parentId);
        foreach ($complexContentObjectItems as $complexContentObjectItem)
        {
            /** @var ComplexContentObjectItem $complexContentObjectItem */
            $childContentObject = $this->contentObjectRepository->findById($complexContentObjectItem->get_ref());

            if ($complexContentObjectItem->get_type() == LearningPath::class_name())
            {
                /** @var LearningPath $childContentObject */
                $contentObject = $this->getOrCreateSectionForLearningPath($childContentObject);
            }
            else
            {
                /** @var LearningPathItem $childContentObject */
                $contentObject = $this->contentObjectRepository->findById($childContentObject->get_ref());
            }

            $learningPathChild =
                $this->createLearningPathChildForContentObject($learningPath, $contentObject, $parentLearningPathChild);

            if ($complexContentObjectItem->get_type() == LearningPath::class_name())
            {
                $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $learningPathChild);
            }
        }
    }

    protected function createLearningPathChildForContentObject(
        LearningPath $learningPath, ContentObject $contentObject, LearningPathChild $parentLearningPathChild = null
    )
    {
        $parentId = !is_null($parentLearningPathChild) ? $parentLearningPathChild->getId() : 0;

        $learningPathChild = new LearningPathChild();

        $learningPathChild->setLearningPathId((int) $learningPath->getId());
        $learningPathChild->setParentLearningPathChildId((int) $parentId);
        $learningPathChild->setContentObjectId((int) $contentObject->getId());
        $learningPathChild->setUserId((int) $contentObject);
        $learningPathChild->setAddedDate(time());

        $this->learningPathTrackingRepository->create($learningPathChild);

        return $learningPathChild;
    }

    /**
     * Creates or retrieves a section from the cache for the given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return Section
     */
    protected function getOrCreateSectionForLearningPath(LearningPath $learningPath)
    {
        if (!array_key_exists($learningPath->getId(), $this->sectionFromLearningPathCache))
        {
            $section = new Section();

            $section->set_title($learningPath->get_title());
            $section->set_description($learningPath->get_description());
            $section->set_creation_date($learningPath->get_creation_date());
            $section->set_owner_id($learningPath->get_owner_id());

            $section->create();

            $this->sectionFromLearningPathCache[$learningPath->getId()] = $section;
        }

        return $this->sectionFromLearningPathCache[$learningPath->getId()];
    }

    /**
     * Returns the complex content object items for a given parent
     *
     * @param int $parentId
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    protected function getComplexContentObjectItemsForParent($parentId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(),
                ComplexContentObjectItem::PROPERTY_PARENT
            ),
            new StaticConditionVariable($parentId)
        );

        return DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(),
            $condition
        );
    }

}