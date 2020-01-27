<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

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


}
