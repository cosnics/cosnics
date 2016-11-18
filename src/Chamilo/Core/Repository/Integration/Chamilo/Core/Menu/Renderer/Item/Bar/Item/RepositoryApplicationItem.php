<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\ApplicationItem;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryApplicationItem extends ApplicationItem
{

    public function isItemSelected()
    {
        $currentWorkspace = $this->getMenuRenderer()->getRequest()->get(
            \Chamilo\Core\Repository\Manager::PARAM_WORKSPACE_ID);
        
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application::PARAM_CONTEXT);
        
        return ($currentContext == $this->getItem()->get_application() && ! isset($currentWorkspace));
    }
}
