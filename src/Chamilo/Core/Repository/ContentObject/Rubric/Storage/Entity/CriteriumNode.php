<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OutOfRangeException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 */
class CriteriumNode extends TreeNode
{
    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer")
     */
    protected $weight = 100;

    /**
     * @var int
     *
     * @ORM\Column(name="rel_weight", type="integer")
     */
    protected $rel_weight = 0;

    /**
     * @var Choice[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="criterium")
     */
    protected $choices;

    /**
     * CriteriumNode constructor.
     *
     * @param string $title
     * @param RubricData $rubricData
     * @param TreeNode|null $parentNode
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function __construct(string $title, RubricData $rubricData, TreeNode $parentNode = null)
    {
        $this->choices = new ArrayCollection();

        parent::__construct($title, $rubricData, $parentNode);
    }

    /**
     * @return int
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return CriteriumNode
     */
    public function setWeight(int $weight): CriteriumNode
    {
        if ($weight < 0 || $weight > 100)
        {
            throw new OutOfRangeException('Weight must be between 0 and 100');
        }

        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getRelativeWeight(): ?int
    {
        return $this->rel_weight;
    }

    /**
     * @param ?int $weight
     *
     * @return CriteriumNode
     */
    public function setRelativeWeight(?int $weight): CriteriumNode
    {
        if ($weight < 0 || $weight > 100)
        {
            throw new OutOfRangeException('Weight must be between 0 and 100');
        }

        $this->rel_weight = $weight;

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
     * @param Choice[]|ArrayCollection $choices
     *
     * @return CriteriumNode
     */
    public function setChoices(ArrayCollection $choices)
    {
        $this->choices = $choices;

        foreach($this->choices as $choice)
        {
            $choice->setCriterium($this);
        }

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return CriteriumNode
     */
    public function addChoice(Choice $choice): self
    {
        if($this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->add($choice);
        $choice->setCriterium($this);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return CriteriumNode
     */
    public function removeChoice(Choice $choice): self
    {
        if(!$this->choices->contains($choice))
        {
            return $this;
        }

        $this->choices->removeElement($choice);
        $choice->setCriterium(null);

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [];
    }

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     * @param RubricData $rubricData
     *
     * @return RubricNode
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public static function fromJSONModel(TreeNodeJSONModel $treeNodeJSONModel, RubricData $rubricData): TreeNode
    {
        $node = new self($treeNodeJSONModel->getTitle(), $rubricData);
        $node->updateFromJSONModel($treeNodeJSONModel);

        return $node;
    }

    /**
     * @return TreeNodeJSONModel
     * @throws \Exception
     */
    public function toJSONModel(): TreeNodeJSONModel
    {
        return new TreeNodeJSONModel(
            $this->getId(), $this->getTitle(), TreeNodeJSONModel::TYPE_CRITERIUM, $this->getParentNodeId(),
            null, $this->getWeight(), $this->getRelativeWeight()
        );
    }

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     *
     * @return TreeNode
     */
    public function updateFromJSONModel(TreeNodeJSONModel $treeNodeJSONModel): TreeNode
    {
        if ($treeNodeJSONModel->getType() != TreeNodeJSONModel::TYPE_CRITERIUM)
        {
            throw new \InvalidArgumentException('The TreeNodeJSONModel does not have the correct type');
        }

        parent::updateFromJSONModel($treeNodeJSONModel);
        $this->setWeight($treeNodeJSONModel->getWeight());
        $this->setRelativeWeight($treeNodeJSONModel->getRelativeWeight());

        return $this;
    }

    /**
     * @return float
     */
    public function getMaximumScore()
    {
        $maximumScore = 0;

        foreach($this->choices as $choice)
        {
            $score = $choice->calculateScore();
            if($score > $maximumScore)
            {
                $maximumScore = $score;
            }
        }

        return $maximumScore;
    }

}
