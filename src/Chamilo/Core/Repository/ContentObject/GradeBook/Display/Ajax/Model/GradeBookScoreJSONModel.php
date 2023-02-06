<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookScoreJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var int|null
     *
     * @Type("integer")
     */
    protected $columnId;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $targetUserId;

    /**
     * @var float|null
     *
     * @Type("double")
     */
    protected $sourceScore;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $sourceScoreAuthAbsent;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $overwritten;

    /**
     * @var float|null
     *
     * @Type("double")
     */
    protected $newScore;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $newScoreAuthAbsent;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $isTotal;

    /**
     * @var string|null
     *
     * @Type("string")
     */
    protected $comment;

    /**
     * @param int $id
     * @param int|null $columnId
     * @param int $targetUserId
     * @param float|null $sourceScore
     * @param bool $sourceScoreAuthAbsent
     * @param bool $overwritten
     * @param float|null $newScore
     * @param bool $newScoreAuthAbsent
     * @param bool $isTotal
     * @param string|null $comment
     */
    public function __construct(int $id, ?int $columnId, int $targetUserId, ?float $sourceScore, bool $sourceScoreAuthAbsent, bool $overwritten, ?float $newScore, bool $newScoreAuthAbsent, bool $isTotal, ?string $comment)
    {
        $this->id = $id;
        $this->columnId = $columnId;
        $this->targetUserId = $targetUserId;
        $this->sourceScore = $sourceScore;
        $this->sourceScoreAuthAbsent = $sourceScoreAuthAbsent;
        $this->overwritten = $overwritten;
        $this->newScore = $newScore;
        $this->newScoreAuthAbsent = $newScoreAuthAbsent;
        $this->isTotal = $isTotal;
        $this->comment = $comment;
    }

    /**
     * @param GradeBookScore $gradeBookScore
     *
     * @return GradeBookScoreJSONModel
     */
    public static function fromGradeBookScore(GradeBookScore $gradeBookScore): GradeBookScoreJSONModel
    {
        $column = $gradeBookScore->getGradeBookColumn();
        $columnId = empty($column) ? null : $column->getId();
        return new self($gradeBookScore->getId(), $columnId, $gradeBookScore->getTargetUserId(), $gradeBookScore->getSourceScore(), $gradeBookScore->isSourceScoreAuthAbsent(), $gradeBookScore->isOverwritten(), $gradeBookScore->getNewScore(), $gradeBookScore->isNewScoreAuthAbsent(), $gradeBookScore->isTotalScore(), $gradeBookScore->getComment());
    }
}