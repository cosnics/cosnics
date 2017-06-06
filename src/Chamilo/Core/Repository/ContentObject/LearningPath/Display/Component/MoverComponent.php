<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MoverComponent extends BaseHtmlTreeComponent
{
    const PARAM_NEW_PARENT = 'new_parent';

    /**
     * Executes this component
     */
    public function build()
    {
        $selected_steps = $this->getRequest()->request->get(self::PARAM_CHILD_ID);
        if (empty($selected_steps))
        {
            $selected_steps = $this->getRequest()->query->get(self::PARAM_CHILD_ID);
        }

        if (empty($selected_steps))
        {
            throw new NoObjectSelectedException(Translation::getInstance()->getTranslation('Step'));
        }

        if (!is_array($selected_steps))
        {
            $selected_steps = array($selected_steps);
        }

        $path = $this->getTree();

        /** @var TreeNode[] $available_nodes */
        $available_nodes = array();

        foreach ($selected_steps as $selected_step)
        {
            try
            {
                $selected_node = $path->getTreeNodeById((int) $selected_step);

                if ($this->canEditTreeNode($selected_node->getParentNode()))
                {
                    $available_nodes[] = $selected_node;
                }
            }
            catch (\Exception $ex)
            {
                throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Step'), $selected_step);
            }
        }

        if (count($available_nodes) == 0)
        {
            throw new UserException(
                Translation::get(
                    'NoObjectsToMove',
                    array('OBJECTS' => Translation::get('Steps')),
                    Utilities::COMMON_LIBRARIES
                )
            );
        }

        $path = $this->getTree();
        $parents = $this->get_possible_parents($available_nodes);

        $form = new FormValidator('move', 'post', $this->get_url());

        if (count($available_nodes) > 1)
        {
            $form->addElement(
                'static',
                null,
                Translation::get('SelectedLearningPathItems'),
                $this->get_available_nodes($available_nodes)
            );
        }

        $parents_element = $form->addElement('select', self::PARAM_NEW_PARENT, Translation::get('NewParent'));

        foreach ($parents as $key => $parent)
        {
            $attributes = $parent[1] ? 'disabled' : '';
            $parents_element->addOption($parent[0], $key, $attributes);
        }
        $form->addElement('submit', 'submit', Translation::get('Move', null, Utilities::COMMON_LIBRARIES));

        if ($form->validate())
        {
            $selected_node_id = $form->exportValue(self::PARAM_NEW_PARENT);

            if (empty($selected_node_id))
            {
                throw new NoObjectSelectedException(Translation::getInstance()->getTranslation('NewParent'));
            }

            $parent_node = $path->getTreeNodeById((int) $selected_node_id);

            $failures = 0;
            $treeNodeDataService = $this->getTreeNodeDataService();
            $new_node = null;

            foreach ($available_nodes as $available_node)
            {
                try
                {
                    $treeNodeDataService->moveContentObjectToOtherLearningPath($available_node, $parent_node);

                    $new_node = $available_node;

                    Event::trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_MOVE_ITEM,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $available_node->getContentObject()->getId(),
                            Activity::PROPERTY_CONTENT => $available_node->getContentObject()->get_title()
                        )
                    );
                }
                catch (\Exception $ex)
                {
                    $failures ++;
                }
            }

            $parameters = array();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

            if ($failures > 0)
            {
                $parameters[self::PARAM_CHILD_ID] = $this->getCurrentTreeNode()->getParentNode()->getId();
            }
            else
            {
                $parameters[self::PARAM_CHILD_ID] = $new_node->getId();
            }

            $this->redirect(
                Translation::get(
                    $failures > 0 ? 'ObjectsNotMoved' : 'ObjectsMoved',
                    array('OBJECTS' => Translation::get('Steps')),
                    Utilities::COMMON_LIBRARIES
                ),
                $failures > 0,
                $parameters
            );
        }
        else
        {
            $variable = $this->getCurrentContentObject() instanceof LearningPath ? 'MoveFolder' : 'MoverComponent';

            $trail = BreadcrumbTrail::getInstance();
            $trail->add(new Breadcrumb($this->get_url(), Translation::get($variable)));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Render the list of available nodes as HTML
     *
     * @param TreeNode[] $available_nodes
     *
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
                $html[] = '<li>' . $available_node->getContentObject()->get_title();
            }

            $html[] = '</ul>';

            return implode(PHP_EOL, $html);
        }
        else
        {
            return '';
        }
    }

    /**
     * Get a list of possible parent nodes for the currently selected node(s)
     *
     * @param TreeNode[] $selectedNodes
     *
     * @return \string[]
     */
    private function get_possible_parents($selectedNodes = array())
    {
        $path = $this->getTree();
        $root = $path->getRoot();

        if (in_array($root, $selectedNodes))
        {
            $name = $root->getContentObject()->get_title() . ' (' . Translation::get('SelectedItem') . ')';
            $node_disabled = true;
        }
        else
        {
            $name = $root->getContentObject()->get_title();
            $node_disabled = false;
        }

        $parents = array($root->getId() => array($name, $node_disabled));
        $parents = $this->get_children_from_node($root, $selectedNodes, $node_disabled, $parents);

        return $parents;
    }

    /**
     * Get the possible parents for the current node based on the children of a given node
     *
     * @param TreeNode $node
     * @param string[] $parents
     * @param int $level
     *
     * @return string[]
     */
    private function get_children_from_node(
        TreeNode $node, $selectedNodes, $node_disabled, $parents, $level = 1
    )
    {
        $automaticNumberingService = $this->getAutomaticNumberingService();

        foreach ($node->getChildNodes() as $child)
        {
            if(!$child->getContentObject() instanceof Section)
            {
                continue;
            }

            $title = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($child);

            if (in_array($child, $selectedNodes))
            {
                $name = $title . ' (' . Translation::get('SelectedItem') . ')';
                $child_node_disabled = true;
            }
            else
            {
                $name = $title;
                $child_node_disabled = $node_disabled;
            }

            $name = str_repeat('--', $level) . ' ' . $name;

            $parents[$child->getId()] = array($name, $child_node_disabled);

            $parents =
                $this->get_children_from_node($child, $selectedNodes, $child_node_disabled, $parents, $level + 1);
        }

        return $parents;
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID);
    }
}
