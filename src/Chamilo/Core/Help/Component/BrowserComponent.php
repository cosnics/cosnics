<?php
namespace Chamilo\Core\Help\Component;

use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Table\Item\HelpItemTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package help.lib.help_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $output = $this->get_user_html();

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<br />' . $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar(
                $this->get_url(array(Manager::PARAM_HELP_ITEM => $this->get_help_item()))
            );
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(array(Manager::PARAM_HELP_ITEM => $this->get_help_item())),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $condition = new ContainsCondition(
                new PropertyConditionVariable(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER), $query
            );
        }

        return $condition;
    }

    public function get_help_item()
    {
        return (Request::get(Manager::PARAM_HELP_ITEM) ? Request::get(Manager::PARAM_HELP_ITEM) : 0);
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */

    public function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $table = new HelpItemTable($this);

        $html = [];
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
