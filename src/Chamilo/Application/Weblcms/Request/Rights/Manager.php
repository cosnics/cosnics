<?php
namespace Chamilo\Application\Weblcms\Request\Rights;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'request_rights_action';
    const PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID = 'location_entity_right_group_id';
    const ACTION_CREATE = 'Creator';
    const ACTION_ACCESS = 'Accessor';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const DEFAULT_ACTION = self :: ACTION_CREATE;

    function get_tabs($current_tab, $content)
    {
        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities:: getInstance()->getClassNameFromNamespace(__CLASS__, true),
            $content
        );

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_CREATE,
                Translation:: get('Add'),
                Theme:: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_CREATE),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)),
                ($current_tab == self :: ACTION_CREATE ? true : false)
            )
        );
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_ACCESS,
                Translation:: get('GeneralAccess'),
                Theme:: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_ACCESS),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ACCESS)),
                ($current_tab == self :: ACTION_ACCESS ? true : false)
            )
        );
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_BROWSE,
                Translation:: get('Targets'),
                Theme:: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_BROWSE),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                ($current_tab == self :: ACTION_BROWSE ? true : false)
            )
        );

        return $tabs;
    }
}

?>