<?php
namespace Chamilo\Core\Menu;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Menu\Menu\ItemMenu;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    const PARAM_DIRECTION = 'direction';
    const PARAM_ITEM = 'item';
    const PARAM_TYPE = 'type';
    const PARAM_PARENT = 'parent';
    const ACTION_CREATE = 'Creator';
    const ACTION_BROWSE = 'Browser';
    const ACTION_EDIT = 'Editor';
    const ACTION_DELETE = 'Deleter';
    const ACTION_MOVE = 'Mover';
    const ACTION_RIGHTS = 'Rights';
    const PARAM_DIRECTION_UP = 'up';
    const PARAM_DIRECTION_DOWN = 'down';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public function get_item_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADD));
    }

    public function get_item_editing_url($navigation_item)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_EDIT, self :: PARAM_ITEM => $navigation_item->get_id()));
    }

    public function get_item_rights_url($navigation_item)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_RIGHTS, self :: PARAM_ITEM => $navigation_item->get_id()));
    }

    public function get_item_deleting_url($navigation_item)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_ITEM => $navigation_item->get_id()));
    }

    public function get_item_moving_url($navigation_item, $direction)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE,
                self :: PARAM_ITEM => $navigation_item->get_id(),
                self :: PARAM_DIRECTION => $direction));
    }

    public function get_menu()
    {
        if (! isset($this->menu))
        {
            $temp_replacement = '__ITEM__';
            $url_format = $this->get_url(
                array(
                    Application :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                    Manager :: PARAM_PARENT => $temp_replacement));
            $this->menu = new ItemMenu(Request :: get(self :: PARAM_PARENT), $url_format);
        }
        return $this->menu;
    }

    public function get_menu_home_url()
    {
        return $this->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_BROWSE));
    }

    public function check_allowed()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $setting = Configuration :: get_instance()->get_setting(array('Chamilo\Core\Menu', 'enable_rights'));

        if ($setting != 1)
        {
            throw new NotAllowedException();
        }
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
