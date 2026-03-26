<?php
namespace Chamilo\Core\Menu\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

/**
 * @package Chamilo\Core\Menu\Implementation\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
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
            $this->translator->trans('ManageDescription', [], $context),
            $this->translator->trans('Manage', [], $context),
            new FontAwesomeGlyph('sort', ['fa-fw', 'fa-2x'], null, 'fas'),
            $this->urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}