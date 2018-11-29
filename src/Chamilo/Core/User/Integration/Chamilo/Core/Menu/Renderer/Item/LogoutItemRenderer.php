<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LogoutItemRenderer extends MenuItemRenderer
{
    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $this->getTranslator()->trans('Logout', [], 'Chamilo\Core\User');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => Manager::context(), Application::PARAM_ACTION => Manager::ACTION_LOGOUT)
        );

        return $redirect->getUrl();
    }
}