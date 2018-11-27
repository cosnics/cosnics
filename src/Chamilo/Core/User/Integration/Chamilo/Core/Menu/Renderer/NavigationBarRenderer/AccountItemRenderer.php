<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\NavigationBarRenderer;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\NavigationBarRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccountItemRenderer extends MenuItemRenderer
{
    /**
     * @return string
     */
    public function getUrl()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::context(),
                Application::PARAM_ACTION => Manager::ACTION_VIEW_ACCOUNT
            )
        );

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function isSelected(Item $item)
    {
        $currentContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);
        $currentAction = $this->getRequest()->query->get(Manager::PARAM_ACTION);

        return $currentContext == Manager::package() && $currentAction == Manager::ACTION_VIEW_ACCOUNT;
    }
}