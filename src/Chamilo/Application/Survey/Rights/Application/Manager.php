<?php
namespace Chamilo\Application\Survey\Rights\Application;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'survey_rights_action';
    const PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID = 'location_entity_right_group_id';
    const ACTION_PUBLISHER = 'Publisher';
    const DEFAULT_ACTION = self :: ACTION_PUBLISHER;

    public function get_tabs($current_tab, $content)
    {
        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(__NAMESPACE__, true),
            $content);

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_PUBLISHER,
                Translation :: get('Publisher'),
                Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'Tab/' . self :: ACTION_PUBLISHER),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PUBLISHER)),
                ($current_tab == self :: ACTION_PUBLISHER ? true : false)));

        return $tabs;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent', array(), '\Chamilo\Application\Survey')));
    }
}
