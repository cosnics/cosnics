<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Interfaces;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Implement this interface and the necessary functions in your application that launches the learning path to provide
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Interfaces
 */
interface EphorusSupportInterface
{
    public function getAssignmentEphorusURL(TreeNode $treeNode);
}