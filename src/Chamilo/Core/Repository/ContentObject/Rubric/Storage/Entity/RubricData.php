<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository")
 *
 * @ORM\Table(
 *      name="repository_rubric_data"
 * )
 */
class RubricData
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
     * @ORM\Column(name="use_scores", type="boolean")
     */
    protected $useScores;

    /**
     * @var int
     *
     * @ORM\Version
     * @ORM\Column(name="version", type="integer")
     *
     */
    protected $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime")
     */
    protected $lastUpdated;

    /**
     * @var Choice[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="rubricData")
     */
    protected $choices;

    /**
     * @var Level[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Level", mappedBy="rubricData")
     */
    protected $levels;

    /**
     * @var TreeNode
     *
     * @ORM\OneToOne(targetEntity="TreeNode")
     * @ORM\JoinColumn(name="rubric_root_node_id", referencedColumnName="id")
     */
    protected $rootNode;

    /**
     * @var TreeNode[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TreeNode", mappedBy="rubricData")
     */
    protected $treeNodes;

    /**
     * RubricData constructor.
     *
     * @param string $rubricTitle
     * @param bool $useScores
     *
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function __construct(string $rubricTitle, bool $useScores = true)
    {
        $this->choices = new ArrayCollection();
        $this->levels = new ArrayCollection();
        $this->treeNodes = new ArrayCollection();

        $rootNode = new RubricNode($rubricTitle, $this);
        $this->setRootNode($rootNode);

        $this->useScores = $useScores;
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
    public function useScores(): ?bool
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
     * @return Choice[]|ArrayCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @return Level[]|ArrayCollection
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @return TreeNode
     */
    public function getRootNode(): ?TreeNode
    {
        return $this->rootNode;
    }

    /**
     * @param TreeNode $rootNode
     *
     * @return RubricData
     */
    public function setRootNode(TreeNode $rootNode): RubricData
    {
        $this->rootNode = $rootNode;
        $this->addTreeNode($rootNode);

        $rootNode->setSort(1);

        return $this;
    }

    /**
     * @return TreeNode[]|ArrayCollection
     */
    public function getTreeNodes()
    {
        return $this->treeNodes;
    }

    /**
     * @param Level $level
     *
     * @return self
     */
    public function addLevel(Level $level): self
    {
        if($this->levels->contains($level))
        {
            return $this;
        }

        $this->levels->add($level);
        $level->setRubricData($this);

        return $this;
    }

    /**
     * @param Level $level
     *
     * @return self
     */
    public function removeLevel(Level $level): self
    {
        if(!$this->levels->contains($level))
        {
            return $this;
        }

        $this->levels->removeElement($level);
        $level->setRubricData(null);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return self
     */
    public function addChoice(Choice $choice): self
    {
        if($this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->add($choice);
        $choice->setRubricData($this);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return self
     */
    public function removeChoice(Choice $choice): self
    {
        if(!$this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->removeElement($choice);
        $choice->setRubricData(null);

        return $this;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return self
     */
    public function addTreeNode(TreeNode $treeNode): self
    {
        if($this->treeNodes->contains($treeNode))
        {
            return $this;
        }

        $this->treeNodes->add($treeNode);
        $treeNode->setRubricData($this);

        return $this;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return self
     */
    public function removeTreeNode(TreeNode $treeNode): self
    {
        if(!$this->treeNodes->contains($treeNode))
        {
            return $this;
        }

        $this->treeNodes->removeElement($treeNode);
        $treeNode->setRubricData(null);

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return RubricData
     */
    public function setVersion(int $version): RubricData
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }

    /**
     * @param \DateTime $lastUpdated
     *
     * @return RubricData
     */
    public function setLastUpdated(\DateTime $lastUpdated): RubricData
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

}
