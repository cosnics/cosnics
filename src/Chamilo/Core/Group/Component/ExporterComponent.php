<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupExportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package group.lib.group_manager.component
 */
class ExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $form = new GroupExportForm(GroupExportForm::TYPE_EXPORT, $this->get_url());

        if ($form->validate())
        {
            $export = $form->exportValues();
            $file_type = $export['file_type'];
            $data['groups'] = $this->build_group_tree(0);
            $this->export_groups($file_type, $data['groups'][0]);
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function build_group_tree($parent_group)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($parent_group));
        $result = $this->retrieve_groups($condition);
        foreach($result as $group)
        {
            $group_array[Group::PROPERTY_NAME] = htmlspecialchars($group->get_name());
            $group_array[Group::PROPERTY_DESCRIPTION] = htmlspecialchars($group->get_description());
            $group_array['children'] = $this->build_group_tree($group->get_id());
            $data[] = $group_array;
        }

        return $data;
    }

    public function export_groups($file_type, $data)
    {
        $filename = 'export_groups_' . date('Y-m-d_H-i-s');
        if ($file_type == 'pdf')
        {
            $data = array(array('key' => 'groups', 'data' => $data));
        }
        $export = Export::factory($file_type, $data);
        $export->set_filename($filename);
        $export->send_to_browser();
    }
}
