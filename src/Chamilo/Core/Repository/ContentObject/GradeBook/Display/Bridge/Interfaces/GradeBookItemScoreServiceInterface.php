<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Interfaces
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface GradeBookItemScoreServiceInterface
{
    /**
     * @param GradeBookItem $gradeBookItem
     * @param int[] $userIds
     *
     * @return array
     */
    public function getScores(GradeBookItem $gradeBookItem, array $userIds): array;
}