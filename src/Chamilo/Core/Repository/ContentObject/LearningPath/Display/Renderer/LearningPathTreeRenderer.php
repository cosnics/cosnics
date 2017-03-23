<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
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
     * @var LearningPathTree
     */
    protected $learningPathTree;

    /**
     *
     * @param LearningPathTree $learningPathTree
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(
        LearningPathTree $learningPathTree, Application $application,
        $treeMenuUrl, $menuName = 'bootstrap-tree-menu'
    )
    {
        $this->learningPathTree = $learningPathTree;

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
//            if ($node->is_completed())
//            {
//                return 'type_completed';
//            }
//            else
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
        return $this->getApplication()->get_action() != Manager::ACTION_REPORTING &&
            $this->getApplication()->getCurrentLearningPathChildId() == $node->getId();
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

        if ($application->get_action() == Manager::ACTION_REPORTING && !$application->is_current_step_set())
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
    public function getMenuItem(LearningPathTreeNode $node)
    {
        $application = $this->getApplication();

        $menuItem['text'] = $node->getContentObject()->get_title();
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

        if($node == $this->getApplication()->getCurrentLearningPathTreeNode())
        {
            $menuItem['state']['expanded'] = true;
        }

        if ($node->hasChildNodes())
        {
            if(in_array($this->getApplication()->getCurrentLearningPathTreeNode(), $node->getDescendantNodes()))
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