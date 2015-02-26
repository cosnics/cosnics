<?php
namespace Chamilo\Core\Help\Integration\Chamilo\Core\Admin;

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
            Translation :: get('List'),
            Translation :: get('ListDescription'),
            Theme :: getInstance()->getImagePath() . 'admin/list.png',
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Help\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Help\Manager :: ACTION_BROWSE_HELP_ITEMS),
                array(),
                false,
                Redirect :: TYPE_CORE));

        return new Actions(\Chamilo\Core\Help\Manager :: context(), $links);
    }
}
