<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function get_actions()
    {
        $info = new Actions(\Chamilo\Core\Repository\Manager :: context());
        
        $links[] = new DynamicAction(
            Translation :: get('ManageExternalInstances'), 
            Translation :: get('ManageExternalInstancesDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/external_instance.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Instance\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Instance\Manager :: ACTION_BROWSE), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        $links[] = new DynamicAction(
            Translation :: get(
                'ContentObjectRelMetadataElementName', 
                null, 
                'core\repository\integration\core\metadata\linker\type'), 
            Translation :: get(
                'ContentObjectRelMetadataElementDescription', 
                null, 
                'core\repository\integration\core\metadata\linker\type'), 
            Theme :: getInstance()->getImagesPath() . 'admin/import.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_LINK_CONTENT_OBJECT_METADATA_ELEMENT), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        $links[] = new DynamicAction(
            Translation :: get(
                'ContentObjectPropertyRelMetadataElementName', 
                null, 
                'core\repository\integration\core\metadata\linker\property'), 
            Translation :: get(
                'ContentObjectPropertyRelMetadataElementDescription', 
                null, 
                'core\repository\integration\core\metadata\linker\property'), 
            Theme :: getInstance()->getImagesPath() . 'admin/import.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_LINK_CONTENT_OBJECT_PROPERTY_METADATA), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        $info->set_search(
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_BROWSE_CONTENT_OBJECTS), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        $info->set_links($links);
        
        return $info;
    }

    public static function get_import_actions()
    {
        $links = array();
        $links[] = new DynamicAction(
            Translation :: get('ImportTemplate'), 
            Translation :: get('ImportTemplateDescription'), 
            Theme :: getInstance()->getImagesPath() . 'admin/import.png', 
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_IMPORT_TEMPLATE), 
                array(), 
                false, 
                Redirect :: TYPE_CORE));
        
        return $links;
    }
}
