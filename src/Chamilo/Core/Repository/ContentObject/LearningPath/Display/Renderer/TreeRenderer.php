<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\BootstrapTreeMenu;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Renders a learning path tree
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeRenderer extends BootstrapTreeMenu
{
    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var LearningPathTrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * @param Tree $tree
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param AutomaticNumberingService $automaticNumberingService
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(
        Tree $tree, Application $application,
        LearningPathTrackingService $learningPathTrackingService,
        AutomaticNumberingService $automaticNumberingService,
        $treeMenuUrl, $menuName = 'bootstrap-tree-menu'
    )
    {
        $this->tree = $tree;
        $this->learningPath = $tree->getRoot()->getContentObject();
        $this->learningPathTrackingService = $learningPathTrackingService;
        $this->automaticNumberingService = $automaticNumberingService;

        parent::__construct($application, $treeMenuUrl, $menuName);
    }

    /**
     * @return int
     */
    public function getCurrentNodeId()
    {
        return 999999999; //return $this->getApplication()->getCurrentTreeNode()->getStep() - 1;
    }

    /**
     * @param TreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(TreeNode $node)
    {
        if ($this->getApplication()->get_parent()->is_allowed_to_view_content_object($node))
        {
            if ($this->learningPathTrackingService->isTreeNodeCompleted(
                $this->learningPath, $this->getApplication()->getUser(), $node
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
     * @param TreeNode $node
     *
     * @return boolean
     */
    protected function isSelectedItem(TreeNode $node)
    {
        return $this->getApplication()->getCurrentLearningPathChildId() == $node->getId();
    }

    /**
     * @return string[]
     */
    protected function getExtraMenuItems()
    {
        $application = $this->getApplication();
        $extraMenuItems = array();

        $progressItem = array();
        $progressItem['text'] = Translation::getInstance()->getTranslation('Progress');
        $progressItem['href'] = $application->get_url(
            array(Manager::PARAM_ACTION => Manager::ACTION_REPORTING, Manager::PARAM_CHILD_ID => null)
        );
        $progressItem['icon'] = 'type_statistics';

        if ($application->get_action() == Manager::ACTION_REPORTING && !$application->isCurrentLearningPathChildIdSet())
        {
            $progressItem['state'] = array('selected' => true);
        }

        $extraMenuItems[] = $progressItem;

        return $extraMenuItems;
    }

    /**
     * @return string[]
     */
    public function getNodes()
    {
        $menu = array();

        $menu[] = $this->getMenuItem($this->tree->getRoot());

        foreach ($this->getExtraMenuItems() as $extraMenuItem)
        {
            $menu[] = $extraMenuItem;
        }

        return $menu;
    }

    /**
     * @param TreeNode $node
     *
     * @return \string[]
     */
    public function getMenuItem(TreeNode $node)
    {
        $application = $this->getApplication();

        $title = $node->getContentObject()->get_title();
        $title = $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($node);

        $menuItem['text'] = $title;
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
            $menuItem['state'] = array('selected' => true);
        }

        if ($node == $this->getApplication()->getCurrentTreeNode())
        {
            $menuItem['state']['expanded'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->getApplication()->getCurrentTreeNode(), $node->getDescendantNodes()))
            {
                $menuItem['state']['expanded'] = true;
            }

            $menuItem['nodes'] = array();

            $children = $node->getChildNodes();
            foreach ($children as $child)
            {
                $menuItem['nodes'][] = $this->getMenuItem($child);
            }
        }

        return $menuItem;
    }
}