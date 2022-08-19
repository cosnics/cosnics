<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookScoreJSONModel;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @ORM\Table(
 *      name="repository_gradebook_score"
 * )
 */
class GradeBookScore
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var GradeBookData
     *
     * @ORM\ManyToOne(targetEntity="GradeBookData")
     * @ORM\JoinColumn(name="gradebook_data_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $gradebookData;

    /**
     * @var GradeBookColumn
     *
     * @ORM\ManyToOne(targetEntity="GradeBookColumn")
     * @ORM\JoinColumn(name="gradebook_column_id", referencedColumnName="id")
     *
     */
    protected $gradebookColumn;

    /**
     * @var int
     *
     * @ORM\Column(name="target_user_id", type="integer", nullable=true)
     */
    protected $targetUserId;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_score", type="integer", nullable=true)
     */
    protected $sourceScore;

    /**
     * @var bool
     *
     * @ORM\Column(name="source_absent", type="boolean")
     */
    protected $sourceScoreAbsent = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="source_auth_absent", type="boolean")
     */
    protected $sourceScoreAuthAbsent = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="overwritten", type="boolean")
     */
    protected $overwritten = false;


    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return GradeBookScore
     */
    public function setId(int $id): GradeBookScore
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return GradeBookData
     */
    public function getGradeBookData(): ?GradeBookData
    {
        return $this->gradebookData;
    }

    /**
     * @param GradeBookData|null $gradebookData
     *
     * @return GradeBookScore
     */
    public function setGradeBookData(GradeBookData $gradebookData = null): GradeBookScore
    {
        if ($this->gradebookData === $gradebookData)
        {
            return $this;
        }

        $oldGradebookData = $this->gradebookData;
        $this->gradebookData = $gradebookData;

        if ($oldGradebookData instanceof GradeBookData)
        {
            $oldGradebookData->removeGradeBookScore($this);
        }

        if ($gradebookData instanceof GradeBookData)
        {
            $gradebookData->addGradeBookScore($this);
        }

        return $this;
    }

    /**
     * @return GradeBookColumn|null
     */
    public function getGradeBookColumn(): ?GradeBookColumn {
        return $this->gradebookColumn;
    }

    /**
     * @param GradeBookColumn $gradebookColumn
     *
     * @return GradeBookScore
     */
    public function setGradeBookColumn(GradeBookColumn $gradebookColumn): GradeBookScore
    {
        // todo: gradebookColumn == null ?
        // fill in the update mechanism
        $this->gradebookColumn = $gradebookColumn;

        return $this;
    }

    /**
     * @return int
     */
    public function getTargetUserId(): ?int
    {
        return $this->targetUserId;
    }

    /**
     * @param int $targetUserId
     *
     * @return GradeBookScore
     */
    public function setTargetUserId(int $targetUserId): GradeBookScore
    {
        $this->targetUserId = $targetUserId;

        return $this;
    }

    /**
     * @return float
     */
    public function getSourceScore(): ?float
    {
        if (is_null($this->sourceScore))
        {
            return null;
        }
        return $this->sourceScore / 100;
    }

    /**
     * @param float|null $score
     *
     * @return GradeBookScore
     */
    public function setSourceScore(?float $score): GradeBookScore
    {
        if (is_null($score))
        {
            $this->sourceScore = null;
        }
        else
        {
            $this->sourceScore = (int) round ($score * 100);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isSourceScoreAbsent(): bool
    {
        return $this->sourceScoreAbsent;
    }

    /**
     * @param bool $isAbsent
     *
     * @return GradeBookScore
     */
    public function setSourceScoreAbsent(bool $isAbsent): GradeBookScore
    {
        $this->sourceScoreAbsent = $isAbsent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSourceScoreAuthAbsent(): bool
    {
        return $this->sourceScoreAuthAbsent;
    }

    /**
     * @param bool $isAuthAbsent
     *
     * @return GradeBookScore
     */
    public function setSourceScoreAuthAbsent(bool $isAuthAbsent): GradeBookScore
    {
        $this->sourceScoreAuthAbsent = $isAuthAbsent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOverwritten(): bool
    {
        return $this->overwritten;
    }

    /**
     * @param bool $isOverwritten
     *
     * @return GradeBookScore
     */
    public function setOverwritten(bool $isOverwritten): GradeBookScore
    {
        $this->overwritten = $isOverwritten;

        return $this;
    }

    /**
     * @return GradeBookScoreJSONModel
     */
    public function toJSONModel(): GradeBookScoreJSONModel
    {
        return GradeBookScoreJSONModel::fromGradeBookScore($this);
    }
}