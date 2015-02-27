<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function get_actions()
    {
        $links = array();
        $links[] = new DynamicAction(
            Translation :: get('List'), 
            Translation :: get('ListDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/list.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_BROWSE_USERS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        if (PlatformSetting :: get('allow_registration', \Chamilo\Core\User\Manager :: context()) == 2)
        {
            $links[] = new DynamicAction(
                Translation :: get('ApproveList'), 
                Translation :: get('ApproveListDescription'), 
                Theme :: getInstance()->getImagesPath() . 'admin/list.png', 
                Redirect :: get_link(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                        \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_USER_APPROVAL_BROWSER), 
                    array(), 
                    false, 
                    Redirect :: TYPE_CORE));
        }
        
        $links[] = new DynamicAction(
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), 
            Translation :: get('CreateDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/add.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_CREATE_USER), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), 
            Translation :: get('ExportDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/export.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_EXPORT_USERS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES), 
            Translation :: get('ImportDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/import.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_IMPORT_USERS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('BuildUserFields'), 
            Translation :: get('BuildUserFieldsDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/build.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_BUILD_USER_FIELDS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('EditTermsConditions'), 
            Translation :: get('EditTermsConditionsDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/build.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_EDIT_TERMSCONDITIONS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        $info = new Actions(\Chamilo\Core\User\Manager :: context(), $links);
        $info->set_search(
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_BROWSE_USERS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        return $info;
    }

    public static function get_import_actions()
    {
        $links = array();
        $links[] = new DynamicAction(
            Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES), 
            Translation :: get('ImportDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/import.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(), 
                    \Chamilo\Core\User\Manager :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_IMPORT_USERS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        return $links;
    }
}
