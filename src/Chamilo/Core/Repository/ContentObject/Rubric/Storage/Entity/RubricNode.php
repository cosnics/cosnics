<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 */
class RubricNode extends TreeNode
{
    /**
     * @var bool
     *
     * @ORM\Column(name="use_scores", type="boolean")
     */
    protected $useScores;

    /**
     * @var Level[]
     *
     * @OneToMany(targetEntity="Level", mappedBy="rubric")
     */
    protected $levels;

    /**
     * @var ClusterNode[]
     *
     * @OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $clusters;

    /**
     * @var CategoryNode[]
     *
     * @OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $categories;

    /**
     * @var CriteriumNode[]
     *
     * @OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $criteria;

    /**
     * @var Choice[]
     *
     * @OneToMany(targetEntity="Choice", mappedBy="rubric")
     */
    protected $choices;

    /**
     * RubricData constructor.
     */
    public function __construct()
    {
        $this->levels = new ArrayCollection();
        $this->clusters = new ArrayCollection();
        $this->choices = new ArrayCollection();
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
     * @return RubricNode
     */
    public function setUseScores(bool $useScores): RubricNode
    {
        $this->useScores = $useScores;

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
     * @return RubricNode
     */
    public function setLevels(array $levels): RubricNode
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * @return ClusterNode[]
     */
    public function getClusters(): ?array
    {
        return $this->clusters;
    }

    /**
     * @param ClusterNode[] $clusters
     *
     * @return RubricNode
     */
    public function setClusters(array $clusters): RubricNode
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
     * @return RubricNode
     */
    public function setChoices(array $choices): RubricNode
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @param Level $level
     *
     * @return RubricNode
     */
    public function addLevel(Level $level): RubricNode
    {
        $this->levels->add($level);

        return $this;
    }

    /**
     * @param Level $level
     *
     * @return RubricNode
     */
    public function removeLevel(Level $level): RubricNode
    {
        $this->levels->removeElement($level);

        return $this;
    }

    /**
     * @param ClusterNode $cluster
     *
     * @return RubricNode
     */
    public function addCluster(ClusterNode $cluster): RubricNode
    {
        $this->clusters->add($cluster);

        return $this;
    }

    /**
     * @param ClusterNode $cluster
     *
     * @return RubricNode
     */
    public function removeCluster(ClusterNode $cluster): RubricNode
    {
        $this->clusters->removeElement($cluster);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return RubricNode
     */
    public function addChoice(Choice $choice): RubricNode
    {
        $this->choices->add($choice);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return RubricNode
     */
    public function removeChoice(Choice $choice): RubricNode
    {
        $this->choices->removeElement($choice);

        return $this;
    }

}
