<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Admin;

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
            Translation :: get('ManageDefault'),
            Translation :: get('ManageDefaultDescription'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/build'),
            Redirect :: get_link(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Home\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\Home\Manager :: ACTION_MANAGE_HOME),
                array(),
                false,
                Redirect :: TYPE_CORE));

        return new Actions(\Chamilo\Core\Home\Manager :: context(), $links);
    }
}
