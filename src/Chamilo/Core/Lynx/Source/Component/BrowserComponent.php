<?php
namespace Chamilo\Core\Lynx\Source\Component;

use Chamilo\Core\Lynx\Source\DataClass\Source;
use Chamilo\Core\Lynx\Source\Manager;
use Chamilo\Core\Lynx\Source\Table\Source\SourceTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class BrowserComponent extends Manager implements TableSupport
{

    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->action_bar = $this->get_action_bar();
        $source_table = new SourceTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $source_table->as_html();
        $html[] = $this->render_footer();

        implode(PHP_EOL, $html);
    }

    public function get_table_condition($object_table_class_name)
    {
        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            return new PatternMatchCondition(
                new PropertyConditionVariable(Source :: class_name(), Source :: PROPERTY_NAME),
                '*' . $query . '*');
        }
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        // $action_bar->add_common_action(
        // new ToolbarItem(Translation :: get('InstallLocal'),
        // Theme :: getInstance()->getImagesPath() . 'action_install_local.png',
        // $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_LOCAL_PACKAGE)),
        // ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        // $action_bar->add_common_action(
        // new ToolbarItem(Translation :: get('InstallRemote'),
        // Theme :: getInstance()->getImagesPath() . 'action_install_remote.png',
        // $this->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_REMOTE_PACKAGE)),
        // ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
