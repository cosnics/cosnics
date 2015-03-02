<?php
namespace Chamilo\Core\Metadata\Integration\Chamilo\Core\Admin;

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
        $links[] = new DynamicAction(
            Translation :: get('MetadataNamespacesBrowser'),
            Translation :: get('MetadataNamespacesDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/list'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_SCHEMA),
                array(),
                false,
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('MetadataElementsBrowser'),
            Translation :: get('MetadataElementsDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/list'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_ELEMENT),
                array(),
                false,
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('MetadataAttributesBrowser'),
            Translation :: get('MetadataAttributesDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/list'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_ATTRIBUTE),
                array(),
                false,
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('ControlledVocabularyBrowser'),
            Translation :: get('ControlledVocabularyBrowserDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/list'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_CONTROLLED_VOCABULARY),
                array(),
                false,
                Redirect :: TYPE_CORE));

        $links[] = new DynamicAction(
            Translation :: get('ExportMetadaTitle'),
            Translation :: get('ExportMetadataDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/export'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_EXPORT_METADATA),
                array(),
                false,
                Redirect :: TYPE_CORE));
        $links[] = new DynamicAction(
            Translation :: get('ImportMetadaTitle'),
            Translation :: get('ImportMetadataDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/import'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_IMPORT_METADATA),
                array(),
                false,
                Redirect :: TYPE_CORE));

        return new Actions(\Chamilo\Core\Metadata\Manager :: context(), $links);
    }
}
