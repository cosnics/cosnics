<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * Migrates old learning paths to the new learning path structure
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MigrationService
{
    /**
     * The ComplexContentObjectItemsMapping for the given learning path
     *
     * @var array
     */
    protected $complexContentObjectItemsMappingForLearningPath;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * Cache for the sections that are converted from learning paths
     *
     * @var Section[]
     */
    protected $sectionFromLearningPathCache;

    /**
     * @var TrackingRepository
     */
    protected $trackingRepository;

    /**
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * @param LearningPathService $learningPathService
     * @param TreeNodeDataService $treeNodeDataService
     * @param TrackingRepository $trackingRepository
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathService $learningPathService, TreeNodeDataService $treeNodeDataService,
        TrackingRepository $trackingRepository, ContentObjectRepository $contentObjectRepository
    )
    {
        ini_set('memory_limit', - 1);

        $this->learningPathService = $learningPathService;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->trackingRepository = $trackingRepository;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * Creates a TreeNodeData for a given LearningPath, ContentObject and parent TreeNodeData
     *
     * @param LearningPath $learningPath
     * @param ContentObject $contentObject
     * @param TreeNodeData $parentTreeNodeData
     * @param LearningPathItem $learningPathItem
     *
     * @return TreeNodeData
     *
     * @throws \Exception
     */
    protected function createTreeNodeDataForContentObject(
        LearningPath $learningPath, ContentObject $contentObject, TreeNodeData $parentTreeNodeData = null,
        LearningPathItem $learningPathItem = null
    )
    {
        $parentId = !is_null($parentTreeNodeData) ? $parentTreeNodeData->getId() : 0;

        $treeNodeData = new TreeNodeData();

        $treeNodeData->setLearningPathId((int) $learningPath->getId());
        $treeNodeData->setParentTreeNodeDataId((int) $parentId);
        $treeNodeData->setContentObjectId((int) $contentObject->getId());
        $treeNodeData->setUserId((int) $contentObject->get_owner_id());
        $treeNodeData->setAddedDate(time());

        if ($learningPathItem && $learningPathItem instanceof $learningPathItem)
        {
            $treeNodeData->setMaxAttempts((int) $learningPathItem->get_max_attempts());
            $treeNodeData->setMasteryScore((int) $learningPathItem->get_mastery_score());
            $treeNodeData->setAllowHints((bool) $learningPathItem->get_allow_hints());
            $treeNodeData->setShowScore((bool) $learningPathItem->get_show_score());
            $treeNodeData->setShowCorrection((bool) $learningPathItem->get_show_correction());
            $treeNodeData->setShowSolution((bool) $learningPathItem->get_show_solution());
            $treeNodeData->setShowAnswerFeedback((int) $learningPathItem->get_show_answer_feedback());
            $treeNodeData->setFeedbackLocation((int) $learningPathItem->get_feedback_location());
        }

        $this->treeNodeDataService->createTreeNodeData($treeNodeData);

        echo "Create TreeNodeData " . $treeNodeData->getId() . PHP_EOL;

        return $treeNodeData;
    }

    /**
     * Change the tracking tables to match the new identifiers from the
     * ComplexContentObject identifiers to the TreeNodeData identifers
     *
     * @param LearningPath $learningPath
     */
    protected function fixLearningPathTracking(LearningPath $learningPath)
    {
        $treeNodeAttempts = $this->trackingRepository->findTreeNodeAttemptsForLearningPath($learningPath);

        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $newLearningPathItemId =
                $this->complexContentObjectItemsMappingForLearningPath[$treeNodeAttempt->getTreeNodeDataId()];

            if (!$newLearningPathItemId)
            {
                //                echo 'New learning path item id not found for id ' .
                //                    $treeNodeAttempt->get_learning_path_item_id() . PHP_EOL;

                continue;
            }

            $treeNodeAttempt->setTreeNodeDataId($newLearningPathItemId);
            $this->trackingRepository->update($treeNodeAttempt);
        }

        $this->trackingRepository->clearTreeNodeAttemptCache();
    }

    /**
     * Returns the complex content object items for a given parent
     *
     * @param int $parentId
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function getComplexContentObjectItemsForParent(
        $parentId
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($parentId)
        );

        return $this->contentObjectRepository->findAll(
            ComplexContentObjectItem::class, new DataClassRetrievesParameters(
                $condition, null, null, new OrderBy(array(
                        new OrderProperty(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                            )
                        )
                    ))
            )
        );
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
    protected function getOrCreateSectionForLearningPath(
        LearningPath $learningPath
    )
    {
        if (!array_key_exists($learningPath->getId(), $this->sectionFromLearningPathCache))
        {
            $section = new Section();

            $section->set_title($learningPath->get_title());
            $section->set_description($learningPath->get_description());
            $section->set_creation_date($learningPath->get_creation_date());
            $section->set_owner_id($learningPath->get_owner_id());

            if (!$this->contentObjectRepository->create($section))
            {
                throw new Exception('Could not create a new section');
            }

            $this->contentObjectRepository->copyIncludesFromContentObject($learningPath, $section);

            echo "Create Section " . $section->getId() . PHP_EOL;

            $this->sectionFromLearningPathCache[$learningPath->getId()] = $section;
        }

        return $this->sectionFromLearningPathCache[$learningPath->getId()];
    }

    /**
     * Checks if the learning path has prerequisites, uses the mapping from ComplexContentObjectItem classes
     *
     * @return bool
     */
    protected function hasLearningPathPrerequisites()
    {
        $conditions = [];

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexLearningPathItem::class, ComplexLearningPathItem::PROPERTY_PREREQUISITES
                ), null
            )
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ComplexLearningPathItem::class, ComplexLearningPathItem::PROPERTY_ID
            ), array_keys($this->complexContentObjectItemsMappingForLearningPath)
        );

        $condition = new AndCondition($conditions);

        return $this->contentObjectRepository->countAll(
                ComplexLearningPathItem::class, new DataClassCountParameters($condition)
            ) > 0;
    }

    /**
     * Migrates a single learning path, recursively
     *
     * @param LearningPath $learningPath
     * @param int $parentId
     * @param TreeNodeData $parentTreeNodeData
     */
    protected function migrateLearningPath(
        LearningPath $learningPath, $parentId, TreeNodeData $parentTreeNodeData = null
    )
    {
        $complexContentObjectItems = $this->getComplexContentObjectItemsForParent($parentId);
        foreach ($complexContentObjectItems as $complexContentObjectItem)
        {
            /** @var ComplexContentObjectItem $complexContentObjectItem */

            try
            {
                $childContentObject = $this->contentObjectRepository->findById($complexContentObjectItem->get_ref());
            }
            catch (Exception $ex)
            {
                continue;
            }

            if ($childContentObject instanceof LearningPath)
            {
                /** @var LearningPath $childContentObject */

                $contentObject = $this->getOrCreateSectionForLearningPath($childContentObject);
                $treeNodeData = $this->createTreeNodeDataForContentObject(
                    $learningPath, $contentObject, $parentTreeNodeData
                );

                $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $treeNodeData);
            }
            else
            {
                if (!$childContentObject instanceof LearningPathItem)
                {
                    echo('The given complex content object item does not reference a learning path item ' .
                        $complexContentObjectItem->getId());

                    continue;
                }

                /** @var LearningPathItem $childContentObject */

                try
                {
                    $contentObject = $this->contentObjectRepository->findById($childContentObject->get_reference());
                }
                catch (Exception $ex)
                {
                    continue;
                }

                if ($contentObject instanceof LearningPath)
                {
                    $contentObject = $this->getOrCreateSectionForLearningPath($contentObject);
                }

                $treeNodeData = $this->createTreeNodeDataForContentObject(
                    $learningPath, $contentObject, $parentTreeNodeData, $childContentObject
                );

                if ($contentObject instanceof Section)
                {
                    $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $treeNodeData);
                }
            }

            $this->complexContentObjectItemsMappingForLearningPath[$complexContentObjectItem->getId()] =
                $treeNodeData->getId();
        }
    }

    /**
     * Migrates the old learning paths to the new structure
     */
    public function migrateLearningPaths()
    {
        $learningPaths = $this->learningPathService->getLearningPaths();
        foreach ($learningPaths as $learningPath)
        {
            $this->complexContentObjectItemsMappingForLearningPath = [];

            $user = new User();
            $user->setId($learningPath->get_owner_id());

            $learningPathTreeNodeData = $this->treeNodeDataService->createTreeNodeDataForLearningPath(
                $learningPath, $user
            );

            $this->complexContentObjectItemsMappingForLearningPath[0] = $learningPathTreeNodeData->getId();

            $this->migrateLearningPath($learningPath, $learningPath->getId(), $learningPathTreeNodeData);

            if ($this->hasLearningPathPrerequisites())
            {
                $learningPath->setEnforceDefaultTraversingOrder(true);
                $this->contentObjectRepository->update($learningPath);

                echo 'Learning Path Prerequisites ' . $learningPath->getId() . PHP_EOL;
            }

            $this->fixLearningPathTracking($learningPath);
        }
    }
}