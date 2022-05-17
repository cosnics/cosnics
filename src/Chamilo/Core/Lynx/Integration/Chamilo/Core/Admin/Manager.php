<?php
namespace Chamilo\Core\Lynx\Integration\Chamilo\Core\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function getActions(): Actions
    {
        $links = [];

        $package_management = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'enable_package_management')
        );

        if ($package_management == '1')
        {
            $redirect = new Redirect(array(Application::PARAM_CONTEXT => \Chamilo\Core\Lynx\Manager::context()));

            $links[] = new DynamicAction(
                Translation::get('ManagePackages'), Translation::get('ManagePackagesDescription'),
                new FontAwesomeGlyph('hard-hat', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
            );
        }

        return new Actions(\Chamilo\Core\Lynx\Manager::context(), $links);
    }
}
