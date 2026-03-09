<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

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
                Application::PARAM_ACTION => ActionEnum::ACCOUNT->value
            ]
        );
    }

    public function isSelected(Item $item, User $user): bool
    {
        $currentContext = $this->getRequest()->query->get(Application::PARAM_CONTEXT);
        $currentAction = $this->getRequest()->query->get(Application::PARAM_ACTION);

        return $currentContext == Manager::CONTEXT && $currentAction == ActionEnum::ACCOUNT->value;
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