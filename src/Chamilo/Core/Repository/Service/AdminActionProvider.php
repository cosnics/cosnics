<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;

class AdminActionProvider extends AbstractActionProvider implements ActionProviderInterface
{

    public function getActions(): Actions
    {
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_LINK_SCHEMAS
        ];

        $links[] = new Action(
            $translator->trans('LinkSchemasDescription', [], $context), $translator->trans('LinkSchemas', [], $context),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_LINK_PROVIDERS
        ];

        $links[] = new Action(
            $translator->trans('LinkProvidersDescription', [], $context),
            $translator->trans('LinkProviders', [], $context),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_CONTENT_OBJECTS
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Repository';
    }

}