<?php
namespace Chamilo\Core\Repository\Viewer\Menu;

use Chamilo\Core\Repository\Menu\SharedRepositoryCategoryTreeMenu;
use Chamilo\Core\Repository\Viewer\Component\BrowserComponent;

/**
 *
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class RepositorySharedCategoryMenu extends SharedRepositoryCategoryTreeMenu
{

    /**
     * Returns the url of a node
     * 
     * @param int $node_id
     * @return string
     */
    public function get_node_url($node_id)
    {
        $parameters = array();
        $parameters[\Chamilo\Core\Repository\Manager :: PARAM_SHARED_CATEGORY_ID] = $node_id;
        $parameters[BrowserComponent :: SHARED_BROWSER] = 1;
        
        return $this->get_parent()->get_url($parameters);
    }
}
