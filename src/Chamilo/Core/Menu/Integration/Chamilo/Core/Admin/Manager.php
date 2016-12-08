<?php
namespace Chamilo\Core\Menu\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Integration\Chamilo\Core\Admin
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = array();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Menu\Manager::context(), 
                Application::PARAM_ACTION => \Chamilo\Core\Menu\Manager::ACTION_BROWSE));
        
        $links[] = new DynamicAction(
            Translation::get('Manage'), 
            Translation::get('ManageDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Sort'), 
            $redirect->getUrl());
        
        return new Actions(\Chamilo\Core\Menu\Manager::context(), $links);
    }
}
