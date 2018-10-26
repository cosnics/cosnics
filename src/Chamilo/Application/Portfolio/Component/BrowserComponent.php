<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Table\User\UserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Portfolio browser component, used to browse for other users' portfolio
 *
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends TabComponent implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function build()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $table = new UserTable($this, $this->getUserService(), $this->getRightsService());
        $table->setSearchForm($this->buttonToolbarRenderer->getSearchForm());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $searchConditions = $this->buttonToolbarRenderer->getConditions(
            array(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE)));

        if ($searchConditions)
        {
            $conditions[] = $searchConditions;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ACTIVE),
            new StaticConditionVariable(1));

        return new AndCondition($conditions);
    }
}