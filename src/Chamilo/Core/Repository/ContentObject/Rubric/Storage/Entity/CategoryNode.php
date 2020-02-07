<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

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
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [CriteriumNode::class];
    }
}
