<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: editor.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package group.lib.group_manager.component
 */
class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = $this->get_parameter(self :: PARAM_GROUP_ID);

        if ($id)
        {
            $group = $this->retrieve_group($id);

            if (! $this->get_user()->is_platform_admin())
            {
                throw new NotAllowedException();
            }

            $form = new GroupForm(
                GroupForm :: TYPE_EDIT,
                $group,
                $this->get_url(array(self :: PARAM_GROUP_ID => $id)),
                $this->get_user());

            if ($form->validate())
            {
                $success = $form->update_group();
                $group = $form->get_group();
                $message = $success ? Translation :: get(
                    'ObjectUpdated',
                    array('OBJECT' => Translation :: get('Group')),
                    Utilities :: COMMON_LIBRARIES) : Translation :: get(
                    'ObjectNotUpdated',
                    array('OBJECT' => Translation :: get('Group')),
                    Utilities :: COMMON_LIBRARIES);

                $this->redirect(
                    $message,
                    ($success ? false : true),
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_VIEW_GROUP,
                        self :: PARAM_GROUP_ID => $group->get_id()));
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS)),
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_VIEW_GROUP,
                        self :: PARAM_GROUP_ID => Request :: get(self :: PARAM_GROUP_ID))),
                Translation :: get('ViewerComponent')));
        $breadcrumbtrail->add_help('group general');
    }

//     public function get_additional_parameters()
//     {
//         return array(self :: PARAM_GROUP_ID);
//     }
}
