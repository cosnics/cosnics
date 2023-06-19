<?php
namespace Chamilo\Core\User\Service\Menu;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LogoutItemRenderer extends MenuItemRenderer
{

    public function getGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('sign-out-alt', ['fa-2x'], null, 'fas');
    }

    public function getUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT]
        );
    }

    public function renderTitle(): string
    {
        return $this->getTranslator()->trans('Logout', [], 'Chamilo\Core\User');
    }
}