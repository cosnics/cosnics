<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\Item\ApplicationItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryApplicationItemRenderer extends ApplicationItemRenderer
{

    /**
     * @param \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryApplicationItem $item
     *
     * @return boolean
     */
    public function isSelected(Item $item)
    {
        $request = $this->getRequest();

        $currentWorkspace = $request->query->get(
            \Chamilo\Core\Repository\Manager::PARAM_WORKSPACE_ID
        );

        $currentContext = $request->query->get(Application::PARAM_CONTEXT);

        return ($currentContext == $item->getApplication() && !isset($currentWorkspace));
    }
}