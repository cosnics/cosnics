<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Architecture\Interface\SelectableItemInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class AccountItemRenderer extends MenuItemRenderer implements SelectableItemInterface
{
    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('user', ['fa-fw']);
    }

    public function getRendererTypeName(): string
    {
        return $this->translator->trans('MyAccount', [], Manager::CONTEXT);
    }

    public function getUrl(): string
    {
        return $this->urlGenerator->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::ACCOUNT->value
            ]
        );
    }

    public function isSelected(Item $item, User $user): bool
    {
        $currentContext = $this->request->query->get(ApplicationInterface::PARAM_CONTEXT);
        $currentAction = $this->request->query->get(ApplicationInterface::PARAM_ACTION);

        return $currentContext == Manager::CONTEXT && $currentAction == ActionEnum::ACCOUNT->value;
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getRendererTypeName();
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->translator->trans('MyAccount', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}