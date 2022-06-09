<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function getActions(): Actions
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_CONFIGURE_PLATFORM
            )
        );

        $links[] = new Action(
            Translation::get('SettingsDescription'), Translation::get('Settings'),
            new FontAwesomeGlyph('cog', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_IMPORTER
            )
        );
        $links[] = new Action(
            Translation::get('ImporterDescription'), Translation::get('Importer'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS
            )
        );
        $links[] = new Action(
            Translation::get('SystemAnnouncementsDescription'), Translation::get('SystemAnnouncements'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_LANGUAGE,
                \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_IMPORT
            )
        );
        $links[] = new Action(
            Translation::get('TranslationsImportDescription'), Translation::get('TranslationsImport'),
            new FontAwesomeGlyph('language', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_LANGUAGE,
                \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_EXPORT
            )
        );
        $links[] = new Action(
            Translation::get('TranslationsExportDescription'), Translation::get('TranslationsExport'),
            new FontAwesomeGlyph('language', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_DIAGNOSE
            )
        );
        $links[] = new Action(
            Translation::get('DiagnoseDescription'), Translation::get('Diagnose'),
            new FontAwesomeGlyph('stethoscope', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_VIEW_LOGS
            )
        );
        $links[] = new Action(
            Translation::get('LogsViewerDescription'), Translation::get('LogsViewer'),
            new FontAwesomeGlyph('info-circle', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        return new Actions(\Chamilo\Core\Admin\Manager::context(), $links);
    }
}
