<?php
namespace Chamilo\Core\Lynx\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = array();

        $package_management = PlatformSetting :: get('enable_package_management');

        if ($package_management == '1')
        {
            $redirect = new Redirect(array(Application :: PARAM_CONTEXT => \Chamilo\Core\Lynx\Manager :: context()));

            $links[] = new DynamicAction(
                Translation :: get('ManagePackages'),
                Translation :: get('ManagePackagesDescription'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Admin/Build'),
                $redirect->getUrl());
        }

        return new Actions(\Chamilo\Core\Lynx\Manager :: context(), $links);
    }
}
