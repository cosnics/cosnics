<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class TotalScoreCalculator
{
    /**
     * @param GradeBookData
     */
    protected $gradeBookData;

    /**
     * @param GradeBookColumn[]
     */
    protected $columns;

    /**
     * @param array
     */
    protected $resultsData;

    /**
     * @param int[]
     */
    protected $userIds;

    /**
     * @param GradeBookData $gradeBookData
     */
    public function __construct(GradeBookData $gradeBookData)
    {
        $this->gradeBookData = $gradeBookData;
        $this->columns = $gradeBookData->getGradeBookColumnsForEndResult();
        $this->initResultsData();
    }

    /**
     * @return GradeBookScore[]
     */
    public function getTotals(): array
    {
        return array_values($this->resultsData['totals']);
    }

    protected function initResultsData()
    {
        $resultsData = ['totals' => []];
        $userIds = [];

        foreach ($this->gradeBookData->getGradeBookScores() as $score)
        {
            $userId = $score->getTargetUserId();
            if ($score->isTotalScore())
            {
                $resultsData['totals'][$userId] = $score;
            }
            else
            {
                $userIds[] = $userId;
                $columnId = $score->getGradeBookColumn()->getId();
                if (!array_key_exists($columnId, $resultsData))
                {
                    $resultsData[$columnId] = [];
                }
                $resultsData[$columnId][$userId] = $score;
            }
        }
        $this->resultsData = $resultsData;
        $this->userIds = $userIds;
    }

    /**
     * @return GradeBookScore[]
     */
    public function calculateTotals(): array
    {
        foreach ($this->userIds as $userId)
        {
            $totalScore = $resultsData['totals'][$userId] = $this->getOrCreateTotalScore($userId);
            $totalScore->setNewScore($this->getEndResult($userId));
        }
        return $this->getTotals();
    }

    /**
     * @param int $userId
     *
     * @return GradeBookScore
     */
    protected function getOrCreateTotalScore(int $userId): GradeBookScore
    {
        $totals = $this->resultsData['totals'];
        if (!array_key_exists($userId, $totals))
        {
            $totalScore = new GradeBookScore();
            $totalScore->setGradeBookData($this->gradeBookData);
            $totalScore->setOverwritten(true);
            $totalScore->setTargetUserId($userId);
            $totalScore->setIsTotalScore(true);
            return $totalScore;
        }
        return $totals[$userId];
    }

    /**
     * @param int $userId
     *
     * @return float
     */
    protected function getEndResult(int $userId): float
    {
        $endResult = 0;
        $maxWeight = 0;
        foreach ($this->columns as $column)
        {
            $columnId = $column->getId();
            $result = $this->getResult($columnId, $userId);
            if (is_null($result))
            {
                $result = 'abs';
            }
            $weight = $this->getWeight($column);
            if (is_numeric($result))
            {
                $maxWeight += $weight;
            }
            elseif ($result === 'authabs')
            {
                if ($column->getAuthPresenceEndResult() != 0)
                {
                    $maxWeight += $weight;
                }
                if ($column->getAuthPresenceEndResult() == 1)
                {
                    $endResult += $weight;
                }
            }
            elseif ($result === 'abs')
            {
                if ($column->getUnauthPresenceEndResult() != 0)
                {
                    $maxWeight += $weight;
                    if ($column->getUnauthPresenceEndResult() == 1)
                    {
                        $endResult += $weight;
                    }
                }
            }
            if (is_numeric($result))
            {
                $endResult += ($result * $weight * 0.01);
            }
        }

        if ($maxWeight == 0)
        {
            return 0;
        }

        return $endResult / $maxWeight * 100;
    }

    /**
     * @param int $columnId
     * @param int $userId
     *
     * @return float|string|null
     */
    protected function getResult(int $columnId, int $userId)
    {
        $resultsData = $this->resultsData;
        if (!array_key_exists($columnId, $resultsData))
        {
            return null;
        }
        if (!array_key_exists($userId, $resultsData[$columnId]))
        {
            return null;
        }
        $score = $resultsData[$columnId][$userId];
        if (!$score instanceof GradeBookScore)
        {
            return null;
        }
        if ($score->isOverwritten())
        {
            if ($score->isNewScoreAuthAbsent())
            {
                return 'authabs';
            }
            return $score->getNewScore();
        }
        if ($score->isSourceScoreAuthAbsent())
        {
            return 'authabs';
        }
        return $score->getSourceScore();
    }

    /**
     * @param GradeBookColumn $column
     *
     * @return float
     */
    protected function getWeight(GradeBookColumn $column): float
    {
        $weight = $column->getWeight();
        if (!is_null($weight))
        {
            return $weight;
        }
        $rest = 100;
        $noRest = 0;
        foreach ($this->columns as $column)
        {
            if (!is_null($column->getWeight()))
            {
                $rest -= $column->getWeight();
            }
            else
            {
                $noRest += 1;
            }
        }
        return $rest / $noRest;
    }
}