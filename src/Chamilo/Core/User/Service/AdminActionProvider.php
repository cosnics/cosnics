<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Admin\Service\AbstractActionProvider;
use Chamilo\Core\Admin\Service\ActionProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;
use Chamilo\Libraries\Utilities\StringUtilities;
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

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_USERS
        ];

        $links[] = new Action(
            $translator->trans('ListDescription', [], $context), $translator->trans('List', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $allowRegistration = $this->getConfigurationConsulter()->getSetting([$context, 'allow_registration']);

        if ($allowRegistration == 2)
        {
            $parameters = [
                Application::PARAM_CONTEXT => $context,
                Application::PARAM_ACTION => Manager::ACTION_USER_APPROVAL_BROWSER
            ];

            $links[] = new Action(
                $translator->trans('ApproveListDescription', [], $context),
                $translator->trans('ApproveList', [], $context),
                new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'),
                $urlGenerator->fromParameters($parameters)
            );
        }

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_CREATE_USER
        ];

        $links[] = new Action(
            $translator->trans('CreateDescription', [], $context),
            $translator->trans('Create', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_EXPORT_USERS
        ];

        $links[] = new Action(
            $translator->trans('ExportDescription', [], $context),
            $translator->trans('Export', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('download', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_IMPORT_USERS
        ];

        $links[] = new Action(
            $translator->trans('ImportDescription', [], $context),
            $translator->trans('Import', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('upload', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BUILD_USER_FIELDS
        ];

        $links[] = new Action(
            $translator->trans('BuildUserFieldsDescription', [], $context),
            $translator->trans('BuildUserFields', [], $context),
            new FontAwesomeGlyph('user', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_BROWSE_USERS
        ];

        return new Actions($context, $links, $urlGenerator->fromParameters($parameters));
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\User';
    }
}