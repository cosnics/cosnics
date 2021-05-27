<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Admin;

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
        $links = [];

        $rightsContext = \Chamilo\Core\Home\Rights\Manager::context();

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => $rightsContext,
                \Chamilo\Core\Home\Manager::PARAM_ACTION => \Chamilo\Core\Home\Rights\Manager::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES
            )
        );

        $links[] = new DynamicAction(
            Translation::get('BrowseBlockTypeTargetEntitiesComponent', null, $rightsContext),
            Translation::get('BrowseBlockTypeTargetEntitiesComponentDescription', null, $rightsContext),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        return new Actions(\Chamilo\Core\Home\Manager::context(), $links);
    }
}
