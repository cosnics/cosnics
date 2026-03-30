<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class LogoutItemRenderer extends MenuItemRenderer
{
    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('sign-out-alt');
    }

    public function getRendererTypeName(): string
    {
        return $this->translator->trans('Logout', [], Manager::CONTEXT);
    }

    public function getUrl(): string
    {
        return $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::LOGOUT->value
            ]
        );
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getRendererTypeName();
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->translator->trans('Logout', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}