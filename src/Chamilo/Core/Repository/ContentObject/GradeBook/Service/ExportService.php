<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ExportService
{
    /**
     * @param GradeBookData $gradeBookData
     * @param GradeBookColumn[] $columns
     *
     * @return string[]
     */
    public function getColumnTitles(GradeBookData $gradeBookData, array $columns): array
    {
        return array_map(function(GradeBookColumn $column) use ($gradeBookData) {
            return $gradeBookData->getGradeBookColumnTitle($column);
        }, $columns);
    }

    /**
     * @param User $user
     * @param GradeBookData $gradeBookData
     * @param GradeBookColumn[] $columns
     * @param array $resultsData
     * @param string $aabsAbbr // authorized absent abbreviation
     *
     * @return string[]
     */
    public function getUserResults(User $user, GradeBookData $gradeBookData, array $columns, array $resultsData, string $aabsAbbr): array
    {
        $safeLastName = StringUtilities::getInstance()->createString($user->get_lastname())->toAscii();
        $safeFirstName = StringUtilities::getInstance()->createString($user->get_firstname())->toAscii();
        $sortName = strtoupper(str_replace(' ', '', $safeLastName . ',' . $safeFirstName));

        $results = [$sortName, $user->get_lastname(), $user->get_firstname(), $user->get_official_code()];
        foreach ($columns as $column)
        {
            $results[] = $this->getResult($resultsData, $column->getId(), $user->getId(), $aabsAbbr);
        }
        $finalScore = $this->getResult($resultsData, GradeBookData::PROPERTY_TOTALS, $user->getId(), $aabsAbbr);
        $results[] = $finalScore;

        if ($gradeBookData->usesDisplayTotal())
        {
            if (is_numeric($finalScore))
            {
                $results[] = round($finalScore * $gradeBookData->getDisplayTotal()) / 100;
            }
            else
            {
                $results[] = '';
            }
        }

        return $results;
    }

    /**
     * @param array $resultsData
     * @param string $columnId
     * @param int $userId
     * @param string $aabsAbbr
     *
     * @return string
     */
    protected function getResult(array $resultsData, string $columnId, int $userId, string $aabsAbbr): string
    {
        if (!array_key_exists($columnId, $resultsData) || !array_key_exists($userId, $resultsData[$columnId]))
        {
            return '';
        }
        /** @var GradeBookScore $score */
        $score = $resultsData[$columnId][$userId];
        if ($score->isOverwritten())
        {
            if ($score->isNewScoreAuthAbsent())
            {
                return $aabsAbbr;
            }
            return $score->getNewScore() ?? '';
        }
        if ($score->isSourceScoreAuthAbsent())
        {
            return $aabsAbbr;
        }
        return $score->getSourceScore() ?? '';
    }
}
