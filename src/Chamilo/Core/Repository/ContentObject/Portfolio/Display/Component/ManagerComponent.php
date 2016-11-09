<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item\ItemTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
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
class ManagerComponent extends ItemComponent implements TableSupport
{

    /**
     * Executes this component
     */
    public function build()
    {

        if (! $this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        BreadcrumbTrail :: getInstance()->add(new Breadcrumb($this->get_url(), Translation :: get('ManagerComponent')));

        $table = new ItemTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
    }
}
