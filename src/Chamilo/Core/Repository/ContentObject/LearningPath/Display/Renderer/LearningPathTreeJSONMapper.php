<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\Application;
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
     * @var Application
     */
    protected $application;

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
     * @param LearningPathTree $learningPathTree
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param AutomaticNumberingService $automaticNumberingService
     * @param string $treeMenuUrl
     */
    public function __construct(
        LearningPathTree $learningPathTree, Application $application,
        LearningPathTrackingService $learningPathTrackingService,
        AutomaticNumberingService $automaticNumberingService,
        $treeMenuUrl
    )
    {
        $this->learningPathTree = $learningPathTree;
        $this->application = $application;
        $this->learningPath = $learningPathTree->getRoot()->getContentObject();
        $this->learningPathTrackingService = $learningPathTrackingService;
        $this->automaticNumberingService = $automaticNumberingService;
        $this->treeMenuUrl = $treeMenuUrl;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(LearningPathTreeNode $node)
    {
        if ($this->application->get_parent()->is_allowed_to_view_content_object($node))
        {
            if ($this->learningPathTrackingService->isLearningPathTreeNodeCompleted(
                $this->learningPath, $this->application->getUser(), $node
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
        return $this->application->getCurrentLearningPathChildId() == $node->getId();
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
        $application = $this->application;

        $title = $this->automaticNumberingService->getAutomaticNumberedTitleForLearningPathTreeNode($node);

        $menuItem['key'] = $node->getId();
        $menuItem['title'] = $title;
        $menuItem['icon'] = $this->getItemIcon($node);

        if ($application->get_parent()->is_allowed_to_view_content_object($node))
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

        if ($node == $this->application->getCurrentLearningPathTreeNode())
        {
            $menuItem['expanded'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->application->getCurrentLearningPathTreeNode(), $node->getDescendantNodes()))
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