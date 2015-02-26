<?php
namespace Chamilo\Core\Metadata\Value\Element\Component;

use Chamilo\Core\Metadata\Value\Element\Manager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Core\Metadata\Value\Element\Table\Value\ValueTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
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

        return implode("\n", $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $html = array();

        $this->action_bar = $this->get_action_bar();
        $html[] = $this->action_bar->as_html();

        $table = new ValueTable($this);
        $html[] = $table->as_html();

        return implode("\n", $html);
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
                Theme :: getInstance()->getCommonImagePath() . 'action_create.png',
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
        $conditions = array();

        if ($this->action_bar->get_query())
        {
            $conditions[] = $this->action_bar->get_conditions(
                array(
                    new PropertyConditionVariable(
                        DefaultElementValue :: class_name(),
                        DefaultElementValue :: PROPERTY_VALUE)));
        }

        $element_id = Request :: get(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(),
                DefaultElementValue :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element_id));

        return new AndCondition($conditions);
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID);
    }
}
