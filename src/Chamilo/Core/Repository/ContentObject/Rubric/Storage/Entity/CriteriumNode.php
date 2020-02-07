<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Choice[] | ArrayCollection
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
        parent::__construct($title, $rubricData, $parentNode);

        $this->choices = new ArrayCollection();
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
        $this->weight = $weight;

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

}
