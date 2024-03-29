<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCategoryJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookColumnJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookItemScoreServiceInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\NullScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookAjaxService
{
    /**
     * @var GradeBookService
     */
    protected $gradeBookService;

    /**
     * @var GradeBookItemScoreServiceInterface
     */
    protected $gradeBookItemScoreService;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * GradeBookAjaxService constructor.
     *
     * @param GradeBookService $gradeBookService
     * @param GradeBookItemScoreServiceInterface $gradeBookItemScoreService
     */
    public function __construct(GradeBookService $gradeBookService, GradeBookItemScoreServiceInterface $gradeBookItemScoreService)
    {
        $this->gradeBookService = $gradeBookService;
        $this->gradeBookItemScoreService = $gradeBookItemScoreService;
        $this->serializer = $this->createSerializer();
    }

    /**
     * @param GradeBook $gradebook
     * @param ContextIdentifier $contextIdentifier
     *
     * @return GradeBookData
     * @throws ORMException
     */
    public function getGradeBookData(GradeBook $gradebook, ContextIdentifier $contextIdentifier): GradeBookData
    {
        return $this->gradeBookService->getGradeBookDataByContextIdentifier($gradebook, $contextIdentifier, null);
    }

    /**
     * @param GradeBook $gradeBook
     * @param ContextIdentifier $contextIdentifier
     *
     * @return GradeBookData
     * @throws ORMException
     */
    public function getOrCreateGradeBookData(GradeBook $gradeBook, ContextIdentifier $contextIdentifier): GradeBookData
    {
        try
        {
            return $this->getGradeBookData($gradeBook, $contextIdentifier);
        }
        catch (NoResultException $exception)
        {
            $gradeBookData = new GradeBookData($gradeBook->get_title());
            $gradeBookData->setContentObjectId($gradeBook->getId());
            $gradeBookData->setContextIdentifier($contextIdentifier);
            $this->gradeBookService->saveGradeBookData($gradeBookData);
            return $gradeBookData;
        }
    }

    /**
     * @param GradeBookData $gradebookData
     * @param array $publicationItems
     *
     * @throws ORMException
     */
    public function updateGradeBookData(GradeBookData $gradebookData, array $publicationItems)
    {
        $shouldUpdate = $this->gradeBookService->completeGradeBookData($gradebookData, $publicationItems);

        if ($shouldUpdate)
        {
            $this->gradeBookService->saveGradeBookData($gradebookData);
        }
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws ORMException
     */
    public function addCategory(GradeBookData $gradeBookData, string $gradeBookCategoryJSONData)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $category = $gradebookCategoryJSONModel->toGradeBookCategory($gradeBookData);
        $gradeBookData->addGradeBookCategory($category);
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'category' => $gradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function removeCategory(GradeBookData $gradeBookData, string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $category = $gradeBookData->getGradeBookCategoryById($jsonModel->getId());

        foreach ($category->getGradeBookColumns() as $column)
        {
            $gradeBookData->updateGradeBookColumnCategory($column->getId(), null);
        }

        $gradeBookData->removeGradeBookCategory($category);
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws ORMException|ObjectNotExistException
     */
    public function updateCategory(GradeBookData $gradeBookData, string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);

        $category = $gradeBookData->getGradeBookCategoryById($jsonModel->getId());
        $jsonModel->updateGradeBookCategory($category);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'category' => $jsonModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookCategoryJSONData
     * @param int $newSort
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function moveCategory(GradeBookData $gradeBookData, string $gradeBookCategoryJSONData, int $newSort)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);

        $category = $gradeBookData->getGradeBookCategoryById($gradebookCategoryJSONModel->getId());
        $gradeBookData->moveGradeBookCategory($category, $newSort);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'category' => GradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param string $gradeBookCategoryJSONData
     *
     * @return GradeBookCategoryJSONModel
     */
    protected function parseGradeBookCategoryJSONModel(string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->serializer->deserialize(
            $gradeBookCategoryJSONData, GradeBookCategoryJSONModel::class, 'json'
        );

        if (!$jsonModel instanceof GradeBookCategoryJSONModel)
        {
            throw new \RuntimeException('Could not parse the gradebook category JSON model');
        }

        return $jsonModel;
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookColumnJSONData
     * @param int[] $targetUserIds
     *
     * @return array
     *
     * @throws ORMException|ObjectNotExistException
     */
    public function addGradeBookColumn(GradeBookData $gradeBookData, string $gradeBookColumnJSONData, array $targetUserIds)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);
        $column = $jsonModel->toGradeBookColumn($gradeBookData);
        $column->setGradeBookCategory(null);

        if ($column->getType() == GradeBookColumn::TYPE_STANDALONE)
        {
            $gradeBookData->addGradeBookColumn($column);
            foreach ($targetUserIds as $userId)
            {
                $this->addGradeBookScore($column, null, $userId, new NullScore());
            }
            $this->gradeBookService->saveGradeBookData($gradeBookData);

            $scores = array_map(function(GradeBookScore $score) {
                return $score->toJSONModel();
            }, $column->getGradeBookScores()->toArray());

            return [
                'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
                'column' => GradeBookColumnJSONModel::fromGradeBookColumn($column), 'scores' => $scores
            ];
        }

        $gradebookItemId = $jsonModel->getSubItemIds()[0];
        $gradeItem = $gradeBookData->getGradeBookItemById($gradebookItemId);

        $gradeItem->setGradeBookColumn($column);
        $gradeBookData->addGradeBookColumn($column);

        $gradeScores = $this->gradeBookItemScoreService->getScores($gradeItem, $targetUserIds);

        foreach ($gradeScores as $userId => $score)
        {
            $this->addGradeBookScore($column, $gradeItem, $userId, $score);
        }

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $column->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($column), 'scores' => $scores
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     * @param int[] $targetUserIds
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function addGradeBookColumnSubItem(GradeBookData $gradeBookData, int $gradeBookColumnId, int $gradeItemId, array $targetUserIds)
    {
        $gradeItem = $gradeBookData->getGradeBookItemById($gradeItemId);
        $columnToMerge = $gradeItem->getGradeBookColumn();
        $isGradeItemInColumn = $columnToMerge instanceof GradeBookColumn;
        $gradeBookColumn = $gradeBookData->getGradeBookColumnById($gradeBookColumnId);

        if ($isGradeItemInColumn && $columnToMerge->getType() == GradeBookColumn::TYPE_GROUP)
        {
            throw new \RuntimeException('Grade item ' . $gradeItem->getId() . ' already belongs to a group');
        }

        $gradeBookColumn->setType(GradeBookColumn::TYPE_GROUP);
        $gradeItem->setGradeBookColumn($gradeBookColumn);

        if ($isGradeItemInColumn)
        {
            $this->mergeColumnScores($gradeBookColumn, $columnToMerge, $targetUserIds);
            $gradeBookData->removeGradeBookColumn($columnToMerge);
        }
        else
        {
            $this->mergeColumnAndItemScores($gradeBookColumn, $gradeItem, $targetUserIds);
        }

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookColumn->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($gradeBookColumn), 'scores' => $scores
        ];
    }

    /**
     * @param GradeBookColumn $gradeBookColumn
     * @param GradeBookColumn $columnToMerge
     * @param array $targetUserIds
     */
    protected function mergeColumnScores(GradeBookColumn $gradeBookColumn, GradeBookColumn $columnToMerge, array $targetUserIds)
    {
        $gradeBookScores = $gradeBookColumn->getGradeBookScores();
        $scoresToMerge = $columnToMerge->getGradeBookScores();

        $userScores = array();

        foreach ($gradeBookScores as $score)
        {
            $userId = $score->getTargetUserId();
            $userScores[$userId] = ['dest' => $score];
        }

        foreach ($targetUserIds as $userId)
        {
            if (!array_key_exists($userId, $userScores))
            {
                $score = $this->addGradeBookScore($gradeBookColumn, null, $userId, new NullScore());
                $userScores[$userId] = ['dest' => $score];
            }
        }

        foreach ($scoresToMerge as $score)
        {
            $userId = $score->getTargetUserId();
            if (array_key_exists($userId, $userScores))
            {
                $userScores[$userId]['from'] = $score;
            }
        }

        foreach ($targetUserIds as $userId)
        {
            $scores = $userScores[$userId];
            /** @var GradeBookScore|null $fromScore */
            $fromScore = $scores['from'];
            /** @var GradeBookScore $destScore */
            $destScore = $scores['dest'];

            if (!is_null($fromScore) && $fromScore->toGradeScore()->hasPresedenceOver($destScore->toGradeScore()))
            {
                $destScore->setSourceScore($fromScore->getSourceScore());
                $destScore->setSourceScoreAuthAbsent($fromScore->isSourceScoreAuthAbsent());
                $destScore->setGradeBookItem($fromScore->getGradeBookItem());
            }
        }
    }

    /**
     * @param GradeBookColumn $gradeBookColumn
     * @param GradeBookItem $gradeBookItem
     * @param array $targetUserIds
     */
    protected function mergeColumnAndItemScores(GradeBookColumn $gradeBookColumn, GradeBookItem $gradeBookItem, array $targetUserIds)
    {
        $gradeBookScores = $gradeBookColumn->getGradeBookScores();
        $gradeScores = $this->gradeBookItemScoreService->getScores($gradeBookItem, $targetUserIds);

        $userScores = array();

        foreach ($gradeBookScores as $score)
        {
            $userId = $score->getTargetUserId();
            $userScores[$userId] = $score;
        }

        foreach ($targetUserIds as $userId)
        {
            if (!array_key_exists($userId, $userScores))
            {
                $userScores[$userId] = $this->addGradeBookScore($gradeBookColumn, null, $userId, new NullScore());
            }
            $destScore = $userScores[$userId];
            $fromGradeScore = $gradeScores[$userId];

            if (!is_null($fromGradeScore) && $fromGradeScore->hasPresedenceOver($destScore->toGradeScore()))
            {
                $destScore->setSourceScoreAuthAbsent($fromGradeScore->isAuthAbsent());
                $destScore->setSourceScore($fromGradeScore->hasValue() ? $fromGradeScore->getValue() : null);
                $destScore->setGradeBookItem($gradeBookItem);
            }
        }
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function removeGradeBookColumnSubItem(GradeBookData $gradeBookData, int $gradeBookColumnId, int $gradeItemId)
    {
        $gradeItem = $gradeBookData->getGradeBookItemById($gradeItemId);
        $gradeItemColumn = $gradeItem->getGradeBookColumn();
        $gradeBookColumn = $gradeBookData->getGradeBookColumnById($gradeBookColumnId);

        if ($gradeItemColumn !== $gradeBookColumn)
        {
            throw new \RuntimeException('Grade item ' . $gradeItem->getId() . ' is not a subitem of column ' . $gradeBookColumn->getId());
        }

        $gradeBookScores = $gradeBookColumn->getGradeBookScores();

        foreach ($gradeBookScores as $score)
        {
            if ($score->getGradeBookItem() === $gradeItem)
            {
                $score->setSourceScore(null);
                $score->setSourceScoreAuthAbsent(false);
                $score->setGradeBookItem(null);
            }
        }
        // possible todo: this doesn't restore to a possible previously "lower" score

        $gradeItem->setGradeBookColumn(null);
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookColumn->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($gradeBookColumn), 'scores' => $scores
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param string $gradeBookColumnJSONData
     *
     * @return array
     *
     * @throws ORMException|ObjectNotExistException
     */
    public function updateGradeBookColumn(GradeBookData $gradeBookData, string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);

        $column = $gradeBookData->getGradeBookColumnById($jsonModel->getId());
        $jsonModel->updateGradeBookColumn($column);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'gradebookColumn' => GradeBookColumnJSONModel::fromGradeBookColumn($column)
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param array $targetUserIds
     *
     * @return array
     * @throws ORMException
     */
    public function synchronizeGradeBook(GradeBookData $gradeBookData, array $targetUserIds)
    {
        $gradeScores = $this->getGradeScores($gradeBookData, $targetUserIds);
        $scoreSynchronizer = new ScoreSynchronizer($gradeBookData, $gradeScores, $targetUserIds);

        foreach ($scoreSynchronizer->getRemoveScores() as $score)
        {
            $gradeBookData->removeGradeBookScore($score);
        }

        foreach ($scoreSynchronizer->getUpdateScores() as list($gradeBookScore, $gradeBookItem, $gradeScore))
        {
            $this->updateGradeBookScore($gradeBookScore, $gradeBookItem, $gradeScore);
        }

        foreach ($scoreSynchronizer->getAddScores() as list($gradeBookColumn, $userId, $gradeBookItem, $gradeScore))
        {
            if ($gradeBookColumn->getType() == GradeBookColumn::TYPE_STANDALONE)
            {
                $gradeScore = new NullScore();
            }
            $this->addGradeBookScore($gradeBookColumn, $gradeBookItem, $userId, $gradeScore);
        }
        $totalScoreCalculator = new TotalScoreCalculator($gradeBookData);
        $totalScoreCalculator->calculateTotals();

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookData->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'scores' => $scores
        ];
    }

    /**
     * @param string $gradeBookColumnJSONData
     *
     * @return GradeBookColumnJSONModel
     */
    protected function parseGradeBookColumnJSONModel(string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->serializer->deserialize(
            $gradeBookColumnJSONData, GradeBookColumnJSONModel::class, 'json'
        );

        if (!$jsonModel instanceof GradeBookColumnJSONModel)
        {
            throw new \RuntimeException('Could not parse the gradebook column JSON model');
        }

        return $jsonModel;
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookColumnId
     * @param int|null $categoryId
     *
     * @return array
     *
     * @throws ObjectNotExistException|ORMException
     */
    public function updateGradeBookColumnCategory(GradeBookData $gradeBookData, int $gradeBookColumnId, ?int $categoryId)
    {
        $column = $gradeBookData->updateGradeBookColumnCategory($gradeBookColumnId, $categoryId);
        $newCategory = $column->getGradeBookCategory();
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'gradebookColumnId' => $column->getId(),
            'categoryId' => empty($newCategory) ? null : $newCategory->getId(),
            'sort' => $column->getSort()
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookColumnId
     * @param int $newSort
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function moveGradeBookColumn(GradeBookData $gradeBookData, int $gradeBookColumnId, int $newSort)
    {
        $gradeBookColumn = $gradeBookData->getGradeBookColumnById($gradeBookColumnId);
        $gradeBookData->moveGradeBookColumn($gradeBookColumn, $newSort);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'gradebookColumnId' => $gradeBookColumn->getId(),
            'sort' => $gradeBookColumn->getSort()
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookColumnId
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function removeGradeBookColumn(GradeBookData $gradeBookData, int $gradeBookColumnId)
    {
        $gradeBookColumn = $gradeBookData->getGradeBookColumnById($gradeBookColumnId);
        $gradeBookData->removeGradeBookColumn($gradeBookColumn);
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()]
        ];
    }

    /**
     * @param GradeBookColumn $column
     * @param GradeBookItem|null $gradeBookItem
     * @param int $userId
     * @param GradeScoreInterface $score
     *
     * @return GradeBookScore
     */
    protected function addGradeBookScore(GradeBookColumn $column, ?GradeBookItem $gradeBookItem, int $userId, GradeScoreInterface $score): GradeBookScore
    {
        $gradeBookScore = new GradeBookScore();
        $gradeBookScore->setGradeBookData($column->getGradeBookData());
        $gradeBookScore->setGradeBookColumn($column);
        $gradeBookScore->setGradeBookItem($gradeBookItem);
        $gradeBookScore->setOverwritten(false);
        $gradeBookScore->setTargetUserId($userId);
        $gradeBookScore->setSourceScoreAuthAbsent($score->isAuthAbsent());
        $gradeBookScore->setIsTotalScore(false);
        $gradeBookScore->setComment(null);
        if (!$score->isAuthAbsent())
        {
            $gradeBookScore->setSourceScore($score->getValue());
        }
        return $gradeBookScore;
    }

    /**
     * @param GradeBookScore $gradeBookScore
     * @param GradeBookItem|null $gradeBookItem
     * @param GradeScoreInterface $gradeScore
     *
     * @return GradeBookScore
     */
    protected function updateGradeBookScore(GradeBookScore $gradeBookScore, ?GradeBookItem $gradeBookItem, GradeScoreInterface $gradeScore): GradeBookScore
    {
        $gradeBookScore->setSourceScoreAuthAbsent($gradeScore->isAuthAbsent());
        $gradeBookScore->setSourceScore($gradeScore->hasValue() ? $gradeScore->getValue() : null);
        $gradeBookScore->setGradeBookItem($gradeBookItem);
        return $gradeBookScore;
    }

    /**
     * @return Serializer
     */
    private function createSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->setDeserializationContextFactory(function () {
                return DeserializationContext::create();
            })
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();
    }

    /**
     * @param GradeBookData $gradebookData
     * @param array $targetUserIds
     * @return array
     */
    protected function getGradeScores(GradeBookData $gradebookData, array $targetUserIds): array
    {
        $gradeScores = array();
        foreach ($gradebookData->getGradeBookItems() as $gradeBookItem)
        {
            if ($gradeBookItem->isRemoved())
            {
                continue;
            }
            $gradeScores[$gradeBookItem->getId()] = $this->gradeBookItemScoreService->getScores($gradeBookItem, $targetUserIds);
        }
        return $gradeScores;
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookScoreId
     * @param float|null $newScore
     * @param bool $isNewScoreAuthAbsent
     *
     * @return array[]
     * @throws ORMException|ObjectNotExistException
     */
    public function overwriteGradeBookScore(GradeBookData $gradeBookData, int $gradeBookScoreId, ?float $newScore, bool $isNewScoreAuthAbsent): array
    {
        $gradebookScore = $gradeBookData->getGradeBookScoreById($gradeBookScoreId);

        $gradebookScore->setOverwritten(true);
        $gradebookScore->setNewScore($isNewScoreAuthAbsent ? null : $newScore);
        $gradebookScore->setNewScoreAuthAbsent($isNewScoreAuthAbsent);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookScoreId
     * @param string|null $comment
     *
     * @return array
     *
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function updateGradeBookScoreComment(GradeBookData $gradeBookData, int $gradeBookScoreId, ?string $comment): array
    {
        $gradebookScore = $gradeBookData->getGradeBookScoreById($gradeBookScoreId);
        $gradebookScore->setComment($comment);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int $gradeBookScoreId
     *
     * @return array
     * @throws ObjectNotExistException
     * @throws ORMException
     */
    public function revertOverwrittenGradeBookScore(GradeBookData $gradeBookData, int $gradeBookScoreId): array
    {
        $gradebookScore = $gradeBookData->getGradeBookScoreById($gradeBookScoreId);

        $gradebookScore->setOverwritten(false);
        $gradebookScore->setNewScore(null);
        $gradebookScore->setNewScoreAuthAbsent(false);

        $this->gradeBookService->saveGradeBookData($gradeBookData);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     *
     * @return array
     * @throws ORMException
     */
    public function calculateTotalScores(GradeBookData $gradeBookData): array
    {
        $totalScoreCalculator = new TotalScoreCalculator($gradeBookData);
        $totals = $totalScoreCalculator->calculateTotals();
        $this->gradeBookService->saveGradeBookData($gradeBookData);

        $totalScores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $totals);

        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()],
            'totalScores' => $totalScores
        ];
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param int|null $displayTotal
     *
     * @return array
     * @throws ORMException
     */
    public function updateDisplayTotal(GradeBookData $gradeBookData, ?int $displayTotal): array
    {
        $gradeBookData->setDisplayTotal($displayTotal);
        $this->gradeBookService->saveGradeBookData($gradeBookData);
        return [
            'gradebook' => ['dataId' => $gradeBookData->getId(), 'version' => $gradeBookData->getVersion()]
        ];
    }
}
