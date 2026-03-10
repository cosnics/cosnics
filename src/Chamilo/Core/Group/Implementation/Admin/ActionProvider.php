<?php
namespace Chamilo\Core\Group\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

/**
 * @package Chamilo\Core\Group\Implementation\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProvider extends AbstractActionProvider implements ActionProviderInterface
{
    public function getActions(): Actions
    {
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
        ];

        $links[] = new Action(
            $translator->trans('ListDescription', [], $context),
            $translator->trans('List', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::CREATE->value,
            Manager::PARAM_GROUP_ID => 0
        ];

        $links[] = new Action(
            $translator->trans('CreateDescription', [], $context),
            $translator->trans('Create', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            ApplicationInterface::PARAM_CONTEXT => $context,
            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}