<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity\ActivityTable;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to list activity on a portfolio item
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityComponent extends ItemComponent implements TableSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        $activity_table = new ActivityTable($this);
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_STEP => $this->get_current_step())), 
                Translation :: get('ActivityComponent')));
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $activity_table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
    }
}
