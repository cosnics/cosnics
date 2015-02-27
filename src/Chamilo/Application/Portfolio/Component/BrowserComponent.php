<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Table\User\UserTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Portfolio browser component, used to browse for other users' portfolio
 *
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends \Chamilo\Application\Portfolio\Manager implements TableSupport
{

    /**
     * The action bar of this browser
     *
     * @var ActionBarRenderer
     */
    private $action_bar;

    public function run()
    {
        $this->action_bar = $this->get_action_bar();
        $table = new UserTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

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

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('GoBackHome'),
                Theme :: getInstance()->getCommonImagePath() . 'action_home.png',
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_HOME)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->action_bar->get_conditions(
            array(
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_LASTNAME),
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_FIRSTNAME)));
    }
}