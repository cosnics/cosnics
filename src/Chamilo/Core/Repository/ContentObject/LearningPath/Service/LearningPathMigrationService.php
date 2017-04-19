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
     * @param LearningPathChild $parentLearningPathChild
     */
    protected function migrateLearningPath(
        LearningPath $learningPath, $parentId, LearningPathChild $parentLearningPathChild = null
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
                $learningPathChild = $this->createLearningPathChildForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentLearningPathChild
                );

                $this->migrateLearningPath($learningPath, $complexContentObjectItem->get_ref(), $learningPathChild);
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

                $learningPathChild = $this->createLearningPathChildForContentObject(
                    $learningPath, $complexContentObjectItem, $contentObject, $parentLearningPathChild,
                    $childContentObject
                );
            }

            $this->complexContentObjectItemsMappingForLearningPath[$complexContentObjectItem->getId()] =
                $learningPathChild->getId();
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

        if ($learningPathItem && $learningPathItem instanceof $learningPathItem)
        {
            $learningPathChild->setMaxAttempts((int) $learningPathItem->get_max_attempts());
            $learningPathChild->setMasteryScore((int) $learningPathItem->get_mastery_score());
            $learningPathChild->setAllowHints((bool) $learningPathItem->get_allow_hints());
            $learningPathChild->setShowScore((bool) $learningPathItem->get_show_score());
            $learningPathChild->setShowCorrection((bool) $learningPathItem->get_show_correction());
            $learningPathChild->setShowSolution((bool) $learningPathItem->get_show_solution());
            $learningPathChild->setShowAnswerFeedback((int) $learningPathItem->get_show_answer_feedback());
            $learningPathChild->setFeedbackLocation((int) $learningPathItem->get_feedback_location());
        }

        if (!$this->learningPathTrackingRepository->create($learningPathChild))
        {
            throw new \Exception('Could not create a new learning path child');
        }

        echo "Create LearningPathChild " . $learningPathChild->getId() . PHP_EOL;

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
     * ComplexContentObject identifiers to the LearningPathChild identifers
     *
     * @param LearningPath $learningPath
     */
    protected function fixLearningPathTracking(LearningPath $learningPath)
    {
        $learningPathChildAttempts =
            $this->learningPathTrackingRepository->findLearningPathChildAttemptsForLearningPath($learningPath);

        foreach($learningPathChildAttempts as $learningPathChildAttempt)
        {
            $newLearningPathItemId =
                $this->complexContentObjectItemsMappingForLearningPath[$learningPathChildAttempt->get_learning_path_item_id()];

            if(!$newLearningPathItemId)
            {
                echo 'New learning path item id not found for id ' .
                    $learningPathChildAttempt->get_learning_path_item_id() . PHP_EOL;

                continue;
            }

            $learningPathChildAttempt->set_learning_path_item_id($newLearningPathItemId);
            $learningPathChildAttempt->update();
        }
    }
}