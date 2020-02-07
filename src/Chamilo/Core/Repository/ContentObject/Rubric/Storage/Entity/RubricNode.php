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
class RubricNode extends TreeNode
{
    /**
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [ClusterNode::class, CategoryNode::class, CriteriumNode::class];
    }
}
