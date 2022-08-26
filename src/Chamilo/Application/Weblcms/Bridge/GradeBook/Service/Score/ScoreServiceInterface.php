<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface ScoreServiceInterface
{
    /**
     * @param ContentObjectPublication $publication
     *
     * @return GradeScoreInterface[]
     */
    public function getScores(ContentObjectPublication $publication): array;
}
