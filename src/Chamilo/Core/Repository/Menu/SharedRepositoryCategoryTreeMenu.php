<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class provides a navigation menu to allow a user to browse through
 * repository categories for shared objects
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SharedRepositoryCategoryTreeMenu extends RepositoryCategoryTreeMenu
{

    /**
     * **************************************************************************************************************
     * Inherited functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the url of a node
     * 
     * @param int $node_id
     *
     * @return string
     */
    public function get_node_url($node_id)
    {
        $url_param[Manager :: PARAM_ACTION] = Manager :: ACTION_BROWSE_SHARED_CONTENT_OBJECTS;
        $url_param[Manager :: PARAM_SHARED_CATEGORY_ID] = null;
        $url_param[Manager :: PARAM_SHARED_VIEW] = null;
        
        return $this->get_parent()->get_url($url_param) . '&' . Manager :: PARAM_SHARED_CATEGORY_ID . '=' . $node_id;
    }

    /**
     * Returns the current selected node id
     * 
     * @return int
     */
    public function get_current_node_id()
    {
        return Request :: get(Manager :: PARAM_SHARED_CATEGORY_ID);
    }

    /**
     * Returns the title of the repository category tree
     * 
     * @return string
     */
    public function get_root_node_title()
    {
        return Translation :: get('ContentObjectsSharedWithMe');
    }

    /**
     * Returns the category type
     * 
     * @return int
     */
    protected function get_type()
    {
        return RepositoryCategory :: TYPE_SHARED;
    }
}
