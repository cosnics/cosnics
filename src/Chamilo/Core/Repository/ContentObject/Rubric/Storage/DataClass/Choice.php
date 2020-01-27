<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Choice
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $selected;

    /**
     * @var string
     */
    protected $feedback;

    /**
     * @var bool
     */
    protected $hasFixedScore;

    /**
     * @var int
     */
    protected $fixedScore;

    /**
     * @var Level
     */
    protected $level;

    /**
     * @var Criterium
     */
    protected $criterium;

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
    public function isHasFixedScore(): ?bool
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
    public function setLevel(Level $level): Choice
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Criterium
     */
    public function getCriterium(): ?Criterium
    {
        return $this->criterium;
    }

    /**
     * @param Criterium $criterium
     *
     * @return Choice
     */
    public function setCriterium(Criterium $criterium): Choice
    {
        $this->criterium = $criterium;

        return $this;
    }
}
