<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\PriviligeForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class PrivilegesViewerComponent extends Manager
{

    private $repository;

    public function run()
    {
        $id = Request :: get(Manager :: PARAM_EXTERNAL_REPOSITORY_ID);
        if ($id)
        {
            $this->repository = $this->retrieve_external_repository_object($id);

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $privilege_form = new PriviligeForm($this->get_url($parameters), $this);

            if ($privilege_form->validate())
            {
                $success = $privilege_form->grant_privilege();
                $message = $success ? Translation :: get('GrantPrivilegeCreated') : Translation :: get(
                    'GrantPrivilegeNotCreated');

                $this->redirect($message, ! $success, $parameters);
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = '<h3>' . $this->repository->get_title() . '</h3>';
            $html[] = $this->get_action_bar($id)->as_html();
            $html[] = $privilege_form->toHtml();

            $privileges = $this->repository->get_privileges();
            $privileges_groups = $this->repository->get_groups_privileges();

            if (count($privileges) > 0 || count($privileges_groups) > 0)
            {
                if (count($privileges) > 0)
                {
                    $data = array();

                    foreach ($privileges as $privilege)
                    {
                        $toolbar_item = new ToolbarItem(
                            Translation :: get('Delete'),
                            Theme :: getInstance()->getCommonImagePath() . 'action_delete.png',
                            $this->get_external_repository_privilege_revoking_url($id, $privilege->get_username()),
                            ToolbarItem :: DISPLAY_ICON);

                        $row = array();

                        $row[] = $privilege->get_username();
                        $row[] = $privilege->get_privilege();
                        $row[] = $privilege->get_first_name();
                        $row[] = $privilege->get_last_name();
                        $row[] = $toolbar_item->as_html();

                        $data[] = $row;
                    }

                    $table = new SortableTableFromArray($data);
                    $table->set_header(0, Translation :: get('Username'));
                    $table->set_header(1, Translation :: get('Privilege'));
                    $table->set_header(2, Translation :: get('FirstName'));
                    $table->set_header(3, Translation :: get('LastName'));
                    $table->set_header(4, '');

                    $html[] = $table->as_html();
                }

                if (count($privileges_groups) > 0)
                {
                    $data = array();

                    foreach ($privileges_groups as $privilege)
                    {
                        $toolbar_item = new ToolbarItem(
                            Translation :: get('Delete'),
                            Theme :: getInstance()->getCommonImagePath() . 'action_delete.png',
                            $this->get_external_repository_group_privilege_revoking_url(
                                $id,
                                $privilege->get_owner_username() . '/' . $privilege->get_group()),
                            ToolbarItem :: DISPLAY_ICON);

                        $row = array();

                        $row[] = $privilege->get_name();
                        $row[] = $privilege->get_privilege();
                        $row[] = $toolbar_item->as_html();

                        $data[] = $row;
                    }

                    $table = new SortableTableFromArray($data);
                    $table->set_header(0, Translation :: get('Group'));
                    $table->set_header(1, Translation :: get('Privilege'));
                    $table->set_header(2, '');

                    $html[] = $table->as_html();
                }
            }
            else
            {
                $html[] = $this->display_warning_message(Translation :: get('NoPrivileges'));
            }
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_repository()
    {
        return $this->repository;
    }

    public function get_action_bar($id)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('RevokeAll'),
                Theme :: getInstance()->getImagePath() . 'action_revoke.png',
                $this->get_external_repository_privilege_revoking_url($id),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
