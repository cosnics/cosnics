<?php
namespace Chamilo\Core\Repository\UserView\Component;

use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Table\UserView\UserViewTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport, DelegateComponent
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
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $output = $this->get_user_html();
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $output;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_user_html()
    {
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $table = new UserViewTable($this);

        return $table->as_html();
    }

    /**
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView::class_name(), UserView::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(UserView::class_name(), UserView::PROPERTY_NAME), '*' . $query . '*'
            );
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(UserView::class_name(), UserView::PROPERTY_DESCRIPTION),
                '*' . $query . '*'
            );
            $or_condition = new OrCondition($or_conditions);

            $and_conditions[] = array();
            $and_conditions = $condition;
            $and_conditions = $or_condition;
            $condition = new AndCondition($and_conditions);
        }

        return $condition;
    }

    /**
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Add', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }
}
