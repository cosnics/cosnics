<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = array();

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_CONFIGURE_PLATFORM
            )
        );

        $links[] = new DynamicAction(
            Translation::get('Settings'), Translation::get('SettingsDescription'),
            new FontAwesomeGlyph('cog', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_IMPORTER
            )
        );
        $links[] = new DynamicAction(
            Translation::get('Importer'), Translation::get('ImporterDescription'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS
            )
        );
        $links[] = new DynamicAction(
            Translation::get('SystemAnnouncements'), Translation::get('SystemAnnouncementsDescription'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_LANGUAGE,
                \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_IMPORT
            )
        );
        $links[] = new DynamicAction(
            Translation::get('TranslationsImport'), Translation::get('TranslationsImportDescription'),
            new FontAwesomeGlyph('language', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_LANGUAGE,
                \Chamilo\Core\Admin\Language\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Language\Manager::ACTION_EXPORT
            )
        );
        $links[] = new DynamicAction(
            Translation::get('TranslationsExport'), Translation::get('TranslationsExportDescription'),
            new FontAwesomeGlyph('language', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_DIAGNOSE
            )
        );
        $links[] = new DynamicAction(
            Translation::get('Diagnose'), Translation::get('DiagnoseDescription'),
            new FontAwesomeGlyph('stethoscope', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_VIEW_LOGS
            )
        );
        $links[] = new DynamicAction(
            Translation::get('LogsViewer'), Translation::get('LogsViewerDescription'),
            new FontAwesomeGlyph('info-circle', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        return new Actions(\Chamilo\Core\Admin\Manager::context(), $links);
    }
}
