<?php
namespace Chamilo\Core\Menu\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
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
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE
        ];

        $links[] = new Action(
            $translator->trans('ManageDescription', [], $context), $translator->trans('Manage', [], $context),
            new FontAwesomeGlyph('sort', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return Manager::CONTEXT;
    }
}