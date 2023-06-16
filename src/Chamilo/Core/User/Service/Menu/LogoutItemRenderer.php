<?php
namespace Chamilo\Core\User\Service\Menu;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LogoutItemRenderer extends MenuItemRenderer
{
    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('sign-out-alt', ['fa-2x'], null, 'fas');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT]
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item): string
    {
        return $this->getTranslator()->trans('Logout', [], 'Chamilo\Core\User');
    }

    public function isSelected(Item $item): bool
    {
        return false;
    }
}