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
     *
     * @return GradeBookData
     * @throws \Doctrine\ORM\ORMException
     */
    public function getGradeBookData(GradeBook $gradebook)
    {
        return $this->gradeBookService->getGradeBook($gradebook->getActiveGradeBookDataId(), null);
    }

    /**
     * @param GradeBookData $gradebookData
     * @param GradeBookItem[] $publicationItems
     *
     */
    public function updateGradeBookData(GradeBookData $gradebookData, array $publicationItems)
    {
        $shouldUpdate = false;
        $gradebookItems = array();
        foreach ($gradebookData->getGradeBookItems() as $gradebookItem)
        {
            $hash = $this->getContextHash($gradebookItem->getContextIdentifier());
            $gradebookItems[$hash] = $gradebookItem;
        }

        foreach ($publicationItems as $publicationItem)
        {
            $hash = $this->getContextHash($publicationItem->getContextIdentifier());
            $gradebookItem = $gradebookItems[$hash];
            if (!empty($gradebookItem))
            {
                $gradebookItem->setType($publicationItem->getType());
                $gradebookItem->setTitle($publicationItem->getTitle());
                $gradebookItem->setBreadcrumb($publicationItem->getBreadcrumb());
                unset($gradebookItems[$hash]);
            }
            else
            {
                // add new items to the gradebook.
                $gradebookItem = $publicationItem;
                $gradebookItem->setGradeBookData($gradebookData);
                $shouldUpdate = true;
            }
        }
        foreach ($gradebookItems as $key => $gradebookItem)
        {
            // $gradebookItems still in the array refer to items that have been removed
            // todo: update the corresponding data structure of the gradebook.
        }

        if ($shouldUpdate)
        {
            $this->gradeBookService->saveGradeBook($gradebookData);
        }
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     *
     * @return string
     */
    public function getContextHash(ContextIdentifier $contextIdentifier): string
    {
        return md5($contextIdentifier->getContextClass() . ':' . $contextIdentifier->getContextId());
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $category = $gradebookCategoryJSONModel->toGradeBookCategory($gradebookData);
        $gradebookData->addGradeBookCategory($category);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $gradebookCategoryJSONModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $category = $gradebookData->getGradeBookCategoryById($jsonModel->getId());

        foreach ($category->getGradeBookColumns() as $column)
        {
            $gradebookData->updateGradeBookColumnCategory($column->getId(), null);
        }

        $gradebookData->removeGradeBookCategory($category);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData)
    {
        $jsonModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $category = $gradebookData->getGradeBookCategoryById($jsonModel->getId());
        $jsonModel->updateGradeBookCategory($category);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'category' => $jsonModel::fromGradeBookCategory($category)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookCategoryJSONData
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveCategory(int $gradeBookDataId, int $versionId, string $gradeBookCategoryJSONData, int $newSort)
    {
        $gradebookCategoryJSONModel = $this->parseGradeBookCategoryJSONModel($gradeBookCategoryJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $category = $gradebookData->getGradeBookCategoryById($gradebookCategoryJSONModel->getId());
        $gradebookData->moveGradeBookCategory($category, $newSort);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
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
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookColumnJSONData
     * @param int[] $targetUserIds
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addGradeBookColumn(int $gradeBookDataId, int $versionId, string $gradeBookColumnJSONData, array $targetUserIds)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $column = $jsonModel->toGradeBookColumn($gradebookData);
        $column->setGradeBookCategory(null);

        if ($column->getType() == 'standalone')
        {
            $gradebookData->addGradeBookColumn($column);
            foreach ($targetUserIds as $userId)
            {
                $this->addGradeBookScore($column, null, $userId, new NullScore());
            }
            $this->gradeBookService->saveGradeBook($gradebookData);

            $scores = array_map(function(GradeBookScore $score) {
                return $score->toJSONModel();
            }, $column->getGradeBookScores()->toArray());

            return [
                'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
                'column' => GradeBookColumnJSONModel::fromGradeBookColumn($column), 'scores' => $scores
            ];
        }

        $gradebookItemId = $jsonModel->getSubItemIds()[0];
        $gradeItem = $gradebookData->getGradeBookItemById($gradebookItemId);

        $gradeItem->setGradeBookColumn($column);
        $gradebookData->addGradeBookColumn($column);

        $gradeScores = $this->gradeBookItemScoreService->getScores($gradeItem, $targetUserIds);

        foreach ($gradeScores as $userId => $score)
        {
            $this->addGradeBookScore($column, $gradeItem, $userId, $score);
        }

        $this->gradeBookService->saveGradeBook($gradebookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $column->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($column), 'scores' => $scores
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     * @param int[] $targetUserIds
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function addGradeBookColumnSubItem(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $gradeItemId, array $targetUserIds)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeItem = $gradebookData->getGradeBookItemById($gradeItemId);
        $columnToMerge = $gradeItem->getGradeBookColumn();
        $isGradeItemInColumn = $columnToMerge instanceof GradeBookColumn;
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);

        if ($isGradeItemInColumn && $columnToMerge->getType() == 'group')
        {
            throw new \RuntimeException('Grade item ' . $gradeItem->getId() . ' already belongs to a group');
        }

        $gradeBookColumn->setType('group');
        $gradeItem->setGradeBookColumn($gradeBookColumn);

        if ($isGradeItemInColumn)
        {
            $this->mergeColumnScores($gradeBookColumn, $columnToMerge, $targetUserIds);
            $gradebookData->removeGradeBookColumn($columnToMerge);
        }
        else
        {
            $this->mergeColumnAndItemScores($gradeBookColumn, $gradeItem, $targetUserIds);
        }

        $this->gradeBookService->saveGradeBook($gradebookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookColumn->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
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
                $destScore->setSourceScoreAbsent($fromScore->isSourceScoreAbsent());
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
                $destScore->setSourceScoreAbsent($fromGradeScore->isAbsent());
                $destScore->setSourceScore($fromGradeScore->hasValue() ? $fromGradeScore->getValue() : null);
                $destScore->setGradeBookItem($gradeBookItem);
            }
        }
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $gradeItemId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeGradeBookColumnSubItem(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $gradeItemId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeItem = $gradebookData->getGradeBookItemById($gradeItemId);
        $gradeItemColumn = $gradeItem->getGradeBookColumn();
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);

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
                $score->setSourceScoreAbsent(false);
                $score->setSourceScoreAuthAbsent(false);
                $score->setGradeBookItem(null);
            }
        }
        // possible todo: this doesn't restore to a possible previously "lower" score

        $gradeItem->setGradeBookColumn(null);
        $this->gradeBookService->saveGradeBook($gradebookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookColumn->getGradeBookScores()->toArray());

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'column' => GradeBookColumnJSONModel::fromGradeBookColumn($gradeBookColumn), 'scores' => $scores
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param string $gradeBookColumnJSONData
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateGradeBookColumn(int $gradeBookDataId, int $versionId, string $gradeBookColumnJSONData)
    {
        $jsonModel = $this->parseGradeBookColumnJSONModel($gradeBookColumnJSONData);
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $column = $gradebookData->getGradeBookColumnById($jsonModel->getId());
        $jsonModel->updateGradeBookColumn($column);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumn' => GradeBookColumnJSONModel::fromGradeBookColumn($column)
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param array $targetUserIds
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function synchronizeGradeBook(int $gradeBookDataId, int $versionId, array $targetUserIds)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeScores = $this->getGradeScores($gradebookData, $targetUserIds);
        $scoreSynchronizer = new ScoreSynchronizer($gradebookData, $gradeScores, $targetUserIds);

        foreach ($scoreSynchronizer->getRemoveScores() as $score)
        {
            $gradebookData->removeGradeBookScore($score);
        }

        foreach ($scoreSynchronizer->getUpdateScores() as list($gradeBookScore, $gradeBookItem, $gradeScore))
        {
            $this->updateGradeBookScore($gradeBookScore, $gradeBookItem, $gradeScore);
        }

        foreach ($scoreSynchronizer->getAddScores() as list($gradeBookColumn, $userId, $gradeBookItem, $gradeScore))
        {
            if ($gradeBookColumn->getType() == 'standalone')
            {
                $gradeScore = new NullScore();
            }
            $this->addGradeBookScore($gradeBookColumn, $gradeBookItem, $userId, $gradeScore);
        }

        $this->gradeBookService->saveGradeBook($gradebookData);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradebookData->getGradeBookScores()->toArray());
        $scores = array_values($scores);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
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
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int|null $categoryId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function updateGradeBookColumnCategory(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, ?int $categoryId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $column = $gradebookData->updateGradeBookColumnCategory($gradeBookColumnId, $categoryId);
        $newCategory = $column->getGradeBookCategory();
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumnId' => $column->getId(),
            'categoryId' => empty($newCategory) ? null : $newCategory->getId(),
            'sort' => $column->getSort()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     * @param int $newSort
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveGradeBookColumn(int $gradeBookDataId, int $versionId, int $gradeBookColumnId, int $newSort)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);

        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);
        $gradebookData->moveGradeBookColumn($gradeBookColumn, $newSort);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'gradebookColumnId' => $gradeBookColumn->getId(),
            'sort' => $gradeBookColumn->getSort()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookColumnId
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeGradeBookColumn(int $gradeBookDataId, int $versionId, int $gradeBookColumnId)
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradeBookColumn = $gradebookData->getGradeBookColumnById($gradeBookColumnId);
        $gradebookData->removeGradeBookColumn($gradeBookColumn);
        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()]
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
        $gradebookScore = new GradeBookScore();
        $gradebookScore->setGradeBookData($column->getGradeBookData());
        $gradebookScore->setGradeBookColumn($column);
        $gradebookScore->setGradeBookItem($gradeBookItem);
        $gradebookScore->setOverwritten(false);
        $gradebookScore->setTargetUserId($userId);
        $gradebookScore->setSourceScoreAuthAbsent($score->isAuthAbsent());
        $gradebookScore->setSourceScoreAbsent($score->isAbsent());
        $gradebookScore->setIsTotalScore(false);
        $gradebookScore->setComment(null);
        if (!($score->isAbsent() || $score->isAuthAbsent()))
        {
            $gradebookScore->setSourceScore($score->getValue());
        }
        return $gradebookScore;
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
        $gradeBookScore->setSourceScoreAbsent($gradeScore->isAbsent());
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
            $gradeScores[$gradeBookItem->getId()] = $this->gradeBookItemScoreService->getScores($gradeBookItem, $targetUserIds);
        }
        return $gradeScores;
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookScoreId
     * @param float|null $newScore
     * @param bool $isNewScoreAbsent
     * @param bool $isNewScoreAuthAbsent
     *
     * @return array[]
     * @throws \Doctrine\ORM\ORMException
     */
    public function overwriteGradeBookScore(int $gradeBookDataId, int $versionId, int $gradeBookScoreId, ?float $newScore, bool $isNewScoreAbsent, bool $isNewScoreAuthAbsent): array
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradebookScore = $gradebookData->getGradeBookScoreById($gradeBookScoreId);

        $gradebookScore->setOverwritten(true);
        $gradebookScore->setNewScore(($isNewScoreAbsent || $isNewScoreAuthAbsent) ? null : $newScore);
        $gradebookScore->setNewScoreAbsent($isNewScoreAbsent);
        $gradebookScore->setNewScoreAuthAbsent($isNewScoreAuthAbsent && !$isNewScoreAbsent);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookScoreId
     * @param string|null $comment
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateGradeBookScoreComment(int $gradeBookDataId, int $versionId, int $gradeBookScoreId, ?string $comment): array
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradebookScore = $gradebookData->getGradeBookScoreById($gradeBookScoreId);
        $gradebookScore->setComment($comment);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     * @param int $gradeBookScoreId
     *
     * @return array
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    public function revertOverwrittenGradeBookScore(int $gradeBookDataId, int $versionId, int $gradeBookScoreId): array
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $gradebookScore = $gradebookData->getGradeBookScoreById($gradeBookScoreId);

        $gradebookScore->setOverwritten(false);
        $gradebookScore->setNewScore(null);
        $gradebookScore->setNewScoreAbsent(false);
        $gradebookScore->setNewScoreAuthAbsent(false);

        $this->gradeBookService->saveGradeBook($gradebookData);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'score' => $gradebookScore->toJSONModel()
        ];
    }

    /**
     * @param int $gradeBookDataId
     * @param int $versionId
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function calculateTotalScores(int $gradeBookDataId, int $versionId): array
    {
        $gradebookData = $this->gradeBookService->getGradeBook($gradeBookDataId, $versionId);
        $totalScoreCalculator = new TotalScoreCalculator($gradebookData);
        $totals = $totalScoreCalculator->calculateTotals();
        $this->gradeBookService->saveGradeBook($gradebookData);

        $totalScores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $totals);

        return [
            'gradebook' => ['dataId' => $gradebookData->getId(), 'version' => $gradebookData->getVersion()],
            'totalScores' => $totalScores
        ];
    }
}
