<?php
namespace Chamilo\Core\Home;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package home.lib.home_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    public const ACTION_CONFIGURE_HOME = 'Configurer';
    public const ACTION_CONFIGURE_HOME_ADMIN = 'AdminConfigurer';
    public const ACTION_CONFIGURE_HOME_PERSONAL = 'HomeConfigurer';
    public const ACTION_EDIT_HOME = 'Editor';
    public const ACTION_EDIT_HOME_ADMIN = 'AdminEditor';
    public const ACTION_EDIT_HOME_PERSONAL = 'HomeEditor';
    public const ACTION_MANAGE_HOME = 'Manager';
    public const ACTION_PERSONAL = 'Personal';
    public const ACTION_TRUNCATE = 'Truncater';
    public const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    public const ACTION_VIEW_HOME = 'Home';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_VIEW_HOME;

    public const PARAM_DIRECTION = 'direction';
    public const PARAM_HOME_ID = 'id';
    public const PARAM_HOME_TYPE = 'type';
    public const PARAM_OBJECT_ID = 'object_id';
    public const PARAM_PARENT_ID = 'parent_id';
    public const PARAM_RENDERER_TYPE = 'renderer_type';
    public const PARAM_TAB_ID = 'tab';

    public const TYPE_BLOCK = 'block';
    public const TYPE_COLUMN = 'column';
    public const TYPE_ROW = 'row';
    public const TYPE_TAB = 'tab';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if ($this->getUser() instanceof User)
        {
            $this->checkAuthorization(Manager::CONTEXT);
        }
    }
}
