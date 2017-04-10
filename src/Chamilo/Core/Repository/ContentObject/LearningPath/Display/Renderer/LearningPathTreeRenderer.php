<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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
class LearningPathTreeRenderer extends BootstrapTreeMenu
{
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
     *
     * @param LearningPathTree $learningPathTree
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(
        LearningPathTree $learningPathTree, Application $application,
        LearningPathTrackingService $learningPathTrackingService,
        $treeMenuUrl, $menuName = 'bootstrap-tree-menu'
    )
    {
        $this->learningPathTree = $learningPathTree;
        $this->learningPath = $learningPathTree->getRoot()->getContentObject();
        $this->learningPathTrackingService = $learningPathTrackingService;

        parent::__construct($application, $treeMenuUrl, $menuName);
    }

    /**
     * @return int
     */
    public function getCurrentNodeId()
    {
        return 999999999; //return $this->getApplication()->getCurrentLearningPathTreeNode()->getStep() - 1;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return string
     */
    protected function getItemIcon(LearningPathTreeNode $node)
    {
        if ($this->getApplication()->get_parent()->is_allowed_to_view_content_object($node))
        {
            if ($this->learningPathTrackingService->isLearningPathTreeNodeCompleted(
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
     * @param LearningPathTreeNode $node
     *
     * @return boolean
     */
    protected function isSelectedItem(LearningPathTreeNode $node)
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

        $menu[] = $this->getMenuItem($this->learningPathTree->getRoot());

        foreach ($this->getExtraMenuItems() as $extraMenuItem)
        {
            $menu[] = $extraMenuItem;
        }

        return $menu;
    }

    /**
     * @param LearningPathTreeNode $node
     *
     * @return \string[]
     */
    public function getMenuItem(LearningPathTreeNode $node, $counter = 1, $prefix = '')
    {
        $application = $this->getApplication();

        $title = $node->getContentObject()->get_title();

        if($this->learningPath->usesAutomaticNumbering())
        {
            if ($prefix)
            {
                $prefix = $prefix . '.' . $counter;
            }
            else
            {
                $prefix = $counter;
            }

            $title = $prefix . '. ' . $title;
        }

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

        if ($node == $this->getApplication()->getCurrentLearningPathTreeNode())
        {
            $menuItem['state']['expanded'] = true;
        }

        if ($node->hasChildNodes())
        {
            if (in_array($this->getApplication()->getCurrentLearningPathTreeNode(), $node->getDescendantNodes()))
            {
                $menuItem['state']['expanded'] = true;
            }

            $menuItem['nodes'] = array();

            $children = $node->getChildNodes();

            $counter = 1;
            foreach ($children as $child)
            {
                $menuItem['nodes'][] = $this->getMenuItem($child, $counter, $prefix);
                $counter ++;
            }
        }

        return $menuItem;
    }
}