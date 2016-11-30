<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * The portfolio structure represented as a tree-menu
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Menu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     *
     * @var Manager
     */
    private $context;

    /**
     * Constructor
     * 
     * @param Manager $context
     */
    public function __construct(Manager $context)
    {
        $this->context = $context;
        $this->path = $this->context->get_root_content_object()->get_complex_content_object_path();
        
        parent::__construct($this->get_menu());
        
        if ($this->context->get_current_step())
        {
            $this->forceCurrentUrl($this->get_url($this->context->get_current_step()));
        }
    }

    /**
     * Get the actual menu contents
     * 
     * @return string[]
     */
    public function get_menu()
    {
        $frequently_asked_questions_id = $this->context->get_root_content_object_id();
        
        $menu = array();
        
        $frequently_asked_questions_item = array();
        $frequently_asked_questions_item['title'] = $this->path->get_root()->get_content_object()->get_title();
        $frequently_asked_questions_item['class'] = 'type_' . StringUtilities::getInstance()->createString(
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                $this->path->get_root()->get_content_object()->package()))->underscored();
        $frequently_asked_questions_item['url'] = $this->get_url($this->path->get_root()->get_id());
        
        $sub_items = $this->get_menu_items($this->path->get_root());
        
        if (count($sub_items) > 0)
        {
            $frequently_asked_questions_item['sub'] = $sub_items;
        }
        
        $menu[] = $frequently_asked_questions_item;
        
        return $menu;
    }

    /**
     * Get the menu items for a given ComplexContentObjectPathNode
     * 
     * @param ComplexContentObjectPathNode $parent
     * @return string[]
     */
    public function get_menu_items(ComplexContentObjectPathNode $parent)
    {
        $menu = array();
        
        $children = $parent->get_children();
        
        foreach ($children as $child)
        {
            $menu_item = array();
            
            $menu_item['title'] = $child->get_content_object()->get_title();
            
            if ($this->context->get_parent()->is_allowed_to_view_content_object($child))
            {
                $menu_item['url'] = $this->get_url($child->get_id());
                $menu_item['class'] = 'type_' . StringUtilities::getInstance()->createString(
                    ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                        $child->get_content_object()->package()))->underscored();
            }
            else
            {
                $menu_item['url'] = '#';
                $menu_item['class'] = 'disabled type_disabled';
            }
            
            if ($child->has_children())
            {
                $menu_item['sub'] = $this->get_menu_items($child);
            }
            
            $menu[] = $menu_item;
        }
        
        return $menu;
    }

    /**
     * Get the URL of the portfolio step
     * 
     * @param int $step
     * @return string
     */
    public function get_url($step)
    {
        return sprintf($this->context->get_parent()->get_frequently_asked_questions_tree_menu_url(), $step);
    }

    /**
     * Get the tree name based on the classname
     * 
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }

    /**
     * Render the tree as HTML
     * 
     * @return string
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}
