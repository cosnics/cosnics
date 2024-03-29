<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

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
 *
 * When a choice is deleted from the rubric, the choices should be deleted both from the rubric as well as from the
 * criterium node
 * When a choice is deleted from a criterium node it should also be disconnected from the rubric.
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
     * @var bool
     *
     * @ORM\Column(name="use_relative_weights", type="boolean")
     */
    protected $useRelativeWeights;

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
     *
     * @Exclude
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
     *
     * @Exclude
     */
    protected $treeNodes;

    /**
     * @var int
     *
     * @ORM\Column(name="content_object_id", type="integer", nullable=true)
     */
    protected $contentObjectId;

    /**
     * Keeps track of removed entities so they can be removed from the database after
     *
     * @var ArrayCollection
     */
    protected $removedEntities;

    /**
     * RubricData constructor.
     *
     * @param string $rubricTitle
     * @param bool $useScores
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function __construct(string $rubricTitle, bool $useScores = true)
    {
        $this->choices = new ArrayCollection();
        $this->levels = new ArrayCollection();
        $this->treeNodes = new ArrayCollection();
        $this->removedEntities = new ArrayCollection();

        $rootNode = new RubricNode($rubricTitle, $this);
        $this->setRootNode($rootNode);

        $this->useScores = $useScores;
        $this->useRelativeWeights = false;
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
     * @return bool
     */
    public function useRelativeWeights(): ?bool
    {
        return $this->useRelativeWeights;
    }

    /**
     * @param bool $useRelativeWeights
     *
     * @return RubricData
     */
    public function setUseRelativeWeights(bool $useRelativeWeights): RubricData
    {
        $this->useRelativeWeights = $useRelativeWeights;

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
     * @return int
     */
    public function maxLevelScore(): int
    {
        $score = 0;
        foreach ($this->levels as $level)
        {
            if ($level->getScore() > $score)
            {
                $score = $level->getScore();
            }
        }
        return $score;
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
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function setRootNode(TreeNode $rootNode = null): RubricData
    {
        $this->rootNode = $rootNode;

        if($rootNode instanceof TreeNode)
        {
            $this->addTreeNode($rootNode);

            $rootNode->setSort(1);
        }

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
     * @param Level $levelToAdd
     *
     * @return self
     */
    public function addLevel(Level $levelToAdd): self
    {
        if ($this->levels->contains($levelToAdd))
        {
            return $this;
        }

        if ($levelToAdd->isDefault())
        {
            $this->setCurrentDefaultLevelNoLongerDefault();
        }

        $this->levels->add($levelToAdd);
        $levelToAdd->setRubricData($this);

        $levels = $this->filterLevelsByCriterium($levelToAdd->getCriterium());

        $levelToAdd->setSort(count($levels));

        if ($levelToAdd->hasCriterium())
        {
            foreach ($levelToAdd->getCriterium()->getChoices() as $choice)
            {
                $this->removeChoice($choice);
            }

            return $this;
        }

        foreach ($this->getCriteriumNodes() as $criteriumNode)
        {
            if (!$criteriumNode->hasLevels())
            {
                $choice = new Choice($this);
                $choice->setLevel($levelToAdd);
                $criteriumNode->addChoice($choice);
            }
        }

        return $this;
    }

    /**
     * @param Level $levelToRemove
     *
     * @return self
     */
    public function removeLevel(Level $levelToRemove): self
    {
        if (!$this->levels->contains($levelToRemove))
        {
            return $this;
        }

        $this->levels->removeElement($levelToRemove);
        $levelCriterium = $levelToRemove->getCriterium();
        $levelToRemove->setCriterium(null);
        $levelToRemove->setRubricData(null);

        $levels = $this->filterLevelsByCriterium($levelCriterium);
        foreach ($levels as $level)
        {
            if ($level->getSort() >= $levelToRemove->getSort())
            {
                $level->setSort($level->getSort() - 1);
            }
        }

        foreach ($levelToRemove->getChoices() as $choice)
        {
            $this->removeChoice($choice);
        }

        $this->getRemovedEntities()->add($levelToRemove);

        if (!empty($levelCriterium) && !$levelCriterium->hasLevels())
        {
            foreach ($this->filterLevelsByCriterium(null) as $level)
            {
                $choice = new Choice($this);
                $choice->setLevel($level);
                $levelCriterium->addChoice($choice);
            }
        }

        return $this;
    }

    /**
     * @param Level $levelToMove
     * @param int $newSort
     */
    public function moveLevel(Level $levelToMove, int $newSort)
    {
        if (!$this->levels->contains($levelToMove))
        {
            throw new \InvalidArgumentException(
                sprintf('The given level %s is not available in rubric data %s', $levelToMove->getId(), $this->getId())
            );
        }

        $levels = $this->filterLevelsByCriterium($levelToMove->getCriterium());

        $oldSort = $levelToMove->getSort();

        foreach ($levels as $level)
        {
            if ($level == $levelToMove)
            {
                continue;
            }

            if ($level->getSort() >= $oldSort)
            {
                $level->decrementSort();
            }

            if ($level->getSort() >= $newSort)
            {
                $level->incrementSort();
            }
        }

        $levelToMove->setSort($newSort);

        return $this;
    }

    /**
     * @param CriteriumNode|null $criterium
     */
    public function setCurrentDefaultLevelNoLongerDefault(CriteriumNode $criterium = null)
    {
        $levels = $this->filterLevelsByCriterium($criterium);

        foreach ($levels as $level)
        {
            if ($level->isDefault())
            {
                $level->setIsDefault(false);
            }
        }
    }

    /**
     * @param Choice $choice
     *
     * @return self
     */
    public function addChoice(Choice $choice): self
    {
        if ($this->choices->contains($choice))
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
        if (!$this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->removeElement($choice);

        $choice->setRubricData(null);
        $choice->setCriterium(null);
        $choice->setLevel(null);
        $this->getRemovedEntities()->add($choice);

        return $this;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return self
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function addTreeNode(TreeNode $treeNode): self
    {
        if ($this->treeNodes->contains($treeNode))
        {
            return $this;
        }

        if ($treeNode instanceof CriteriumNode && !$treeNode->hasLevels())
        {
            foreach ($this->filterLevelsByCriterium(null) as $level)
            {
                $choice = new Choice($this);
                $choice->setLevel($level);
                $treeNode->addChoice($choice);
            }
        }

        $this->treeNodes->add($treeNode);
        $treeNode->setRubricData($this);

        return $this;
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return self
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function removeTreeNode(TreeNode $treeNode): self
    {
        if (!$this->treeNodes->contains($treeNode))
        {
            return $this;
        }

        $this->treeNodes->removeElement($treeNode);
        $treeNode->setRubricData(null);
        $treeNode->setParentNode(null);

        if ($treeNode instanceof CriteriumNode)
        {
            foreach ($treeNode->getChoices() as $choice)
            {
                $this->removeChoice($choice);
            }

            foreach ($treeNode->getLevels() as $level)
            {
                $this->removeLevel($level);
            }
        }

        foreach ($treeNode->getChildren() as $child)
        {
            $this->removeTreeNode($child);
        }

        $this->getRemovedEntities()->add($treeNode);

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

    /**
     * @return int
     */
    public function getContentObjectId(): ?int
    {
        return $this->contentObjectId;
    }

    /**
     * @param int $contentObjectId
     *
     * @return RubricData
     */
    public function setContentObjectId(int $contentObjectId): RubricData
    {
        $this->contentObjectId = $contentObjectId;

        return $this;
    }

    /**
     * @param int $treeNodeIdentifier
     *
     * @return TreeNode
     *
     * @throws ObjectNotExistException
     */
    public function getTreeNodeById(int $treeNodeIdentifier)
    {
        $treeNode = $this->treeNodes->filter(function(TreeNode $treeNode) use ($treeNodeIdentifier) {
            return $treeNode->getId() == $treeNodeIdentifier;
        })->first();

        if (!$treeNode instanceof TreeNode)
        {
            throw new ObjectNotExistException('tree node', $treeNodeIdentifier);
        }

        return $treeNode;
    }

    /**
     * @param int $criteriumIdentifier
     *
     * @return CriteriumNode
     *
     * @throws ObjectNotExistException
     */
    public function getCriteriumById(int $criteriumIdentifier)
    {
        $criterium = $this->getTreeNodeById($criteriumIdentifier);

        if (!$criterium instanceof CriteriumNode)
        {
            throw new ObjectNotExistException('criterium', $criteriumIdentifier);
        }

        return $criterium;
    }

    /**
     * @param CriteriumNode $criteriumNode
     *
     * @return bool
     */
    public function hasCriterium(CriteriumNode $criteriumNode): bool
    {
        return $this->getCriteriumNodes()->contains($criteriumNode);
    }

    /**
     * @param int $levelIdentifier
     *
     * @return Level
     *
     * @throws ObjectNotExistException
     */
    public function getLevelById(int $levelIdentifier)
    {
        $level = $this->levels->filter(function(Level $level) use ($levelIdentifier) {
            return $level->getId() == $levelIdentifier;
        })->first();

        if (!$level instanceof Level)
        {
            throw new ObjectNotExistException('level', $levelIdentifier);
        }

        return $level;
    }

    /**
     * @param int $choiceIdentifier
     *
     * @return Choice
     *
     * @throws ObjectNotExistException
     */
    public function getChoiceById(int $choiceIdentifier)
    {
        $choice = $this->choices->filter(function(Choice $choice) use ($choiceIdentifier) {
            return $choice->getId() == $choiceIdentifier;
        })->first();

        if (!$choice instanceof Choice)
        {
            throw new ObjectNotExistException('choice', $choiceIdentifier);
        }

        return $choice;
    }

    /**
     * @param int $levelId
     * @param int $criteriumId
     *
     * @return Choice|mixed
     * @throws ObjectNotExistException
     */
    public function getChoiceByLevelAndCriteriumId(int $levelId, int $criteriumId)
    {
        $choice = $this->choices->filter(function(Choice $choice) use ($levelId, $criteriumId) {
            $criterium = $choice->getCriterium();
            $level = $choice->getLevel();

            if(!$choice instanceof Choice || !$criterium instanceof CriteriumNode)
            {
                return false;
            }

            return $criterium->getId() == $criteriumId && $level->getId() == $levelId;

        })->first();

        if (!$choice instanceof Choice)
        {
            throw new ObjectNotExistException('choice', $levelId . '-' . $criteriumId);
        }

        return $choice;
    }

    /**
     * @param int|null $parentNodeId
     *
     * @return TreeNode|null
     * @throws ObjectNotExistException
     */
    public function getParentNodeById(int $parentNodeId = null)
    {
        return empty($parentNodeId) ? $this->getRootNode() : $this->getTreeNodeById($parentNodeId);
    }

    /**
     * @param Level $level
     * @param int $possibleSort
     *
     * @return bool
     */
    public function isLevelSortValid(Level $level, int $possibleSort): bool
    {
        $levels = $this->filterLevelsByCriterium($level->getCriterium());
        return $possibleSort > 0 && $possibleSort <= count($levels);
    }

    /**
     * @return ArrayCollection|CriteriumNode[]
     */
    public function getCriteriumNodes()
    {
        return $this->treeNodes->filter(function(TreeNode $treeNode) {
            return $treeNode instanceof CriteriumNode;
        });
    }

    /**
     * @return ArrayCollection|ClusterNode[]
     */
    public function getClusterNodes()
    {
        return $this->treeNodes->filter(function(TreeNode $treeNode) {
            return $treeNode instanceof ClusterNode;
        });
    }

    /**
     * @return ArrayCollection|CategoryNode[]
     */
    public function getCategoryNodes()
    {
        return $this->treeNodes->filter(function(TreeNode $treeNode) {
            return $treeNode instanceof CategoryNode;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getRemovedEntities(): ?ArrayCollection
    {
        if(!$this->removedEntities instanceof ArrayCollection)
        {
            $this->removedEntities = new ArrayCollection();
        }

        return $this->removedEntities;
    }

    /**
     * @return float
     */
    public function getMaximumScore(): float
    {
        if ($this->useRelativeWeights()) {
            return 100.0;
        }

        $maximumScore = 0.0;

        foreach ($this->getTreeNodes() as $treeNode)
        {
            if (!$treeNode instanceof CriteriumNode)
            {
                continue;
            }

            $maximumScore += $treeNode->getMaximumScore();
        }

        return $maximumScore;
    }

    /**
     * @return float
     */
    public function getEqRestWeight(): float
    {
        $criteria = $this->getCriteriumNodes();
        $countWithout = 0;
        $sum = 0;
        foreach($criteria as $criterium)
        {
            if (is_null($criterium->getRelativeWeight()))
            {
                $countWithout += 1;
            }
            else
            {
                $sum += $criterium->getRelativeWeight();
            }
        }
        if (empty($countWithout))
        {
            return 0;
        }
        return (100 - $sum) / $countWithout;
    }

    /**
     * @param CriteriumNode|null $criterium
     *
     * @return array
     */
    public function filterLevelsByCriterium(CriteriumNode $criterium = null): array
    {
        $levels = array();
        foreach ($this->levels as $level)
        {
            if (!empty($criterium) && $criterium === $level->getCriterium())
            {
                $levels[] = $level;
            }
            elseif (empty($criterium) && empty($level->getCriterium())) // levels without a criterium
            {
                $levels[] = $level;
            }
        }
        return $levels;
    }
}
