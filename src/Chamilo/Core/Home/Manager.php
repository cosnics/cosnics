<?php
namespace Chamilo\Core\Home;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: home_manager.class.php 227 2009-11-13 14:45:05Z kariboe $
 *
 * @package home.lib.home_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'home';
    const PARAM_HOME_ID = 'id';
    const PARAM_HOME_TYPE = 'type';
    const PARAM_DIRECTION = 'direction';
    const PARAM_TAB_ID = 'tab';
    const PARAM_OBJECT_ID = 'object_id';
    const PARAM_PARENT_ID = 'parent_id';
    const ACTION_VIEW_HOME = 'home';
    const ACTION_MANAGE_HOME = 'manager';
    const ACTION_EDIT_HOME = 'editor';
    const ACTION_CONFIGURE_HOME = 'configurer';
    const ACTION_EDIT_HOME_ADMIN = 'admin_editor';
    const ACTION_EDIT_HOME_PERSONAL = 'home_editor';
    const ACTION_CONFIGURE_HOME_ADMIN = 'admin_configurer';
    const ACTION_CONFIGURE_HOME_PERSONAL = 'home_configurer';
    const ACTION_TRUNCATE = 'truncater';
    const ACTION_PERSONAL = 'personal';
    const ACTION_VIEW_ATTACHMENT = 'attachment_viewer';
    const DEFAULT_ACTION = self :: ACTION_VIEW_HOME;
    const TYPE_BLOCK = 'block';
    const TYPE_COLUMN = 'column';
    const TYPE_ROW = 'row';
    const TYPE_TAB = 'tab';

    public function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
    }
}
