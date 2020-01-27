<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricData
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $useScores;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Level[]
     */
    protected $levels;

    /**
     * @var Cluster[]
     */
    protected $clusters;

    /**
     * @var Choice[]
     */
    protected $choices;

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
     * @return RubricData
     */
    public function setId(int $id): RubricData
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseScores(): ?bool
    {
        return $this->useScores;
    }

    /**
     * @param bool $useScores
     *
     * @return RubricData
     */
    public function setUseScores(bool $useScores): RubricData
    {
        $this->useScores = $useScores;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return RubricData
     */
    public function setTitle(string $title): RubricData
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Level[]
     */
    public function getLevels(): ?array
    {
        return $this->levels;
    }

    /**
     * @param Level[] $levels
     *
     * @return RubricData
     */
    public function setLevels(array $levels): RubricData
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * @return Cluster[]
     */
    public function getClusters(): ?array
    {
        return $this->clusters;
    }

    /**
     * @param Cluster[] $clusters
     *
     * @return RubricData
     */
    public function setClusters(array $clusters): RubricData
    {
        $this->clusters = $clusters;

        return $this;
    }

    /**
     * @return Choice[]
     */
    public function getChoices(): ?array
    {
        return $this->choices;
    }

    /**
     * @param Choice[] $choices
     *
     * @return RubricData
     */
    public function setChoices(array $choices): RubricData
    {
        $this->choices = $choices;

        return $this;
    }


}
