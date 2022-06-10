<?php
namespace Chamilo\Application\Weblcms\Request\Rights;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    public const ACTION_ACCESS = 'Accessor';
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';

    public const DEFAULT_ACTION = self::ACTION_CREATE;

    public const PARAM_ACTION = 'request_rights_action';
    public const PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID = 'location_entity_right_group_id';

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function get_tabs($current_tab, $content)
    {
        $tabs = new LinkTabsRenderer(
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(__CLASS__, true), $content
        );

        $tabs->addTab(
            new LinkTab(
                self::ACTION_CREATE, Translation::get('Add'), new FontAwesomeGlyph('plus', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)), $current_tab == self::ACTION_CREATE
            )
        );
        $tabs->addTab(
            new LinkTab(
                self::ACTION_ACCESS, Translation::get('GeneralAccess'),
                new FontAwesomeGlyph('key', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_ACCESS)), $current_tab == self::ACTION_ACCESS
            )
        );
        $tabs->addTab(
            new LinkTab(
                self::ACTION_BROWSE, Translation::get('Targets'),
                new FontAwesomeGlyph('folder', array('fa-lg'), null, 'fas'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)), $current_tab == self::ACTION_BROWSE
            )
        );

        return $tabs;
    }
}

?>