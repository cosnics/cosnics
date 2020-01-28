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
class CategoryNode extends TreeNode
{
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    protected $color;

    /**
     * @var CriteriumNode[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CriteriumNode", mappedBy="parentNode")
     */
    protected $criteria;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->criteria = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return CategoryNode
     */
    public function setColor(string $color): CategoryNode
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return CriteriumNode[]
     */
    public function getCriteria(): ?array
    {
        return $this->criteria;
    }

    /**
     * @param CriteriumNode[] $criteria
     *
     * @return CategoryNode
     */
    public function setCriteria(array $criteria): CategoryNode
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @param CriteriumNode $criterium
     *
     * @return CategoryNode
     */
    public function addCriterium(CriteriumNode $criterium): CategoryNode
    {
        $this->criteria->add($criterium);
        $this->children->add($criterium);

        return $this;
    }

    /**
     * @param CriteriumNode $criterium
     *
     * @return CategoryNode
     */
    public function removeCriterium(CriteriumNode $criterium): CategoryNode
    {
        $this->criteria->removeElement($criterium);
        $this->children->removeElement($criterium);

        return $this;
    }
}
