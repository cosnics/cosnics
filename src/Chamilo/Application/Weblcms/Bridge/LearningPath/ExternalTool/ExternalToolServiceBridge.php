<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool;

use Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service\LTIIntegration;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolServiceBridge implements ExternalToolServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service\ExternalToolResultService
     */
    protected $externalToolResultService;

    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected $course;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $hasEditRight;

    /**
     * ExternalToolServiceBridge constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service\ExternalToolResultService $externalToolResultService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service\ExternalToolResultService $externalToolResultService
    )
    {
        $this->externalToolResultService = $externalToolResultService;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\ExternalToolServiceBridge
     */
    public function setCourse(\Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\ExternalToolServiceBridge
     */
    public function setContentObjectPublication(
        \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
    )
    {
        if (!$contentObjectPublication->get_content_object() instanceof LearningPath)
        {
            throw new \RuntimeException(
                'The given publication does not reference a valid external tool and can therefor not be displayed'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;

        return $this;
    }

    /**
     * @param bool $hasEditRight
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\ExternalToolServiceBridge
     */
    public function setHasEditRight(bool $hasEditRight)
    {
        $this->hasEditRight = $hasEditRight;

        return $this;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool(TreeNode $treeNode)
    {
        $contentObject = $treeNode->getContentObject();
        if(!$contentObject instanceof ExternalTool)
        {
            throw new \RuntimeException('The given content object in the tree node is not of type external tool');
        }

        return $contentObject;
    }

    /**
     * Returns whether or not the outcomes service is supported
     *
     * @return bool
     */
    public function supportsOutcomesService()
    {
        return true;
    }

    /**
     * Returns a unique ID to identify the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextIdentifier(TreeNode $treeNode)
    {
        $data = json_encode(
            [
                'course' => $this->course->getId(),
                'content_object_publication' => $this->contentObjectPublication->getId()
            ]
        );

        return base64_encode($data);
    }

    /**
     * Returns the title of the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextTitle(TreeNode $treeNode)
    {
        return $this->course->get_title() . ' - ' . $this->contentObjectPublication->get_content_object()->get_title();
    }

    /**
     * Returns a unique label / code of the context where the tool is running
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getContextLabel(TreeNode $treeNode)
    {
        return $this->course->get_visual_code() . ':' . $this->contentObjectPublication->getId();
    }

    /**
     * Returns a unique ID to identify the external link in the context (e.g. the publication ID).
     * Preferred obfuscated with Base64 encoding
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getResourceLinkIdentifier(TreeNode $treeNode)
    {
        $data = $this->course->getId() . ':' . $this->contentObjectPublication->getId() . ':' . $treeNode->getId();

        return base64_encode($data);
    }

    /**
     * Returns whether or not the current user is allowed to be a course instructor in the external tool
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return bool
     */
    public function isCourseInstructorInTool(TreeNode $treeNode)
    {
        return $this->hasEditRight;
    }

    /**
     * Returns the classname of the LTI Integration service. This classname is used to define the context needed
     * for the LTI webservices.
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return string
     */
    public function getLTIIntegrationClass(TreeNode $treeNode)
    {
        return LTIIntegration::class;
    }

    /**
     * Returns the result identifier for the current user. This identifier is used for the basic outcomes LTI webservice.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $treeNodeAttempt
     *
     * @return int
     */
    public function getOrCreateResultIdentifierForUser(User $user, TreeNode $treeNode, TreeNodeAttempt $treeNodeAttempt)
    {
        return $treeNodeAttempt->getId();
    }
}