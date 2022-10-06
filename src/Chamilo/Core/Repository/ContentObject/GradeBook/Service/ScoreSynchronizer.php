<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ScoreSynchronizer
{
    /**
     * @param GradeBookData
     */
    protected $gradeBookData;

    /**
     * @var array
     */
    protected $gradeScores;

    /**
     * @var int[]
     */
    protected $targetUserIds;

    /**
     * @var array
     */
    protected $columnSubItems = [];

    /**
     * @var array
     */
    protected $userColumnUserIds = [];

    /**
     * @var array
     */
    protected $toAddScores = [];

    /**
     * @var array
     */
    protected $toUpdateScores = [];

    /**
     * @var GradeBookScore[]
     */
    protected $toRemoveScores = [];

    /**
     * @param GradeBookData $gradeBookData
     * @param array $gradeScores
     * @param array $targetUserIds
     */
    public function __construct(GradeBookData $gradeBookData, array $gradeScores, array $targetUserIds)
    {
        $this->gradeBookData = $gradeBookData;
        $this->gradeScores = $gradeScores;
        $this->targetUserIds = $targetUserIds;
        $this->init();
        $this->synchronize();
    }

    protected function init()
    {
        foreach ($this->gradeBookData->getGradeBookColumns() as $gradeBookColumn)
        {
            $columnId = $gradeBookColumn->getId();
            $this->userColumnUserIds[$columnId] = array();
            $this->columnSubItems[$columnId] = array();
        }
        $this->initColumnSubItems();
    }

    protected function initColumnSubItems()
    {
        foreach ($this->gradeBookData->getGradeBookItems() as $gradeBookItem)
        {
            $column = $gradeBookItem->getGradeBookColumn();
            if (isset($column))
            {
                $columnId = $column->getId();
                $itemId = $gradeBookItem->getId();
                $this->columnSubItems[$columnId][$itemId] = $gradeBookItem;
            }
        }
    }

    protected function synchronize()
    {
        foreach ($this->gradeBookData->getGradeBookScores() as $score)
        {
            $this->synchronizeExistingScore($score);
        }

        foreach ($this->gradeBookData->getGradeBookColumns() as $gradeBookColumn)
        {
            $this->addScoresForUnsynchronizedUsers($gradeBookColumn);
        }
    }

    /**
     * @param GradeBookScore $score
     */
    protected function synchronizeExistingScore(GradeBookScore $score)
    {
        if ($score->isTotalScore())
        {
            return;
        }
        $userId = $score->getTargetUserId();
        if (!in_array($userId, $this->targetUserIds) || is_null($score->getGradeBookColumn()))
        {
            $this->toRemoveScores[] = $score;
            return;
        }
        $column = $score->getGradeBookColumn();
        $columnId = $column->getId();
        $subItems = $this->columnSubItems[$columnId];
        $this->userColumnUserIds[$columnId][] = $userId;
        if ($column->getType() == 'standalone')
        {
            return;
        }
        list($gradeBookItem, $gradeScore) = $this->getGradeScoreForUser($userId, $subItems);
        if ($this->shouldUpdate($score, $gradeScore))
        {
            $this->toUpdateScores[] = [$score, $gradeBookItem, $gradeScore];
        }
    }

    /**
     * @param GradeBookScore $score
     * @param GradeScoreInterface $gradeScore
     *
     * @return bool
     */
    protected function shouldUpdate(GradeBookScore $score, GradeScoreInterface $gradeScore): bool
    {
        switch (true)
        {
            case $score->isSourceScoreAbsent() && !$gradeScore->isAbsent():
            case !$score->isSourceScoreAbsent() && $gradeScore->isAbsent():
            case $score->isSourceScoreAuthAbsent() && !$gradeScore->isAuthAbsent():
            case !$score->isSourceScoreAuthAbsent() && $gradeScore->isAuthAbsent():
            case is_null($score->getSourceScore()) && $gradeScore->hasValue():
            case !is_null($score->getSourceScore()) && $gradeScore->isNull():
            case !is_null($score->getSourceScore()) && ($score->getSourceScore() != $gradeScore->getValue()):
                return true;
            default:
                return false;
        }
    }

    /**
     * @param GradeBookColumn $gradeBookColumn
     */
    protected function addScoresForUnsynchronizedUsers(GradeBookColumn $gradeBookColumn)
    {
        $columnId = $gradeBookColumn->getId();
        $userScoreUserIds = $this->userColumnUserIds[$columnId];
        $subItems = $this->columnSubItems[$columnId];
        $unsynchronizedUsersIds = array_diff($this->targetUserIds, $userScoreUserIds);

        if (empty($unsynchronizedUsersIds))
        {
            return;
        }

        foreach ($unsynchronizedUsersIds as $userId)
        {
            $this->toAddScores[] = array_merge([$gradeBookColumn, $userId], $this->getGradeScoreForUser($userId, $subItems));
        }
    }

    /**
     * @param int $userId
     * @param GradeBookItem[] $gradeBookItems
     *
     * @return array
     */
    protected function getGradeScoreForUser(int $userId, array $gradeBookItems): array
    {
        $itemId = array_key_first($gradeBookItems);
        $gradeBookItem = $gradeBookItems[$itemId];
        $gradeScore = $this->gradeScores[$itemId][$userId];

        if (count($gradeBookItems) > 1)
        {
            $isFirst = true;
            foreach ($gradeBookItems as $itemId => $nextGradeBookItem)
            {
                if ($isFirst)
                {
                    $isFirst = false;
                    continue;
                }
                $nextGradeScore = $this->gradeScores[$itemId][$userId];
                if ($nextGradeScore->hasPresedenceOver($gradeScore))
                {
                    $gradeBookItem = $nextGradeBookItem;
                    $gradeScore = $nextGradeScore;
                }
            }
        }

        return [$gradeBookItem, $gradeScore];
    }

    /**
     * @return array
     */
    public function getAddScores(): array
    {
        // [][GradeBookColumn, int $userId, GradeBookItem, GradeScoreInterface]
        return $this->toAddScores;
    }

    /**
     * @return array
     */
    public function getUpdateScores(): array
    {
        // [][GradeBookScore, GradeBookItem, GradeScoreInterface]
        return $this->toUpdateScores;
    }

    /**
     * @return GradeBookScore[]
     */
    public function getRemoveScores(): array
    {
        return $this->toRemoveScores;
    }
}