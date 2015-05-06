<?php
namespace Chamilo\Core\MetadataOld\Element\Component;

use Chamilo\Core\MetadataOld\Element\Manager;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Table\Element\ElementTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     * The action bar of this browser
     *
     * @var ActionBarRenderer
     */
    private $action_bar;

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $this->action_bar = $this->get_action_bar();
        $table = new ElementTable($this);

        $html = array();

        $html[] = $this->action_bar->as_html();
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Create'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE))));

        return $action_bar;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        return $this->action_bar->get_conditions(
            array(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME)));
    }
}
