<?php
namespace Chamilo\Core\Repository\Share\Component;

use Chamilo\Core\Repository\Share\Form\ContentObjectShareForm;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Updater component for shared content objects
 *
 * @author Pieterjan Broekaert
 * @author Sven Vanpoucke
 */
class UpdateComponent extends Manager
{

    public function run()
    {
        $ids = $this->get_content_object_ids();

        $target_users = Request :: get(self :: PARAM_TARGET_USERS);
        $target_groups = Request :: get(self :: PARAM_TARGET_GROUPS);

        if (($target_users || $target_groups))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            if ($target_users && ! is_array($target_users))
            {
                $target_users = array($target_users);
            }

            if ($target_groups && ! is_array($target_groups))
            {
                $target_groups = array($target_groups);
            }

            $share_form = ContentObjectShareForm :: factory(
                ContentObjectShareForm :: TYPE_EDIT,
                $ids,
                $this->get_user(),
                $this->get_url());

            if (count($target_users) + count($target_groups) == 1 && count($ids) == 1)
            {
                $share_form->set_default_rights($target_users, $target_groups);
            }

            if ($share_form->validate())
            {
                $succes = $share_form->update_content_object_share($target_users, $target_groups);

                $message = $succes ? Translation :: get('ObjectShared') : Translation :: get(
                    'ObjectNotShared',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES);

                $this->redirect($message, ! $succes, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $this->display_content_objects();
                $html[] = $share_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                Translation :: get(
                    'NoObjectsSelected',
                    array('OBJECTS' => Translation :: get('ContentObjects')),
                    Utilities :: COMMON_LIBRARIES));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                Translation :: get('ShareManagerBrowserComponent')));

        $breadcrumbtrail->add_help('repository_content_object_share_rights_creator');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_TARGET_GROUPS, self :: PARAM_TARGET_USERS);
    }
}
