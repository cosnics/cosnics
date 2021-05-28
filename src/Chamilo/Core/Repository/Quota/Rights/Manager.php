<?php
namespace Chamilo\Core\Repository\Quota\Rights;

use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    const ACTION_ACCESS = 'Accessor';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';

    const DEFAULT_ACTION = self::ACTION_CREATE;

    const PARAM_ACTION = 'quota_rights_action';
    const PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID = 'location_entity_right_group_id';

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    public function getRightsService()
    {
        return $this->getService(RightsService::class);
    }

    public function get_tabs($current_tab, $content)
    {
        $tabs =
            new DynamicVisualTabsRenderer(ClassnameUtilities::getInstance()->getNamespaceId(__NAMESPACE__), $content);

        $tabs->add_tab(
            new DynamicVisualTab(
                self::ACTION_CREATE, Translation::get('Add'), new FontAwesomeGlyph('plus', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)), $current_tab == self::ACTION_CREATE
            )
        );
        $tabs->add_tab(
            new DynamicVisualTab(
                self::ACTION_ACCESS, Translation::get('GeneralAccess'),
                new FontAwesomeGlyph('key', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_ACCESS)), $current_tab == self::ACTION_ACCESS
            )
        );
        $tabs->add_tab(
            new DynamicVisualTab(
                self::ACTION_BROWSE, Translation::get('Targets'),
                new FontAwesomeGlyph('bullseye', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)), $current_tab == self::ACTION_BROWSE
            )
        );

        return $tabs;
    }
}
