<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNode\TreeNodeTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Apply batch-actions on specific folders or items (move, delete, rights configuration)
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagerComponent extends BaseHtmlTreeComponent implements TableSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        $currentNode = $this->getCurrentTreeNode();
        if (!$this->canEditTreeNode($currentNode))
        {
            throw new NotAllowedException();
        }
        
        BreadcrumbTrail::getInstance()->add(new Breadcrumb($this->get_url(), Translation::get('ManagerComponent')));
        
        $table = new TreeNodeTable($this);
        
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
