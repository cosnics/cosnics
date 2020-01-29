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
     * @param TreeNode|null $parentNode
     */
    public function __construct(string $title, TreeNode $parentNode = null)
    {
        parent::__construct($title, $parentNode);

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
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return CriteriumNode
     */
    public function addChoice(Choice $choice): self
    {
        $choice->setCriterium($this);
        $this->choices->add($choice);

        return $this;
    }

    /**
     * @param Choice $choice
     *
     * @return CriteriumNode
     */
    public function removeChoice(Choice $choice): self
    {
        $choice->setCriterium(null);
        $this->choices->removeElement($choice);

        return $this;
    }


}
