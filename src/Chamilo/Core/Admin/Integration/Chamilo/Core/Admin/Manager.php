<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = array();

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_CONFIGURE_PLATFORM));

        $links[] = new DynamicAction(
            Translation :: get('Settings'),
            Translation :: get('SettingsDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/Settings'),
            $redirect->getUrl());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_IMPORTER));
        $links[] = new DynamicAction(
            Translation :: get('Importer'),
            Translation :: get('ImporterDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'),
            $redirect->getUrl());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_SYSTEM_ANNOUNCEMENTS));
        $links[] = new DynamicAction(
            Translation :: get('SystemAnnouncements'),
            Translation :: get('SystemAnnouncementsDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'),
            $redirect->getUrl());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_LANGUAGE));
        $links[] = new DynamicAction(
            Translation :: get('TranslationsImport'),
            Translation :: get('TranslationsImportDescription'),
            Theme :: getInstance()->getImagePath(\Chamilo\Core\Admin\Language\Manager :: context(), 'Logo/32'),
            $redirect->getUrl());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_DIAGNOSE));
        $links[] = new DynamicAction(
            Translation :: get('Diagnose'),
            Translation :: get('DiagnoseDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/Information'),
            $redirect->getUrl());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context(),
                \Chamilo\Core\Admin\Manager :: PARAM_ACTION => \Chamilo\Core\Admin\Manager :: ACTION_VIEW_LOGS));
        $links[] = new DynamicAction(
            Translation :: get('LogsViewer'),
            Translation :: get('LogsViewerDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/Information'),
            $redirect->getUrl());

        return new Actions(\Chamilo\Core\Admin\Manager :: context(), $links);
    }
}
