<?php
namespace Chamilo\Core\Lynx\Source\Component;

use Chamilo\Core\Lynx\Source\DataClass\Source;
use Chamilo\Core\Lynx\Source\Manager;
use Chamilo\Core\Lynx\Source\Table\Source\SourceTable;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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
        $source_table = new SourceTable($this);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $source_table->as_html();
        $html[] = $this->render_footer();
        
        implode(PHP_EOL, $html);
    }

    public function get_table_condition($object_table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        
        if (isset($query) && $query != '')
        {
            return new PatternMatchCondition(
                new PropertyConditionVariable(Source :: class_name(), Source :: PROPERTY_NAME), 
                '*' . $query . '*');
        }
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
