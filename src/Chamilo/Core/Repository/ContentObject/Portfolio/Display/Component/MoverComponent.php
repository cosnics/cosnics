<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the user to move an item or folder to another parent folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MoverComponent extends Manager implements DelegateComponent
{
    const PARAM_NEW_PARENT = 'new_parent';

    /**
     * Executes this component
     */
    public function run()
    {
        parent :: run();

        $selected_steps = Request :: get(self :: PARAM_STEP);
        if (! is_array($selected_steps))
        {
            $selected_steps = array($selected_steps);
        }

        $path = $this->get_root_content_object()->get_complex_content_object_path();

        $available_nodes = array();

        foreach ($selected_steps as $selected_step)
        {
            $selected_node = $path->get_node($selected_step);

            if ($this->get_parent()->is_allowed_to_edit_content_object($selected_node->get_parent()))
            {
                $available_nodes[] = $selected_node;
            }
        }

        if (count($available_nodes) == 0)
        {
            $parameters = array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                self :: PARAM_STEP => $this->get_current_node()->get_parent()->get_id());

            $this->redirect(
                Translation :: get(
                    'NoObjectsToMove',
                    array('OBJECTS' => Translation :: get('ComplexContentObjectItems')),
                    Utilities :: COMMON_LIBRARIES),
                true,
                $parameters);
        }

        $current_node = $this->get_current_node();
        $path = $this->get_root_content_object()->get_complex_content_object_path();
        $parents = $this->get_possible_parents();

        $form = new FormValidator('move', 'post', $this->get_url());

        if (count($available_nodes) > 1)
        {
            $form->addElement(
                'static',
                null,
                Translation :: get('SelectedPortfolioItems'),
                $this->get_available_nodes($available_nodes));
        }

        $form->addElement('select', self :: PARAM_NEW_PARENT, Translation :: get('NewParent'), $parents);
        $form->addElement('submit', 'submit', Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES));

        if ($form->validate())
        {
            $selected_node_id = $form->exportValue(self :: PARAM_NEW_PARENT);
            $parent_node = $path->get_node($selected_node_id);
            $parent_id = $parent_node->get_content_object()->get_id();

            $failures = 0;

            foreach ($available_nodes as $available_node)
            {
                $complex_content_object_item = $available_node->get_complex_content_object_item();
                $old_parent_id = $complex_content_object_item->get_parent();

                $parent_node_content_object_ids_path = $parent_node->get_parents_content_object_ids(true, true);
                $current_node_ids = array();
                $current_node_ids[] = $available_node->get_hash();

                foreach ($available_node->get_descendants() as $descendant)
                {
                    $current_node_ids[] = $descendant->get_hash();
                }

                if ($old_parent_id != $parent_id)
                {
                    $complex_content_object_item->set_parent($parent_id);
                    $complex_content_object_item->set_display_order(
                        \Chamilo\Core\Repository\Storage\DataManager :: select_next_display_order($parent_id));
                    if ($complex_content_object_item->update())
                    {
                        $new_content_object_ids_path = $parent_node_content_object_ids_path;
                        $new_content_object_ids_path[] = $available_node->get_content_object()->get_id();

                        $this->get_root_content_object()->get_complex_content_object_path()->reset();
                        $new_node = $this->get_root_content_object()->get_complex_content_object_path()->follow_path_by_content_object_ids(
                            $new_content_object_ids_path);

                        $new_node_ids = array();
                        $new_node_ids[] = $new_node->get_hash();

                        foreach ($new_node->get_descendants() as $descendant)
                        {
                            $new_node_ids[] = $descendant->get_hash();
                        }

                        Event :: trigger(
                            'activity',
                            \Chamilo\Core\Repository\Manager :: context(),
                            array(
                                Activity :: PROPERTY_TYPE => Activity :: ACTIVITY_MOVE_ITEM,
                                Activity :: PROPERTY_USER_ID => $this->get_user_id(),
                                Activity :: PROPERTY_DATE => time(),
                                Activity :: PROPERTY_CONTENT_OBJECT_ID => $available_node->get_content_object()->get_id(),
                                Activity :: PROPERTY_CONTENT => $available_node->get_content_object()->get_title()));

                        if (! \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataManager :: update_node_ids(
                            $current_node_ids,
                            $new_node_ids))
                        {
                            $failures ++;
                        }
                    }
                    else
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }
            }

            $parameters = array();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

            if ($failures > 0)
            {
                $parameters[self :: PARAM_STEP] = $this->get_current_node()->get_parent()->get_id();
            }
            else
            {
                $parameters[self :: PARAM_STEP] = $new_node->get_id();
            }

            $this->redirect(
                Translation :: get(
                    $failures > 0 ? 'ObjectsNotMoved' : 'ObjectsMoved',
                    array('OBJECTS' => Translation :: get('ComplexContentObjectItems')),
                    Utilities :: COMMON_LIBRARIES),
                $failures > 0,
                $parameters);
        }
        else
        {
            $variable = $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get($variable)));

            $this->get_tabs_renderer()->set_content($form->toHtml());

            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->get_tabs_renderer()->render();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    /**
     * Render the list of available nodes as HTML
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode[] $available_nodes
     * @return string
     */
    public function get_available_nodes($available_nodes)
    {
        if (count($available_nodes) > 1)
        {
            $html = array();

            $html[] = '<ul>';

            foreach ($available_nodes as $available_node)
            {
                $html[] = '<li>' . $available_node->get_content_object()->get_title();
            }

            $html[] = '</ul>';

            return implode("\n", $html);
        }
        else
        {
            return '';
        }
    }

    /**
     * Get a list of possible parent nodes for the currently selected node(s)
     *
     * @return string[]
     */
    private function get_possible_parents()
    {
        $path = $this->get_root_content_object()->get_complex_content_object_path();
        $root = $path->get_root();

        if ($root->get_id() == $this->get_current_node()->get_parent()->get_id())
        {
            $current = ' (' . Translation :: get('Current') . ')';
        }
        else
        {
            $current = '';
        }

        $parents = array(1 => $root->get_content_object()->get_title() . $current);
        $parents = $this->get_children_from_node($root, $parents);

        return $parents;
    }

    /**
     * Get the possible parents for the current node based on the children of a given node
     *
     * @param ComplexContentObjectPathNode $node
     * @param string[] $parents
     * @param int $level
     * @return string[]
     */
    private function get_children_from_node(ComplexContentObjectPathNode $node, $parents, $level = 1)
    {
        foreach ($node->get_children() as $child)
        {
            $content_object = $child->get_content_object();

            if (! $content_object instanceof ComplexContentObjectSupport)
            {
                continue;
            }

            if ($child->get_id() == $this->get_current_node()->get_parent()->get_id())
            {
                $current = ' (' . Translation :: get('Current') . ')';
            }
            else
            {
                $current = '';
            }

            $parents[$child->get_id()] = str_repeat('--', $level) . ' ' . $content_object->get_title() . $current;

            $parents = $this->get_children_from_node($child, $parents, $level + 1);
        }

        return $parents;
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }
}