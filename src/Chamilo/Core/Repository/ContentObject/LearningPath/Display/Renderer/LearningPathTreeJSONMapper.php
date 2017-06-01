<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Renders a learning path tree
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTreeJSONMapper
{
    const NODE_PLACEHOLDER = '__NODE__';

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var LearningPathTree
     */
    protected $learningPathTree;

    /**
     * @var LearningPathTrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * @var string
     */
    protected $treeMenuUrl;

    /**
     * @var LearningPathTreeNode
     */
    protected $currentLearningPathTreeNode;

    /**
     * @var bool
     */
    protected $allowedToViewContentObject;

    /**
     * @var NodeActionGenerator
     */
    protected $nodeActionGenerator;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var bool
     */
    protected $allowedToEditLearningPathTree;

    /**
     * @param LearningPathTree $learningPathTree
     * @param User $user
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param AutomaticNumberingService $automaticNumberingService
     * @param NodeActionGenerator $nodeActionGenerator
     * @param string $treeMenuUrl
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param bool $allowedToViewContentObject
     */
    public function __construct(
        LearningPathTree $learningPathTree, User $user,
        LearningPathTrackingService $learningPathTrackingService = null,
        AutomaticNumberingService $automaticNumberingService,
        NodeActionGenerator $nodeActionGenerator,
        $treeMenuUrl, LearningPathTreeNode $currentLearningPathTreeNode,
        $allowedToViewContentObject, $allowedToEditLearningPathTree = false
    )
    {
        $this->learningPathTree = $learningPathTree;
        $this->user = $user;
        $this->learningPath = $learningPathTree->getRoot()->getContentObject();
        $this->learningPathTrackingService = $learningPathTrackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->nodeActionGenerator = $nodeActionGenerator;
        $this->treeMenuUrl = $treeMenuUrl;
        $this->currentLearningPathTreeNode = $currentLearningPathTreeNode;
        $this->allowedToViewContentObject = $allowedToViewContentObject;
        $this->allowedToEditLearningPathTree = $allowedToEditLearningPathTree;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(LearningPathTreeNode $node)
    {
        $objectType = (string) StringUtilities::getInstance()->createString(
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace($node->getContentObject()->package())
        )->underscored();

        $class = 'type_' . $objectType;

        if ($this->learningPathTrackingService &&
            $this->learningPathTrackingService->isLearningPathTreeNodeCompleted(
                $this->learningPath, $this->user, $node
            )
        )
        {
            $class .= ' type_completed';
        }

        return $class;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return boolean
     */
    protected function isSelectedItem(LearningPathTreeNode $node)
    {
        return $this->currentLearningPathTreeNode->getId() == $node->getId();
    }

    /**
     * @return string[]
     */
    public function getNodes()
    {
        $nodeData = array();

        $nodeData[] = $this->getNodeDataForLearningPathTreeNode($this->learningPathTree->getRoot());

        return $nodeData;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return \string[]
     */
    protected function getNodeDataForLearningPathTreeNode(LearningPathTreeNode $node)
    {
        $number = $this->automaticNumberingService->getAutomaticNumberingForLearningPathTreeNode($node);

        $nodeData = array();

        $nodeData['key'] = $node->getId();
        $nodeData['title'] = $node->getContentObject()->get_title();
        $nodeData['number'] = is_null($number) ? '' : $number;
        $nodeData['icon'] = $this->getItemIcon($node);

        if ($node->getContentObject() instanceof LearningPath || $node->getContentObject() instanceof Section)
        {
            $nodeData['folder'] = true;
        }

        if ($this->allowedToViewContentObject)
        {
            $nodeData['href'] = $this->getNodeUrl($node->getId());
        }
        else
        {
            $nodeData['href'] = '#';
        }

        if ($this->isSelectedItem($node))
        {
            $nodeData['active'] = true;
        }

        if ($node === $this->currentLearningPathTreeNode)
        {
            $nodeData['expanded'] = true;
        }

        if (!$node->isRootNode() && $node->getLearningPathChild()->isBlocked())
        {
            $nodeData['step_blocked'] = true;
        }

        if ($this->learningPathTrackingService &&
            $this->learningPathTrackingService->isLearningPathTreeNodeCompleted(
                $this->learningPath, $this->user, $node
            )
        )
        {
            $nodeData['completed'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->currentLearningPathTreeNode, $node->getDescendantNodes()))
            {
                $nodeData['expanded'] = true;
            }

            $nodeData['children'] = array();

            $children = $node->getChildNodes();
            foreach ($children as $child)
            {
                $nodeData['children'][] = $this->getNodeDataForLearningPathTreeNode($child);
            }
        }

        $actions = $this->nodeActionGenerator->generateNodeActions($node, $this->allowedToEditLearningPathTree);
        foreach ($actions as $action)
        {
            $nodeData['actions'][$action->getName()] = $action->toArray();
        }

        return $nodeData;
    }

    /**
     *
     * @param int $nodeIdentifier
     *
     * @return string
     */
    protected function getNodeUrl($nodeIdentifier)
    {
        return str_replace(self::NODE_PLACEHOLDER, $nodeIdentifier, $this->treeMenuUrl);
    }
}