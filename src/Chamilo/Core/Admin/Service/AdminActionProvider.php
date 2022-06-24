<?php
namespace Chamilo\Core\Admin\Service;

use Chamilo\Core\Admin\Manager;
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
            Application::PARAM_ACTION => Manager::ACTION_CONFIGURE_PLATFORM
        ];

        $links[] = new Action(
            $translator->trans('SettingsDescription', [], $context), $translator->trans('Settings', [], $context),
            new FontAwesomeGlyph('cog', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_SYSTEM_ANNOUNCEMENTS
        ];

        $links[] = new Action(
            $translator->trans('SystemAnnouncementsDescription', [], $context),
            $translator->trans('SystemAnnouncements', [], $context),
            new FontAwesomeGlyph('list', ['fa-fw', 'fa-2x'], null, 'fas'), $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_LANGUAGE,
            \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_IMPORT
        ];

        $links[] = new Action(
            $translator->trans('TranslationsImportDescription', [], $context),
            $translator->trans('TranslationsImport', [], $context),
            new FontAwesomeGlyph('language', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
        );

        $parameters = [
            Application::PARAM_CONTEXT => $context,
            Application::PARAM_ACTION => Manager::ACTION_LANGUAGE,
            \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_EXPORT
        ];

        $links[] = new Action(
            $translator->trans('TranslationsExportDescription', [], $context),
            $translator->trans('TranslationsExport', [], $context),
            new FontAwesomeGlyph('language', ['fa-fw', 'fa-2x'], null, 'fas'),
            $urlGenerator->fromParameters($parameters)
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

        return new Actions($context, $links);
    }

    public function getContext(): string
    {
        return 'Chamilo\Core\Admin';
    }
}