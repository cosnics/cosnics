<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
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
     * @var User
     */
    protected $user;

    /**
     * @param LearningPathTree $learningPathTree
     * @param User $user
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param AutomaticNumberingService $automaticNumberingService
     * @param string $treeMenuUrl
     * @param LearningPathTreeNode $currentLearningPathTreeNode
     * @param bool $allowedToViewContentObject
     */
    public function __construct(
        LearningPathTree $learningPathTree, User $user,
        LearningPathTrackingService $learningPathTrackingService,
        AutomaticNumberingService $automaticNumberingService,
        $treeMenuUrl, LearningPathTreeNode $currentLearningPathTreeNode, $allowedToViewContentObject
    )
    {
        $this->learningPathTree = $learningPathTree;
        $this->user = $user;
        $this->learningPath = $learningPathTree->getRoot()->getContentObject();
        $this->learningPathTrackingService = $learningPathTrackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->treeMenuUrl = $treeMenuUrl;
        $this->currentLearningPathTreeNode = $currentLearningPathTreeNode;
        $this->allowedToViewContentObject = $allowedToViewContentObject;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(LearningPathTreeNode $node)
    {
        if ($this->allowedToViewContentObject)
        {
            if ($this->learningPathTrackingService->isLearningPathTreeNodeCompleted(
                $this->learningPath, $this->user, $node
            )
            )
            {
                return 'type_completed';
            }
            else
            {
                $objectType = (string) StringUtilities::getInstance()->createString(
                    ClassnameUtilities::getInstance()->getPackageNameFromNamespace($node->getContentObject()->package())
                )->underscored();

                return 'type_' . $objectType;
            }
        }
        else
        {
            return 'disabled type_disabled';
        }
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
        $menu = array();

        $menu[] = $this->getMenuItem($this->learningPathTree->getRoot());

        return $menu;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return \string[]
     */
    protected function getMenuItem(LearningPathTreeNode $node)
    {
        $number = $this->automaticNumberingService->getAutomaticNumberingForLearningPathTreeNode($node);

        $menuItem['key'] = $node->getId();
        $menuItem['title'] = $node->getContentObject()->get_title();
        $menuItem['number'] = is_null($number) ? '' : $number;
        $menuItem['icon'] = $this->getItemIcon($node);

        if ($this->allowedToViewContentObject)
        {
            $menuItem['href'] = $this->getNodeUrl($node->getId());
        }
        else
        {
            $menuItem['href'] = '#';
        }

        if ($this->isSelectedItem($node))
        {
            $menuItem['active'] = true;
        }

        if ($node === $this->currentLearningPathTreeNode)
        {
            $menuItem['expanded'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->currentLearningPathTreeNode, $node->getDescendantNodes()))
            {
                $menuItem['expanded'] = true;
            }

            $menuItem['children'] = array();

            $children = $node->getChildNodes();
            foreach ($children as $child)
            {
                $menuItem['children'][] = $this->getMenuItem($child);
            }
        }

        return $menuItem;
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