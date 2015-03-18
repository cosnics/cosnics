<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use HTML_Menu;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Menu extends HTML_Menu
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
        $this->path = $this->context->get_complex_content_object_path();

        parent :: __construct($this->get_menu());

        if ($this->context->get_current_step())
        {
            $this->forceCurrentUrl($this->get_url($this->context->get_current_step()));
        }

        if ($this->context->get_action() == Manager :: ACTION_REPORTING && ! $this->context->is_current_step_set())
        {
            $this->forceCurrentUrl($this->get_reporting_url());
        }
    }

    /**
     * Get the actual menu contents
     *
     * @return string[]
     */
    public function get_menu()
    {
        $learning_path_id = $this->context->get_root_content_object_id();

        $menu = array();

        $learning_path_item = array();
        $learning_path_item['title'] = $this->get_title($this->path->get_root());
        $learning_path_item['class'] = 'type_' .
             ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                    $this->path->get_root()->get_content_object()->get_type()));
        $learning_path_item['url'] = $this->get_url($this->path->get_root()->get_id());

        $sub_items = $this->get_menu_items($this->path->get_root());

        if (count($sub_items) > 0)
        {
            $learning_path_item['sub'] = $sub_items;
        }

        $menu[] = $learning_path_item;

        $progress_item = array();
        $progress_item['title'] = Translation :: get('Progress');
        $progress_item['url'] = $this->get_reporting_url();
        $progress_item['class'] = 'type_statistics';

        $menu[] = $progress_item;

        return $menu;
    }

    public function get_reporting_url()
    {
        return $this->context->get_url(
            array(Manager :: PARAM_ACTION => Manager :: ACTION_REPORTING, Manager :: PARAM_STEP => null));
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

            $menu_item['title'] = $this->get_title($child);

            if ($this->context->get_parent()->is_allowed_to_view_content_object($child))
            {
                $menu_item['url'] = $this->get_url($child->get_id());
                $menu_item['class'] = 'type_' .
                     ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                        ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                            $child->get_content_object()->get_type()));
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

    public function get_title($node)
    {
        if ($node->is_completed())
        {
            return $node->get_content_object()->get_title() . Theme :: getInstance()->getCommonImage('Status/OkMini');
        }
        else
        {
            return $node->get_content_object()->get_title();
        }
    }

    /**
     * Get the URL of the learning_path step
     *
     * @param int $step
     * @return string
     */
    public function get_url($step)
    {
        return str_replace('__STEP__', $step, $this->context->get_parent()->get_learning_path_tree_menu_url());
    }

    /**
     * Get the tree name based on the classname
     *
     * @return string
     */
    public static function get_tree_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
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
