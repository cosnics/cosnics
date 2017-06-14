<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TrackingService;
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
class TreeJSONMapper
{
    const NODE_PLACEHOLDER = '__NODE__';

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * @var string
     */
    protected $treeMenuUrl;

    /**
     * @var TreeNode
     */
    protected $currentTreeNode;

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
    protected $allowedToEditTree;

    /**
     * @param Tree $tree
     * @param User $user
     * @param TrackingService $trackingService
     * @param AutomaticNumberingService $automaticNumberingService
     * @param NodeActionGenerator $nodeActionGenerator
     * @param string $treeMenuUrl
     * @param TreeNode $currentTreeNode
     * @param bool $allowedToViewContentObject
     */
    public function __construct(
        Tree $tree, User $user,
        TrackingService $trackingService = null,
        AutomaticNumberingService $automaticNumberingService,
        NodeActionGenerator $nodeActionGenerator,
        $treeMenuUrl, TreeNode $currentTreeNode,
        $allowedToViewContentObject, $allowedToEditTree = false
    )
    {
        $this->tree = $tree;
        $this->user = $user;
        $this->learningPath = $tree->getRoot()->getContentObject();
        $this->trackingService = $trackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->nodeActionGenerator = $nodeActionGenerator;
        $this->treeMenuUrl = $treeMenuUrl;
        $this->currentTreeNode = $currentTreeNode;
        $this->allowedToViewContentObject = $allowedToViewContentObject;
        $this->allowedToEditTree = $allowedToEditTree;
    }

    /**
     * @param TreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(TreeNode $node)
    {
        $objectType = (string) StringUtilities::getInstance()->createString(
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace($node->getContentObject()->package())
        )->underscored();

        $class = 'type_' . $objectType;

        if ($this->trackingService &&
            $this->trackingService->isTreeNodeCompleted(
                $this->learningPath, $this->user, $node
            )
        )
        {
            $class .= ' type_completed';
        }

        return $class;
    }

    /**
     * @param TreeNode $node
     *
     * @return boolean
     */
    protected function isSelectedItem(TreeNode $node)
    {
        return $this->currentTreeNode->getId() == $node->getId();
    }

    /**
     * @return string[]
     */
    public function getNodes()
    {
        $nodeData = array();

        $nodeData[] = $this->getNodeDataForTreeNode($this->tree->getRoot());

        return $nodeData;
    }

    /**
     * @param TreeNode $node
     *
     * @return \string[]
     */
    protected function getNodeDataForTreeNode(TreeNode $node)
    {
        $number = $this->automaticNumberingService->getAutomaticNumberingForTreeNode($node);

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

        if ($node === $this->currentTreeNode)
        {
            $nodeData['expanded'] = true;
        }

        if (!$node->isRootNode() && $node->getTreeNodeData()->isBlocked())
        {
            $nodeData['step_blocked'] = true;
        }

        if ($this->trackingService &&
            $this->trackingService->isTreeNodeCompleted(
                $this->learningPath, $this->user, $node
            )
        )
        {
            $nodeData['completed'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->currentTreeNode, $node->getDescendantNodes()))
            {
                $nodeData['expanded'] = true;
            }

            $nodeData['children'] = array();

            $children = $node->getChildNodes();
            foreach ($children as $child)
            {
                $nodeData['children'][] = $this->getNodeDataForTreeNode($child);
            }
        }

        $actions = $this->nodeActionGenerator->generateNodeActions($node, $this->allowedToEditTree);
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