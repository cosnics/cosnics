<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component\Viewer;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use HTML_Menu;
use HTML_Menu_ArrayRenderer;

class Menu extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    private $context;

    public function __construct($context)
    {
        $this->context = $context;
       
        $this->path = $this->context->get_root_content_object()->get_complex_content_object_path();
        
        parent :: __construct($this->get_menu());
        
        if ($this->context->get_current_step())
        {
            $this->forceCurrentUrl($this->get_url($this->context->get_current_step()));
        }
    }

    public function get_menu()
    {
        $menu = array();
               
        $survey_item = array();
        $survey_item['title'] = $this->path->get_root()->get_content_object()->get_title();
        $survey_item['class'] = 'type_' . $this->path->get_root()->get_content_object()->get_type();
        $survey_item['url'] = $this->get_url($this->path->get_root()->get_id());
        
        $sub_items = $this->get_menu_items($this->path->get_root());
        
        if (count($sub_items) > 0)
        {
            $survey_item['sub'] = $sub_items;
        }
        
        $menu[] = $survey_item;
        
        return $menu;
    }

    public function get_menu_items(ComplexContentObjectPathNode $parent)
    {
        $menu = array();
        
        $children = $parent->get_children();
        
        foreach ($children as $child)
        {
            if ($child->in_menu())
            {
                $menu_item = array();
                
                $menu_item['title'] = $child->get_content_object()->get_title();
                $menu_item['class'] = 'type_' . $child->get_content_object()->get_type();
                $menu_item['url'] = $this->get_url($child->get_id());
                if ($child->has_children())
                {
                    $menu_item['sub'] = $this->get_menu_items($child);
                }
                
                $menu[] = $menu_item;
            }
        }
        
        return $menu;
    }

    public function get_url($step)
    {
        return $this->context->get_url(array(Manager :: PARAM_STEP => $step));
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
    }

    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
}