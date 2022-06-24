<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Core\Metadata\Manager;
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
            Application::PARAM_ACTION => Manager::ACTION_SCHEMA
        ];

        $links[] = new Action(
            $translator->trans('MetadataNamespacesDescription', [], $context),
            $translator->trans('MetadataNamespacesBrowser', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Metadata';
    }
}