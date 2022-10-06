<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookScoreJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\AbsentScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\AuthAbsentScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\NullScore;
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
     * @ORM\JoinColumn(name="gradebook_column_id", referencedColumnName="id", nullable=true)
     *
     */
    protected $gradebookColumn;

    /**
     * @var GradeBookItem
     *
     * @ORM\ManyToOne(targetEntity="GradeBookItem")
     * @ORM\JoinColumn(name="gradebook_item_id", referencedColumnName="id", nullable=true)
     *
     * @Exclude
     */
    protected $gradebookItem;

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
     * @var integer
     *
     * @ORM\Column(name="new_score", type="integer", nullable=true)
     */
    protected $newScore;

    /**
     * @var bool
     *
     * @ORM\Column(name="new_absent", type="boolean")
     */
    protected $newScoreAbsent = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="new_auth_absent", type="boolean")
     */
    protected $newScoreAuthAbsent = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_total", type="boolean")
     */
    protected $isTotal = false;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    protected $comment;

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
     * @param GradeBookColumn|null $gradebookColumn
     *
     * @return GradeBookScore
     */
    public function setGradeBookColumn(GradeBookColumn $gradebookColumn = null): GradeBookScore
    {
        if ($this->gradebookColumn === $gradebookColumn)
        {
            return $this;
        }

        $oldGradebookColumn = $this->gradebookColumn;
        $this->gradebookColumn = $gradebookColumn;

        if ($oldGradebookColumn instanceof GradeBookColumn)
        {
            $oldGradebookColumn->removeGradeBookScore($this);
        }

        if ($gradebookColumn instanceof GradeBookColumn)
        {
            $gradebookColumn->addGradeBookScore($this);
        }

        return $this;
    }

    /**
     * @return GradeBookItem|null
     */
    public function getGradeBookItem(): ?GradeBookItem {
        return $this->gradebookItem;
    }

    /**
     * @param GradeBookItem|null $gradebookItem
     *
     * @return GradeBookScore
     */
    public function setGradeBookItem(GradeBookItem $gradebookItem = null): GradeBookScore
    {
        if ($this->gradebookItem === $gradebookItem) {
            return $this;
        }

        $this->gradebookItem = $gradebookItem;

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
     * @return float|null
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
     * @return float|null
     */
    public function getNewScore(): ?float
    {
        if (is_null($this->newScore))
        {
            return null;
        }
        return $this->newScore / 100;
    }

    /**
     * @param float|null $score
     *
     * @return GradeBookScore
     */
    public function setNewScore(?float $score): GradeBookScore
    {
        if (is_null($score))
        {
            $this->newScore = null;
        }
        else
        {
            $this->newScore = (int) round ($score * 100);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isNewScoreAbsent(): bool
    {
        return $this->newScoreAbsent;
    }

    /**
     * @param bool $isAbsent
     *
     * @return GradeBookScore
     */
    public function setNewScoreAbsent(bool $isAbsent): GradeBookScore
    {
        $this->newScoreAbsent = $isAbsent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNewScoreAuthAbsent(): bool
    {
        return $this->newScoreAuthAbsent;
    }

    /**
     * @param bool $isAuthAbsent
     *
     * @return GradeBookScore
     */
    public function setNewScoreAuthAbsent(bool $isAuthAbsent): GradeBookScore
    {
        $this->newScoreAuthAbsent = $isAuthAbsent;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTotalScore(): bool
    {
        return $this->isTotal;
    }

    /**
     * @param bool $isTotal
     *
     * @return GradeBookScore
     */
    public function setIsTotalScore(bool $isTotal): GradeBookScore
    {
        $this->isTotal = $isTotal;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return GradeBookScore
     */
    public function setComment(?string $comment): GradeBookScore
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return GradeBookScoreJSONModel
     */
    public function toJSONModel(): GradeBookScoreJSONModel
    {
        return GradeBookScoreJSONModel::fromGradeBookScore($this);
    }

    /**
     * @return GradeScoreInterface
     */
    public function toGradeScore(): GradeScoreInterface
    {
        if ($this->sourceScoreAbsent)
        {
            return new AbsentScore();
        }
        if ($this->sourceScoreAuthAbsent)
        {
            return new AuthAbsentScore();
        }
        $sourceScore = $this->getSourceScore();
        if (is_null($sourceScore))
        {
            return new NullScore();
        }
        return new GradeScore($sourceScore);
    }
}