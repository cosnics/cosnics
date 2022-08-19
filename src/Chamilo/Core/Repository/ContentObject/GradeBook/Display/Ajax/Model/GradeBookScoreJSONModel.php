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
     * @var int
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
    protected $sourceScoreAbsent;

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
     * @param int $id
     * @param int $columnId
     * @param int $targetUserId
     * @param float|null $sourceScore
     * @param bool $sourceScoreAbsent
     * @param bool $sourceScoreAuthAbsent
     * @param bool $overwritten
     */
    public function __construct(int $id, int $columnId, int $targetUserId, ?float $sourceScore, bool $sourceScoreAbsent, bool $sourceScoreAuthAbsent, bool $overwritten)
    {
        $this->id = $id;
        $this->columnId = $columnId;
        $this->targetUserId = $targetUserId;
        $this->sourceScore = $sourceScore;
        $this->sourceScoreAbsent = $sourceScoreAbsent;
        $this->sourceScoreAuthAbsent = $sourceScoreAuthAbsent;
        $this->overwritten = $overwritten;
    }

    /**
     * @param GradeBookScore $gradeBookScore
     *
     * @return GradeBookScoreJSONModel
     */
    public static function fromGradeBookScore(GradeBookScore $gradeBookScore): GradeBookScoreJSONModel
    {
        return new self($gradeBookScore->getId(), $gradeBookScore->getGradeBookColumn()->getId(), $gradeBookScore->getTargetUserId(), $gradeBookScore->getSourceScore(), $gradeBookScore->isSourceScoreAbsent(), $gradeBookScore->isSourceScoreAuthAbsent(), $gradeBookScore->isOverwritten());
    }
}