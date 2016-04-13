<?php
namespace Chamilo\Application\Portfolio\Component;

use Chamilo\Application\Portfolio\Table\User\UserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
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
class BrowserComponent extends \Chamilo\Application\Portfolio\Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $table = new UserTable($this);
        
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
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation :: get('GoBackHome'), 
                    Theme :: getInstance()->getCommonImagePath('Action/Home'), 
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_HOME)), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            
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
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME), 
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME), 
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE)));
        
        if ($searchConditions)
        {
            $conditions[] = $searchConditions;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ACTIVE), 
            new StaticConditionVariable(1));
        
        return new AndCondition($conditions);
    }
}