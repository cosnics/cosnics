<?php
namespace Chamilo\Core\Admin\Table\WhoisOnline;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: whois_online_table_cell_renderer.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 *
 * @package admin.lib.admin_manager.component.whois_online_table
 */
/**
 * Cell renderer for the user object browser table
 */
class WhoisOnlineTableCellRenderer extends DataClassTableCellRenderer
{

    // Inherited
    public function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator', array(), \Chamilo\Core\User\Manager :: context());
                }
                if ($user->get_status() == '1')
                {
                    return Translation :: get('CourseAdmin', array(), \Chamilo\Core\User\Manager :: context());
                }
                else
                {
                    return Translation :: get('Student', array(), \Chamilo\Core\User\Manager :: context());
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdministrator', array(), \Chamilo\Core\User\Manager :: context());
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_PICTURE_URI :
                if ($this->get_component()->get_user()->is_platform_admin())
                {
                    $profilePhotoUrl = new Redirect(
                        array(
                            Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                            Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                            \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $user->get_id()));

                    return '<a href="' . $this->get_component()->get_url(array('uid' => $user->get_id())) . '">' .
                         '<img style="max-width: 100px; max-height: 100px;" src="' . $profilePhotoUrl->getUrl() .
                         '" alt="' . Translation :: get('UserPicture', array(), \Chamilo\Core\User\Manager :: context()) .
                         '" /></a>';
                }
                return '';
        }
        return parent :: render_cell($column, $user);
    }
}
