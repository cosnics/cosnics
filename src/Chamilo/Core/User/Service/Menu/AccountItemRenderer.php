<?php
namespace Chamilo\Core\User\Service\Menu;

use Chamilo\Core\Menu\Architecture\Interfaces\SelectableItemInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccountItemRenderer extends MenuItemRenderer implements SelectableItemInterface
{

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('user', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('MyAccount', [], Manager::CONTEXT);
    }

    public function getUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW_ACCOUNT
            ]
        );
    }

    public function isSelected(Item $item, User $user): bool
    {
        $currentContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);
        $currentAction = $this->getRequest()->query->get(Application::PARAM_ACTION);

        return $currentContext == Manager::CONTEXT && $currentAction == Manager::ACTION_VIEW_ACCOUNT;
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getRendererTypeName();
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->getTranslator()->trans('MyAccount', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}