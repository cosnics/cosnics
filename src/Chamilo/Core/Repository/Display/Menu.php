<?php
namespace Chamilo\Core\Repository\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Menu\BootstrapTreeMenu;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class Menu extends BootstrapTreeMenu
{
    /**
     *
     * @var \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath
     */
    private $complexContentObjectPath;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath $complexContentObjectPath
     * @param string $treeMenuUrl
     * @param string $menuName
     */
    public function __construct(Application $application, ComplexContentObjectPath $complexContentObjectPath,
        $treeMenuUrl, $menuName = 'bootstrap-tree-menu')
    {
        $this->complexContentObjectPath = $complexContentObjectPath;
        
        parent::__construct($application, $treeMenuUrl, $menuName);
    }

    /**
     * @return mixed
     */
    public function getComplexContentObjectPath()
    {
        return $this->complexContentObjectPath;
    }

    /**
     * @param mixed $complexContentObjectPath
     */
    public function setComplexContentObjectPath($complexContentObjectPath)
    {
        $this->complexContentObjectPath = $complexContentObjectPath;
    }
    
    /**
     *
     * @see \Chamilo\Libraries\Format\Menu\BootstrapTreeMenu::getCurrentNodeId()
     */
    public function getCurrentNodeId()
    {
        return $this->getApplication()->get_current_step() - 1;
    }

    public function getNodes()
    {
        $menu = array();
        
        $menu[] = $this->getMenuItem($this->getComplexContentObjectPath()->get_root());
        
        foreach ($this->getExtraMenuItems() as $extraMenuItem)
        {
            $menu[] = $extraMenuItem;
        }
        
        return $menu;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $parent
     * @return string[]
     */
    public function getMenuItem(ComplexContentObjectPathNode $node)
    {
        $application = $this->getApplication();
        
        $menuItem['text'] = $node->get_content_object()->get_title();
        $menuItem['node-id'] = $node->get_id();
        $menuItem['icon'] = $this->getItemIcon($node);
        
        if ($application->get_parent()->is_allowed_to_view_content_object($node))
        {
            $menuItem['href'] = $this->getNodeUrl($node->get_id());
        }
        else
        {
            $menuItem['href'] = '#';
        }
        
        if ($this->isSelectedItem($node))
        {
            $menuItem['state'] = array('selected' => true);
        }
        
        if ($node->has_children())
        {
            $menuItem['nodes'] = array();
            
            $children = $node->get_children();
            
            foreach ($children as $child)
            {
                $menuItem['nodes'][] = $this->getMenuItem($child);
            }
        }
        
        return $menuItem;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     * @return string
     */
    protected function getItemIcon(ComplexContentObjectPathNode $node)
    {
        $objectType = (string) StringUtilities::getInstance()->createString(
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace($node->get_content_object()->package()))->underscored();
        
        return 'type_' . $objectType;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
    protected function isSelectedItem(ComplexContentObjectPathNode $node)
    {
        return $this->getApplication()->get_current_step() == $node->get_id();
    }

    /**
     *
     * @return string[]
     */
    protected function getExtraMenuItems()
    {
        return array();
    }
}