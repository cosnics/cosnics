<?php
namespace Chamilo\Core\Admin\Implementation\Admin;

use Chamilo\Core\Admin\Architecture\Domain\AbstractActionProvider;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Format\Tabs\Actions;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Admin\Implementation\Admin
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
            Application::PARAM_ACTION => Manager::ACTION_CONFIGURE_PLATFORM
        ];

        $links[] = new Action(
            $translator->trans('SettingsDescription', [], $context), $translator->trans('Settings', [], $context),
            new FontAwesomeGlyph('cog', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_DIAGNOSE
        ];

        $links[] = new Action(
            $translator->trans('DiagnoseDescription', [], $context), $translator->trans('Diagnose', [], $context),
            new FontAwesomeGlyph('stethoscope', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_VIEW_LOGS
        ];

        $links[] = new Action(
            $translator->trans('LogsViewerDescription', [], $context), $translator->trans('LogsViewer', [], $context),
            new FontAwesomeGlyph('info-circle', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_WHOIS_ONLINE
        ];

        $links[] = new Action(
            $translator->trans('WhoisOnline', [], StringUtilities::LIBRARIES),
            $translator->trans('WhoisOnline', [], StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('user', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Admin';
    }
}