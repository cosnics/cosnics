<?php
namespace Chamilo\Core\Home;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

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
    // Parameters
    const PARAM_HOME_ID = 'id';
    const PARAM_HOME_TYPE = 'type';
    const PARAM_DIRECTION = 'direction';
    const PARAM_TAB_ID = 'tab';
    const PARAM_OBJECT_ID = 'object_id';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_RENDERER_TYPE = 'renderer_type';

    // Actions
    const ACTION_VIEW_HOME = 'Home';
    const ACTION_MANAGE_HOME = 'Manager';
    const ACTION_EDIT_HOME = 'Editor';
    const ACTION_CONFIGURE_HOME = 'Configurer';
    const ACTION_EDIT_HOME_ADMIN = 'AdminEditor';
    const ACTION_EDIT_HOME_PERSONAL = 'HomeEditor';
    const ACTION_CONFIGURE_HOME_ADMIN = 'AdminConfigurer';
    const ACTION_CONFIGURE_HOME_PERSONAL = 'HomeConfigurer';
    const ACTION_TRUNCATE = 'Truncater';
    const ACTION_PERSONAL = 'Personal';
    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    const DEFAULT_ACTION = self :: ACTION_VIEW_HOME;

    // Types
    const TYPE_BLOCK = 'block';
    const TYPE_COLUMN = 'column';
    const TYPE_ROW = 'row';
    const TYPE_TAB = 'tab';

    public function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
    }

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent:: __construct($applicationConfiguration);

        if($this->getUser() instanceof User)
        {
            $this->checkAuthorization(Manager::context());
        }
    }
}
