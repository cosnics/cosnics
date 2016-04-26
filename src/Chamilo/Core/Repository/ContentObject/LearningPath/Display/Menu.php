<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Menu
{

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
    }

    /**
     * Get the actual menu contents
     *
     * @return string[]
     */
    public function getNodes()
    {
        $learning_path_id = $this->context->get_root_content_object_id();

        $menu = array();

        $learning_path_item = array();
        $learning_path_item['text'] = $this->path->get_root()->get_content_object()->get_title();

        $objectType = (string) StringUtilities :: getInstance()->createString(
            ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                $this->path->get_root()->get_content_object()->package()))->underscored();

        $learning_path_item['icon'] = 'type_' . $objectType;
        $learning_path_item['href'] = $this->getUrl($this->path->get_root()->get_id());

        if ($this->context->get_current_step() == $this->path->get_root()->get_id())
        {
            $learning_path_item['state'] = array('selected' => true);
        }

        $sub_items = $this->getSubNodes($this->path->get_root());

        if (count($sub_items) > 0)
        {
            $learning_path_item['nodes'] = $sub_items;
        }

        $menu[] = $learning_path_item;

        $progress_item = array();
        $progress_item['text'] = Translation :: get('Progress');
        $progress_item['href'] = $this->context->get_url(
            array(Manager :: PARAM_ACTION => Manager :: ACTION_REPORTING, Manager :: PARAM_STEP => null));
        $progress_item['icon'] = 'type_statistics';

        if ($this->context->get_action() == Manager :: ACTION_REPORTING && ! $this->context->is_current_step_set())
        {
            $progress_item['state'] = array('selected' => true);
        }

        $menu[] = $progress_item;

        return $menu;
    }

    /**
     * Get the menu items for a given ComplexContentObjectPathNode
     *
     * @param ComplexContentObjectPathNode $parent
     * @return string[]
     */
    public function getSubNodes(ComplexContentObjectPathNode $parent)
    {
        $menu = array();

        $children = $parent->get_children();

        foreach ($children as $child)
        {
            $menu_item = array();

            $menu_item['text'] = $child->get_content_object()->get_title();

            if ($this->context->get_parent()->is_allowed_to_view_content_object($child))
            {
                $objectType = (string) StringUtilities :: getInstance()->createString(
                    ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                        $child->get_content_object()->package()))->underscored();

                $menu_item['href'] = $this->getUrl($child->get_id());
                $menu_item['icon'] = 'type_' . $objectType;
            }
            else
            {
                $menu_item['href'] = '#';
                $menu_item['icon'] = 'disabled type_disabled';
            }

            if ($child->is_completed())
            {
                $menu_item['icon'] = 'type_completed';
            }

            $menu_item['state'] = array();

            if ($this->context->get_current_step() == $child->get_id())
            {
                $menu_item['state']['selected'] = true;
            }

            if ($child->has_children())
            {
                $menu_item['nodes'] = $this->getSubNodes($child);
            }

            $menu[] = $menu_item;
        }

        return $menu;
    }

    /**
     * Get the URL of the learning_path step
     *
     * @param int $step
     * @return string
     */
    public function getUrl($step)
    {
        return str_replace('__STEP__', $step, $this->context->get_parent()->get_learning_path_tree_menu_url());
    }

    /**
     * Render the tree as HTML
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div id="learning_path_menu">';
        $html[] = '</div>';

        $html[] = "<script>
            $(function()
            {
                $(document).ready(function()
                {
                    $('#learning_path_menu').treeview({
                        enableLinks : true,
                        expandIcon: 'glyphicon glyphicon-chevron-right',
                        collapseIcon: 'glyphicon glyphicon-chevron-down',
                        color: '#428bca',
                        showBorder: false,
                        checkedIcon: 'glyphicon glyphicon-ok',
                        data: " . json_encode($this->getNodes()) . "
                    });
                });
            });
        </script>";

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) .
                 'Plugin/Bootstrap/treeview/dist/bootstrap-treeview.min.js');

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) .
                 'Plugin/Bootstrap/treeview/dist/bootstrap-treeview.min.css');

        return implode(PHP_EOL, $html);
    }
}
