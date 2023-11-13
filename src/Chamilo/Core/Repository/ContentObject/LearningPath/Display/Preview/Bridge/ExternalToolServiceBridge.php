<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Bridge;

use Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service\LTIIntegration;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

class ExternalToolServiceBridge implements ExternalToolServiceBridgeInterface
{

    public function getExternalTool(TreeNode $treeNode)
    {
        return $treeNode->getContentObject();
    }

    public function getContextIdentifier(TreeNode $treeNode)
    {
        return 0;
    }

    public function getContextTitle(TreeNode $treeNode)
    {
        return 'Preview';
    }

    public function getContextLabel(TreeNode $treeNode)
    {
        return 'Preview';
    }

    public function getResourceLinkIdentifier(TreeNode $treeNode)
    {
        return 1;
    }

    public function isCourseInstructorInTool(TreeNode $treeNode)
    {
        return false;
    }

    public function getLTIIntegrationClass(TreeNode $treeNode)
    {
        return LTIIntegration::class;
    }

    public function getOrCreateResultIdentifierForUser(User $user, TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt)
    {
        return 0;
    }

    public function supportsOutcomesService()
    {
        return false;
    }
}