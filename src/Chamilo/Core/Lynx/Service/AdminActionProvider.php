<?php
namespace Chamilo\Core\Lynx\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;
use Symfony\Component\Translation\Translator;

class AdminActionProvider extends AbstractActionProvider implements ActionProviderInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(
        UrlGenerator $urlGenerator, Translator $translator, ConfigurationConsulter $configurationConsulter
    )
    {
        parent::__construct($urlGenerator, $translator);
        $this->configurationConsulter = $configurationConsulter;
    }

    public function getActions(): Actions
    {
        $translator = $this->getTranslator();
        $context = $this->getContext();
        $urlGenerator = $this->getUrlGenerator();

        $links = [];

        $packageManagementEnabled =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'enable_package_management']);

        if ($packageManagementEnabled == '1')
        {
            $parameters = [Application::PARAM_CONTEXT => $context];

            $links[] = new Action(
                $translator->trans('ManagePackagesDescription', [], $context),
                $translator->trans('ManagePackages', [], $context),
                new FontAwesomeGlyph('hard-hat', ['fa-fw', 'fa-2x'], null, 'fas'),
                $urlGenerator->fromParameters($parameters)
            );
        }

        return new Actions($context, $links);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Lynx';
    }
}