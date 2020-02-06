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
class ClusterNode extends TreeNode
{
    /**
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [CategoryNode::class, CriteriumNode::class];
    }
}
