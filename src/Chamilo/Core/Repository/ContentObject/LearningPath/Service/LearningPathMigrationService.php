<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Migrates old learning paths to the new learning path structure
 *
 * TODO: FIX TRACKING
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
     * LearningPathMigrationService constructor.
     *
     * @param LearningPathService $learningPathService
     * @param LearningPathTrackingRepository $learningPathTrackingRepository
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathService $learningPathService, LearningPathTrackingRepository $learningPathTrackingRepository,
        ContentObjectRepository $contentObjectRepository
    )
    {
        $this->learningPathService = $learningPathService;
        $this->learningPathTrackingRepository = $learningPathTrackingRepository;
        $this->contentObjectRepository = $contentObjectRepository;
    }

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

    /**
     * Migrates a single learning path, recursively
     *
     * @param LearningPath $learningPath
     * @param int $parentId
     * @param LearningPathChild $parentLearningPathChild
     */
    protected function migrateLearningPath(
        LearningPath $learningPath, $parentId, LearningPathChild $parentLearningPathChild = null
    )
    {
        $complexContentObjectItems = $this->getComplexContentObjectItemsForParent($parentId);
        foreach ($complexContentObjectItems as $complexContentObjectItem)
        {
            /** @var ComplexLearningPathItem $complexContentObjectItem */

            $childContentObject = $this->contentObjectRepository->findById($complexContentObjectItem->get_ref());

            if ($complexContentObjectItem->get_type() == LearningPath::class_name())
            {
                /** @var LearningPath $childContentObject */

                $contentObject = $this->getOrCreateSectionForLearningPath($childContentObject);
                $learningPathChild = $this->createLearningPathChildForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentLearningPathChild
                );
            }
            else
            {
                /** @var LearningPathItem $childContentObject */

                $contentObject = $this->contentObjectRepository->findById($childContentObject->get_reference());
                $learningPathChild = $this->createLearningPathChildForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentLearningPathChild,
                    $childContentObject
                );
            }

            if ($complexContentObjectItem->get_type() == LearningPath::class_name())
            {
                $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $learningPathChild);
            }

            if(!empty($complexContentObjectItem->get_prerequisites()))
            {
                if(!$learningPath->enforcesDefaultTraversingOrder())
                {
                    $learningPath->setEnforceDefaultTraversingOrder(true);
                    $learningPath->update();
                }
            }
        }
    }

    /**
     * Creates a LearningPathChild for a given LearningPath, ContentObject and parent LearningPathChild
     *
     * @param LearningPath $learningPath
     * @param ComplexContentObjectItem $complexContentObjectItem
     * @param ContentObject $contentObject
     * @param LearningPathChild $parentLearningPathChild
     * @param LearningPathItem $learningPathItem
     *
     * @return LearningPathChild
     *
     * @throws \Exception
     */
    protected function createLearningPathChildForContentObject(
        LearningPath $learningPath, ComplexContentObjectItem $complexContentObjectItem, ContentObject $contentObject,
        LearningPathChild $parentLearningPathChild = null, LearningPathItem $learningPathItem = null
    )
    {
        $parentId = !is_null($parentLearningPathChild) ? $parentLearningPathChild->getId() : 0;

        $learningPathChild = new LearningPathChild();

        $learningPathChild->setLearningPathId((int) $learningPath->getId());
        $learningPathChild->setParentLearningPathChildId((int) $parentId);
        $learningPathChild->setContentObjectId((int) $contentObject->getId());
        $learningPathChild->setUserId((int) $contentObject->get_owner_id());
        $learningPathChild->setAddedDate(time());

        if ($learningPathItem instanceof $learningPathItem)
        {
            $learningPathChild->setMaxAttempts($learningPathItem->get_max_attempts());
            $learningPathChild->setMasteryScore($learningPathItem->get_mastery_score());
            $learningPathChild->setAllowHints($learningPathItem->get_allow_hints());
            $learningPathChild->setShowScore($learningPathItem->get_show_score());
            $learningPathChild->setShowCorrection($learningPathItem->get_show_correction());
            $learningPathChild->setShowSolution($learningPathItem->get_show_solution());
            $learningPathChild->setShowAnswerFeedback($learningPathItem->get_show_answer_feedback());
            $learningPathChild->setFeedbackLocation($learningPathItem->get_feedback_location());
        }

        if(!$this->learningPathTrackingRepository->create($learningPathChild))
        {
            throw new \Exception('Could not create a new learning path child');
        }

        echo "Create LearningPathChild " . $learningPathChild->getId();

//        $this->changeTrackingToLearningPathChildId($complexContentObjectItem, $learningPathChild);

        return $learningPathChild;
    }

    /**
     * Creates or retrieves a section from the cache for the given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return Section
     *
     * @throws \Exception
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

            if(!$section->create())
            {
                throw new \Exception('Could not create a new section');
            }

            echo "Create Section " . $section->getId();

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

        return $this->contentObjectRepository->findAll(
            ComplexLearningPathItem::class_name(), new DataClassRetrievesParameters(
                $condition, null, null, array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class_name(),
                            ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                        )
                    )
                )
            )
        );
    }

    /**
     * Change the tracking tables to match the new identifiers from the
     * ComplexContentObject identifiers to the LearningPathChild identifers
     *
     * @param ComplexContentObjectItem $complexContentObjectItem
     * @param LearningPathChild $learningPathChild
     */
    protected function changeTrackingToLearningPathChildId(
        ComplexContentObjectItem $complexContentObjectItem, LearningPathChild $learningPathChild
    )
    {
        $this->learningPathTrackingRepository->changeLearningPathChildIdInLearningPathChildAttempts(
            $complexContentObjectItem->getId(), $learningPathChild->getId()
        );
    }

}