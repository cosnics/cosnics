<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item\ItemTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Apply batch-actions on specific folders or items (move, delete, rights configuration)
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagerComponent extends Manager implements DelegateComponent, TableSupport
{

    /**
     * Executes this component
     */
    public function run()
    {
        parent :: run();

        if (! $this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        BreadcrumbTrail :: get_instance()->add(new Breadcrumb($this->get_url(), Translation :: get('ManagerComponent')));

        $table = new ItemTable($this);

        $this->get_tabs_renderer()->set_content($table->as_html());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_tabs_renderer()->render();
        $html[] = $this->render_footer();

        return implode("\n", $html);
    }

    /**
     *
     * @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
    }
}
