<?php
namespace Chamilo\Core\User\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Action;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\Actions;

class ActionProvider extends AbstractActionProvider implements ActionProviderInterface
{
    public function getActions(): Actions
    {
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE
        ];

        $links[] = new Action(
            $translator->trans('ListDescription', [], $context), $translator->trans('List', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_CREATE
        ];

        $links[] = new Action(
            $translator->trans('CreateDescription', [], $context),
            $translator->trans('Create', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}