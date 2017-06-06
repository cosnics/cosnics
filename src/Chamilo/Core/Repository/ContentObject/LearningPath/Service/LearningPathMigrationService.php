<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
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
     * The ComplexContentObjectItemsMapping for the given learning path
     *
     * @var array
     */
    protected $complexContentObjectItemsMappingForLearningPath;

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
        ini_set('memory_limit', -1);

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
            $this->complexContentObjectItemsMappingForLearningPath = array();

            $this->migrateLearningPath($learningPath, $learningPath->getId());

            if ($this->hasLearningPathPrerequisites())
            {
                $learningPath->setEnforceDefaultTraversingOrder(true);
                $learningPath->update();

                echo 'Learning Path Prerequisites ' . $learningPath->getId() . PHP_EOL;
            }

            $this->fixLearningPathTracking($learningPath);
        }
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
        while ($complexContentObjectItem = $complexContentObjectItems->next_result())
        {
            /** @var ComplexContentObjectItem $complexContentObjectItem */

            try
            {
                $childContentObject = $this->contentObjectRepository->findById($complexContentObjectItem->get_ref());
            }
            catch (\Exception $ex)
            {
                continue;
            }

            if ($childContentObject instanceof LearningPath)
            {
                /** @var LearningPath $childContentObject */

                $contentObject = $this->getOrCreateSectionForLearningPath($childContentObject);
                $treeNodeData = $this->createTreeNodeDataForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentTreeNodeData
                );

                $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $treeNodeData);
            }
            else
            {
                if (!$childContentObject instanceof LearningPathItem)
                {
                    echo(
                        'The given complex content object item does not reference a learning path item ' .
                        $complexContentObjectItem->getId()
                    );

                    continue;
                }

                /** @var LearningPathItem $childContentObject */

                try
                {
                    $contentObject = $this->contentObjectRepository->findById($childContentObject->get_reference());
                }
                catch (\Exception $ex)
                {
                    continue;
                }

                $treeNodeData = $this->createTreeNodeDataForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentTreeNodeData,
                    $childContentObject
                );
            }

            $this->complexContentObjectItemsMappingForLearningPath[$complexContentObjectItem->getId()] =
                $treeNodeData->getId();
        }
    }

    /**
     * Creates a TreeNodeData for a given LearningPath, ContentObject and parent TreeNodeData
     *
     * @param LearningPath $learningPath
     * @param ComplexContentObjectItem $complexContentObjectItem
     * @param ContentObject $contentObject
     * @param TreeNodeData $parentTreeNodeData
     * @param LearningPathItem $learningPathItem
     *
     * @return TreeNodeData
     *
     * @throws \Exception
     */
    protected function createTreeNodeDataForContentObject(
        LearningPath $learningPath, ComplexContentObjectItem $complexContentObjectItem, ContentObject $contentObject,
        TreeNodeData $parentTreeNodeData = null, LearningPathItem $learningPathItem = null
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

        if (!$this->learningPathTrackingRepository->create($treeNodeData))
        {
            throw new \Exception('Could not create a new learning path child');
        }

        echo "Create TreeNodeData " . $treeNodeData->getId() . PHP_EOL;

        return $treeNodeData;
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

            if (!$section->create())
            {
                throw new \Exception('Could not create a new section');
            }

            echo "Create Section " . $section->getId() . PHP_EOL;

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
    protected function getComplexContentObjectItemsForParent(
        $parentId
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(),
                ComplexContentObjectItem::PROPERTY_PARENT
            ),
            new StaticConditionVariable($parentId)
        );

        return $this->contentObjectRepository->findAll(
            ComplexContentObjectItem::class_name(), new DataClassRetrievesParameters(
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
     * Checks if the learning path has prerequisites, uses the mapping from ComplexContentObjectItem classes
     *
     * @return bool
     */
    protected function hasLearningPathPrerequisites()
    {
        $conditions = array();

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexLearningPathItem::class_name(), ComplexLearningPathItem::PROPERTY_PREREQUISITES
                ),
                null
            )
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ComplexLearningPathItem::class_name(), ComplexLearningPathItem::PROPERTY_ID
            ),
            array_keys($this->complexContentObjectItemsMappingForLearningPath)
        );

        $condition = new AndCondition($conditions);

        return $this->contentObjectRepository->countAll(
                ComplexLearningPathItem::class_name(), new DataClassCountParameters($condition)
            ) > 0;
    }

    /**
     * Change the tracking tables to match the new identifiers from the
     * ComplexContentObject identifiers to the TreeNodeData identifers
     *
     * @param LearningPath $learningPath
     */
    protected function fixLearningPathTracking(LearningPath $learningPath)
    {
        $treeNodeAttempts =
            $this->learningPathTrackingRepository->findTreeNodeAttemptsForLearningPath($learningPath);

        foreach($treeNodeAttempts as $treeNodeAttempt)
        {
            $newLearningPathItemId =
                $this->complexContentObjectItemsMappingForLearningPath[$treeNodeAttempt->get_learning_path_item_id()];

            if(!$newLearningPathItemId)
            {
//                echo 'New learning path item id not found for id ' .
//                    $treeNodeAttempt->get_learning_path_item_id() . PHP_EOL;

                continue;
            }

            $treeNodeAttempt->set_learning_path_item_id($newLearningPathItemId);
            $treeNodeAttempt->update();
        }

        $this->learningPathTrackingRepository->clearTreeNodeAttemptCache();
    }
}

/**
 * RESTORE
 *
DELETE FROM `repository_content_object` WHERE `id` >= 2698805 ORDER BY `id`  ASC;

TRUNCATE repository_tree_node_data;

TRUNCATE tracking_weblcms_tree_node_attempt;

INSERT INTO tracking_weblcms_tree_node_attempt
SELECT * FROM tracking_weblcms_tree_node_attempt_backup;
 *
 */