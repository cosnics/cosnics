<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;

interface ExternalToolServiceBridgeInterface
{

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool(TreeNode $treeNode);

    /**
     * Returns a unique ID to identify the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextIdentifier(TreeNode $treeNode);

    /**
     * Returns the title of the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextTitle(TreeNode $treeNode);

    /**
     * Returns a unique label / code of the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextLabel(TreeNode $treeNode);

    /**
     * Returns a unique ID to identify the external link in the context (e.g. the publication ID).
     * Preferred obfuscated with Base64 encoding
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getResourceLinkIdentifier(TreeNode $treeNode);

    /**
     * Returns whether or not the current user is allowed to be a course instructor in the external tool
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return bool
     */
    public function isCourseInstructorInTool(TreeNode $treeNode);

    /**
     * Returns the classname of the LTI Integration service. This classname is used to define the context needed
     * for the LTI webservices.
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getLTIIntegrationClass(TreeNode $treeNode);

    /**
     * Returns the result identifier for the current user. This identifier is used for the basic outcomes LTI webservice.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     *
     * @return int
     */
    public function getOrCreateResultIdentifierForUser(User $user, TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt);

    /**
     * Returns whether or not the outcomes service is supported
     *
     * @return bool
     */
    public function supportsOutcomesService();
}