<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LogoutItemRenderer extends MenuItemRenderer
{
    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('sign-out-alt', array('fa-2x'), null, 'fas');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT)
        );

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $this->getTranslator()->trans('Logout', [], 'Chamilo\Core\User');
    }
}