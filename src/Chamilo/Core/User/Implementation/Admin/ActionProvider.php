<?php
namespace Chamilo\Core\User\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

class ActionProvider extends AbstractActionProvider implements ActionProviderInterface
{
    public function getActions(): Actions
    {
        $context = $this->getContext();

        $links = [];

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
        ];

        $links[] = new Action(
            $this->translator->trans('ListDescription', [], $context), $this->translator->trans('List', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::CREATE->value
        ];

        $links[] = new Action(
            $this->translator->trans('CreateDescription', [], $context),
            $this->translator->trans('Create', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
        ];

        return new Actions($context, $links, $this->urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}