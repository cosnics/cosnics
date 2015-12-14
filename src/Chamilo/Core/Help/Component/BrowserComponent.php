<?php
namespace Chamilo\Core\Help\Component;

use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Table\Item\HelpItemTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: browser.class.php 226 2009-11-13 14:44:03Z chellee $
 *
 * @package help.lib.help_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class BrowserComponent extends Manager implements TableSupport
{

    private $ab;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->ab = $this->get_action_bar();
        $output = $this->get_user_html();

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<br />' . $this->ab->as_html() . '<br />';
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->ab->get_query();

        $table = new HelpItemTable($this);

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    public function get_help_item()
    {
        return (Request :: get(Manager :: PARAM_HELP_ITEM) ? Request :: get(Manager :: PARAM_HELP_ITEM) : 0);
    }

    public function get_condition()
    {
        $query = $this->ab->get_query();
        if (isset($query) && $query != '')
        {
            $condition = new PatternMatchCondition(
                new PropertyConditionVariable(HelpItem :: class_name(), HelpItem :: PROPERTY_NAME),
                '*' . $query . '*');
        }

        return $condition;
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(Manager :: PARAM_HELP_ITEM => $this->get_help_item())));
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(Manager :: PARAM_HELP_ITEM => $this->get_help_item())),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
