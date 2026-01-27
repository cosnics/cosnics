<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LogoutItemRenderer extends MenuItemRenderer
{

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('sign-out-alt');
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('Logout', [], Manager::CONTEXT);
    }

    public function getUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT]
        );
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getRendererTypeName();
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->getTranslator()->trans('Logout', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}