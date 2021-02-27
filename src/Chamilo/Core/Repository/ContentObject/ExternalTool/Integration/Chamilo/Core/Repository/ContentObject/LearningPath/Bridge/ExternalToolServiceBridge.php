<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolServiceBridge implements ExternalToolServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface
     */
    protected $learningPathExternalToolServiceBridge;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    protected $treeNodeAttempt;

    /**
     * ExternalToolServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface $learningPathExternalToolServiceBridge
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface $learningPathExternalToolServiceBridge
    )
    {
        $this->learningPathExternalToolServiceBridge = $learningPathExternalToolServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof ExternalTool)
        {
            throw new \RuntimeException(
                'The given tree node does not reference a valid external tool and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     */
    public function setTreeNodeAttempt(TreeNodeAttempt $treeNodeAttempt)
    {
        $this->treeNodeAttempt = $treeNodeAttempt;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool()
    {
        return $this->learningPathExternalToolServiceBridge->getExternalTool($this->treeNode);
    }

    /**
     * Returns a unique ID to identify the context where the tool is running
     *
     * @return string
     */
    public function getContextIdentifier()
    {
        return $this->learningPathExternalToolServiceBridge->getContextIdentifier($this->treeNode);
    }

    /**
     * Returns the title of the context where the tool is running
     *
     * @return string
     */
    public function getContextTitle()
    {
        return $this->learningPathExternalToolServiceBridge->getContextTitle($this->treeNode);
    }

    /**
     * Returns a unique label / code of the context where the tool is running
     *
     * @return string
     */
    public function getContextLabel()
    {
        return $this->learningPathExternalToolServiceBridge->getContextLabel($this->treeNode);
    }

    /**
     * Returns a unique ID to identify the external link in the context (e.g. the publication ID).
     * Preferred obfuscated with Base64 encoding
     *
     * @return string
     */
    public function getResourceLinkIdentifier()
    {
        return $this->learningPathExternalToolServiceBridge->getResourceLinkIdentifier($this->treeNode);
    }

    /**
     * Returns whether or not the current user is allowed to be a course instructor in the external tool
     *
     * @return bool
     */
    public function isCourseInstructorInTool()
    {
        return $this->learningPathExternalToolServiceBridge->isCourseInstructorInTool($this->treeNode);
    }

    /**
     * Returns the classname of the LTI Integration service. This classname is used to define the context needed
     * for the LTI webservices.
     *
     * @return string
     */
    public function getLTIIntegrationClass()
    {
        return $this->learningPathExternalToolServiceBridge->getLTIIntegrationClass($this->treeNode);
    }

    /**
     * Returns the result identifier for the current user. This identifier is used for the basic outcomes LTI webservice.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function getOrCreateResultIdentifierForUser(User $user)
    {
        return $this->learningPathExternalToolServiceBridge->getOrCreateResultIdentifierForUser(
            $user, $this->treeNode, $this->treeNodeAttempt
        );
    }

    /**
     * Returns whether or not the outcomes service is supported
     *
     * @return bool
     */
    public function supportsOutcomesService()
    {
        return $this->learningPathExternalToolServiceBridge->supportsOutcomesService();
    }
}