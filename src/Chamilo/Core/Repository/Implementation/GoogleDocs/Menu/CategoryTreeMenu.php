<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Menu;

use Chamilo\Core\Repository\Implementation\GoogleDocs\DataConnector;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Menu\TreeMenu\GenericTree;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * This class provides a navigation menu to allow a user to browse through repository categories
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CategoryTreeMenu extends GenericTree
{
    const ROOT_NODE_CLASS = 'category';
    const CATEGORY_CLASS = 'category';

    /**
     *
     * @var \Chamilo\Core\Repository\Manager
     */
    private $connector;

    private $additional_items;

    /**
     * Creates a new category navigation menu.
     *
     * @param $parent - the parent component
     * @param array $additional_items An array of extra tree items, added to the root.
     */
    public function __construct(DataConnector $connector, $additional_items = array())
    {
        $this->connector = $connector;
        $this->additional_items = $additional_items;

        parent :: __construct();
    }

    /**
     * Builds the tree and adds additional items
     */
    public function build_tree()
    {
        parent :: build_tree();

        foreach ($this->additional_items as $additional_item)
        {
            $this->tree[] = $additional_item;
        }
    }

    /**
     * Returns the url of a node
     *
     * @param int $node_id
     *
     * @return string
     */
    public function get_node_url($node_id)
    {
        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = Manager :: context();
        $parameters[Manager :: PARAM_EXTERNAL_REPOSITORY] = $this->get_connector()->get_external_repository_instance_id();
        $parameters[Manager :: PARAM_FOLDER] = $node_id;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    public function get_current_node_id()
    {
        return Request :: get(Manager :: PARAM_FOLDER);
    }

    public function get_node($node_id)
    {
        return $this->get_connector()->retrieve_folder($node_id);
    }

    public function get_node_children($parent_node_id)
    {
        return $this->get_connector()->retrieve_my_folders($parent_node_id);
    }

    public function node_has_children($parent_node_id)
    {
        return $this->get_node_children($parent_node_id)->size() > 0;
    }

    public function get_search_url()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Implementation\GoogleDocs\Ajax\Manager :: package(),
                \Chamilo\Core\Repository\Implementation\GoogleDocs\Ajax\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Implementation\GoogleDocs\Ajax\Manager :: ACTION_CATEGORY_MENU_FEED));
        return $redirect->getUrl();
    }

    public function get_url_format()
    {
        return $this->get_node_url('%d');
    }

    public function get_root_node_class()
    {
        return self :: ROOT_NODE_CLASS;
    }

    public function get_node_class($node)
    {
        return self :: CATEGORY_CLASS;
    }

    public function get_root_node_id()
    {
        return 'root';
    }

    public function get_root_node_title()
    {
        return 'my drive';
    }

    public function get_node_title($node)
    {
        return $node->getTitle();
    }

    public function get_node_safe_title($node)
    {
        return $this->get_node_title($node);
    }

    public function get_node_id($node)
    {
        return $node->getId();
    }

    public function get_node_parent($node)
    {
        return $node->getParent();
    }

    public function get_connector()
    {
        return $this->connector;
    }
}
