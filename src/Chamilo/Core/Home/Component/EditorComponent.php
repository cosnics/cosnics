<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Form\BlockForm;
use Chamilo\Core\Home\Form\ColumnForm;
use Chamilo\Core\Home\Form\RowForm;
use Chamilo\Core\Home\Form\TabForm;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: editor.class.php 227 2009-11-13 14:45:05Z kariboe $
 *
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_HOME_ID);
        $type = Request :: get(self :: PARAM_HOME_TYPE);

        $user = $this->get_user();
        $user_home_allowed = PlatformSetting :: get('allow_user_home', 'core\home');

        if ($user_home_allowed && Authentication :: is_valid())
        {
            $user_id = $user->get_id();
        }

        if ($id && $type)
        {
            $url = $this->get_url();
            switch ($type)
            {
                case self :: TYPE_BLOCK :
                    $object = DataManager :: retrieve_by_id(Block :: class_name(), $id);
                    $form = new BlockForm(BlockForm :: TYPE_EDIT, $object, $url);
                    break;
                case self :: TYPE_COLUMN :
                    $object = DataManager :: retrieve_by_id(Column :: class_name(), $id);
                    $form = new ColumnForm(ColumnForm :: TYPE_EDIT, $object, $url);
                    break;
                case self :: TYPE_ROW :
                    $object = DataManager :: retrieve_by_id(Row :: class_name(), $id);
                    $form = new RowForm(RowForm :: TYPE_EDIT, $object, $url);
                    break;
                case self :: TYPE_TAB :
                    $object = DataManager :: retrieve_by_id(Tab :: class_name(), $id);
                    $form = new TabForm(TabForm :: TYPE_EDIT, $object, $url);
                    break;
            }

            if ($object->get_user() == $user_id || (! $object->get_user() && $user->is_platform_admin()))
            {
                if ($form->validate())
                {
                    $success = $form->update_object();
                    $this->redirect($success);
                }
                else
                {
                    $html = array();

                    $html[] = $this->render_header();
                    $html[] = $form->toHtml();
                    $html[] = $this->render_footer();

                    return implode("\n", $html);
                }
            }
            else
            {
                return $this->display_error_page(
                    htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_HOME_TYPE, self :: PARAM_HOME_ID);
    }
}
