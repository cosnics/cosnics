<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @ORM\Table(
 *      name="repository_rubric_choice"
 * )
 */
class Choice
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
     * @var bool
     *
     * @ORM\Column(name="selected", type="boolean")
     */
    protected $selected = false;

    /**
     * @var string
     *
     *  @ORM\Column(name="feedback", type="text", nullable=true)
     */
    protected $feedback;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_fixed_score", type="boolean")
     */
    protected $hasFixedScore = false;

    /**
     * @var int
     *
     * @ORM\Column(name="fixed_score", type="integer")
     */
    protected $fixedScore = 0;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     */
    protected $level;

    /**
     * @var CriteriumNode
     *
     * @ORM\ManyToOne(targetEntity="CriteriumNode")
     * @ORM\JoinColumn(name="criterium_id", referencedColumnName="id")
     */
    protected $criterium;

    /**
     * @var RubricData
     *
     * @ORM\ManyToOne(targetEntity="RubricData")
     * @ORM\JoinColumn(name="rubric_data_id", referencedColumnName="id")
     */
    protected $rubricData;

    /**
     * Choice constructor.
     *
     * @param RubricData $rubricData
     */
    public function __construct(RubricData $rubricData)
    {
        $this->setRubricData($rubricData);
    }

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
     * @return Choice
     */
    public function setId(int $id): Choice
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSelected(): ?bool
    {
        return $this->selected;
    }

    /**
     * @param bool $selected
     *
     * @return Choice
     */
    public function setSelected(bool $selected): Choice
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    /**
     * @param string $feedback
     *
     * @return Choice
     */
    public function setFeedback(string $feedback): Choice
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFixedScore(): ?bool
    {
        return $this->hasFixedScore;
    }

    /**
     * @param bool $hasFixedScore
     *
     * @return Choice
     */
    public function setHasFixedScore(bool $hasFixedScore): Choice
    {
        $this->hasFixedScore = $hasFixedScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getFixedScore(): ?int
    {
        return $this->fixedScore;
    }

    /**
     * @param int $fixedScore
     *
     * @return Choice
     */
    public function setFixedScore(int $fixedScore): Choice
    {
        $this->fixedScore = $fixedScore;

        return $this;
    }

    /**
     * @return Level
     */
    public function getLevel(): ?Level
    {
        return $this->level;
    }

    /**
     * @param Level $level
     *
     * @return Choice
     */
    public function setLevel(Level $level = null): Choice
    {
        if($this->level === $level)
        {
            return $this;
        }

        $oldLevel = $this->level;
        $this->level = $level;

        if($oldLevel instanceof Level)
        {
            $oldLevel->removeChoice($this);
        }

        if($level instanceof Level)
        {
            $level->addChoice($this);
        }

        return $this;
    }

    /**
     * @return CriteriumNode
     */
    public function getCriterium(): ?CriteriumNode
    {
        return $this->criterium;
    }

    /**
     * @param CriteriumNode $criterium
     *
     * @return Choice
     */
    public function setCriterium(CriteriumNode $criterium = null): Choice
    {
        if($this->criterium === $criterium)
        {
            return $this;
        }

        $oldCriterium = $this->criterium;
        $this->criterium = $criterium;

        if($oldCriterium instanceof CriteriumNode)
        {
            $oldCriterium->removeChoice($this);
        }

        if($criterium instanceof CriteriumNode)
        {
            $criterium->addChoice($this);
        }

        return $this;
    }

    /**
     * @return RubricData
     */
    public function getRubricData(): ?RubricData
    {
        return $this->rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return Choice
     */
    public function setRubricData(RubricData $rubricData = null): Choice
    {
        if($this->rubricData === $rubricData)
        {
            return $this;
        }

        $oldRubricData = $this->rubricData;
        $this->rubricData = $rubricData;

        if($oldRubricData instanceof RubricData)
        {
            $oldRubricData->removeChoice($this);
        }

        if($rubricData instanceof RubricData)
        {
            $rubricData->addChoice($this);
        }

        return $this;
    }
}
